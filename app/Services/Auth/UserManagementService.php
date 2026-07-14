<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Core\Roles;
use App\Core\ValidationException;
use App\Interfaces\UsuarioManagementRepositoryInterface;
use App\Services\Crypto\PasswordHasherService;
use DateTimeImmutable;

final class UserManagementService
{
    public function __construct(
        private readonly UsuarioManagementRepositoryInterface $repository,
        private readonly PasswordHasherService $passwordHasher,
        private readonly PasswordPolicy $passwordPolicy
    ) {
    }

    public function listUsers(array $filters = []): array
    {
        return $this->repository->listUsers($filters);
    }

    public function listRoles(): array
    {
        return $this->repository->listActiveRoles();
    }

    public function findUser(int $userId): ?array
    {
        return $this->repository->findById($userId);
    }

    public function create(array $input): int
    {
        $data = $this->normalize($input);
        $errors = $this->validate($data, true);

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $conflicts = $this->repository
            ->findConflictsExcluding(
                0,
                $data['cedula'],
                $data['usuario'],
                $data['correo']
            );

        if ($conflicts !== []) {
            throw new ValidationException($conflicts);
        }

        $passwordHash = $this->passwordHasher
            ->transformar($data['password']);

        return $this->repository->createUser(
            [
                'cedula' => $data['cedula'],
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'usuario' => $data['usuario'],
                'correo' => $data['correo'],
                'nombreRol' => $data['nombreRol'],
                'passwordHash' => $passwordHash,
            ],
            $this->buildCollaboratorData($data)
        );
    }

    public function update(
        int $userId,
        int $actorUserId,
        array $input
    ): void {
        $currentUser = $this->repository->findById($userId);

        if ($currentUser === null) {
            throw new ValidationException([
                'general' => 'El usuario solicitado no existe.',
            ]);
        }

        $data = $this->normalize($input);
        $errors = $this->validate($data, false);

        if (
            $userId === $actorUserId
            && $currentUser['nombreRol']
                === Roles::ADMINISTRADOR
            && $data['nombreRol']
                !== Roles::ADMINISTRADOR
        ) {
            $errors['nombreRol'] =
                'No puedes quitarte el rol de administrador.';
        }

        if (
            $currentUser['nombreRol']
                === Roles::ADMINISTRADOR
            && $data['nombreRol']
                !== Roles::ADMINISTRADOR
            && $this->repository
                ->countActiveAdministrators() <= 1
        ) {
            $errors['nombreRol'] =
                'No se puede cambiar el rol del último '
                . 'administrador activo.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $conflicts = $this->repository
            ->findConflictsExcluding(
                $userId,
                $data['cedula'],
                $data['usuario'],
                $data['correo']
            );

        if ($conflicts !== []) {
            throw new ValidationException($conflicts);
        }

        $passwordHash = null;

        if ($data['password'] !== '') {
            $passwordHash = $this->passwordHasher
                ->transformar($data['password']);
        }

        $this->repository->updateUser(
            $userId,
            [
                'cedula' => $data['cedula'],
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'usuario' => $data['usuario'],
                'correo' => $data['correo'],
                'nombreRol' => $data['nombreRol'],
                'passwordHash' => $passwordHash,
            ],
            $this->buildCollaboratorData($data)
        );
    }

    public function changeActiveState(
        int $userId,
        int $actorUserId,
        bool $active
    ): void {
        $targetUser = $this->repository->findById($userId);

        if ($targetUser === null) {
            throw new ValidationException([
                'general' => 'El usuario solicitado no existe.',
            ]);
        }

        if (!$active && $userId === $actorUserId) {
            throw new ValidationException([
                'general' =>
                    'No puedes desactivar tu propia cuenta.',
            ]);
        }

        if (
            !$active
            && $targetUser['nombreRol']
                === Roles::ADMINISTRADOR
            && $this->repository
                ->countActiveAdministrators() <= 1
        ) {
            throw new ValidationException([
                'general' =>
                    'No se puede desactivar el último '
                    . 'administrador activo.',
            ]);
        }

        $this->repository->setActiveState(
            $userId,
            $active
        );
    }

    public function unlock(int $userId): void
    {
        $user = $this->repository->findById($userId);

        if ($user === null) {
            throw new ValidationException([
                'general' => 'El usuario solicitado no existe.',
            ]);
        }

        $this->repository->unlock($userId);
    }

    private function normalize(array $input): array
    {
        return [
            'cedula' => trim(
                (string) ($input['cedula'] ?? '')
            ),
            'nombre' => trim(
                (string) ($input['nombre'] ?? '')
            ),
            'apellido' => trim(
                (string) ($input['apellido'] ?? '')
            ),
            'usuario' => strtolower(trim(
                (string) ($input['usuario'] ?? '')
            )),
            'correo' => strtolower(trim(
                (string) ($input['correo'] ?? '')
            )),
            'nombreRol' => trim(
                (string) ($input['nombreRol'] ?? '')
            ),
            'password' => (string) (
                $input['password'] ?? ''
            ),
            'passwordConfirmation' => (string) (
                $input['password_confirmation'] ?? ''
            ),
            'telefono' => trim(
                (string) ($input['telefono'] ?? '')
            ),
            'cargo' => trim(
                (string) ($input['cargo'] ?? '')
            ),
            'departamento' => trim(
                (string) ($input['departamento'] ?? '')
            ),
            'fechaIngreso' => trim(
                (string) ($input['fechaIngreso'] ?? '')
            ),
        ];
    }

    private function validate(
        array $data,
        bool $passwordRequired
    ): array {
        $errors = [];

        if (
            !preg_match(
                '/^[A-Za-z0-9-]{4,25}$/',
                $data['cedula']
            )
        ) {
            $errors['cedula'] =
                'Introduce una cédula válida.';
        }

        if (
            !preg_match(
                "/^[\p{L}\s'-]{2,60}$/u",
                $data['nombre']
            )
        ) {
            $errors['nombre'] =
                'Introduce un nombre válido.';
        }

        if (
            !preg_match(
                "/^[\p{L}\s'-]{2,60}$/u",
                $data['apellido']
            )
        ) {
            $errors['apellido'] =
                'Introduce un apellido válido.';
        }

        if (
            !preg_match(
                '/^[a-z0-9._-]{4,40}$/',
                $data['usuario']
            )
        ) {
            $errors['usuario'] =
                'El usuario debe tener entre 4 y 40 '
                . 'caracteres válidos.';
        }

        if (
            filter_var(
                $data['correo'],
                FILTER_VALIDATE_EMAIL
            ) === false
        ) {
            $errors['correo'] =
                'Introduce un correo válido.';
        }

        if (!Roles::isValid($data['nombreRol'])) {
            $errors['nombreRol'] =
                'Selecciona un rol válido.';
        }

        if (
            $passwordRequired
            || $data['password'] !== ''
        ) {
            $passwordErrors = $this->passwordPolicy
                ->validate($data['password']);

            if ($passwordErrors !== []) {
                $errors['password'] = implode(
                    ' ',
                    $passwordErrors
                );
            }

            if (
                $data['password']
                !== $data['passwordConfirmation']
            ) {
                $errors['password_confirmation'] =
                    'Las contraseñas no coinciden.';
            }
        }

        if (
            $data['telefono'] !== ''
            && !preg_match(
                '/^[0-9+\-\s()]{7,25}$/',
                $data['telefono']
            )
        ) {
            $errors['telefono'] =
                'Introduce un teléfono válido.';
        }

        if (
            $data['fechaIngreso'] !== ''
            && !$this->isValidDate(
                $data['fechaIngreso']
            )
        ) {
            $errors['fechaIngreso'] =
                'Introduce una fecha válida.';
        }

        return $errors;
    }

    private function buildCollaboratorData(
        array $data
    ): ?array {
        if ($data['nombreRol'] !== Roles::COLABORADOR) {
            return null;
        }

        return [
            'telefono' => $data['telefono'] !== ''
                ? $data['telefono']
                : null,

            'cargo' => $data['cargo'] !== ''
                ? $data['cargo']
                : null,

            'departamento' => $data['departamento'] !== ''
                ? $data['departamento']
                : null,

            'fechaIngreso' => $data['fechaIngreso'] !== ''
                ? $data['fechaIngreso']
                : null,
        ];
    }

    private function isValidDate(string $date): bool
    {
        $parsed = DateTimeImmutable::createFromFormat(
            'Y-m-d',
            $date
        );

        return $parsed !== false
            && $parsed->format('Y-m-d') === $date;
    }
}