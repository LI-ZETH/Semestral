<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Roles;
use App\Core\ValidationException;
use App\Interfaces\PerfilRepositoryInterface;
use App\Services\Auth\PasswordPolicy;
use App\Services\Crypto\PasswordHasherService;

final class PerfilService
{
    public function __construct(
        private readonly PerfilRepositoryInterface $repository,
        private readonly PasswordHasherService $passwordHasher,
        private readonly PasswordPolicy $passwordPolicy
    ) {
    }

    public function getProfile(int $userId): ?array
    {
        $profile = $this->repository->findByUserId($userId);

        if ($profile === null) {
            return null;
        }

        $collaboratorId = (int) (
            $profile['idColaborador'] ?? 0
        );

        $profile['locationHistory'] = $collaboratorId > 0
            ? $this->repository->getLocationHistory(
                $collaboratorId
            )
            : [];

        return $profile;
    }

    public function listLocations(): array
    {
        return $this->repository->listActiveLocations();
    }

    public function updateProfile(
        int $userId,
        array $input
    ): array {
        $current = $this->repository->findByUserId($userId);

        if ($current === null) {
            throw new ValidationException([
                'general' => 'No fue posible localizar tu perfil.',
            ]);
        }

        $data = $this->normalizeProfileInput($input);
        $errors = $this->validateProfile(
            $data,
            (string) $current['nombreRol']
        );

        if (
            $data['correo'] !== ''
            && $this->repository->emailExistsForAnotherUser(
                $userId,
                $data['correo']
            )
        ) {
            $errors['correo'] =
                'El correo ya está registrado por otra cuenta.';
        }

        if (
            $data['cedula'] !== ''
            && $this->repository
                ->identificationExistsForAnotherUser(
                    $userId,
                    $data['cedula']
                )
        ) {
            $errors['cedula'] =
                'La identificación ya está registrada.';
        }

        $isCollaborator = (
            (string) $current['nombreRol']
            === Roles::COLABORADOR
        );

        if ($isCollaborator) {
            $collaboratorId = (int) (
                $current['idColaborador'] ?? 0
            );

            if ($collaboratorId <= 0) {
                $errors['general'] =
                    'La cuenta no tiene un perfil de colaborador asociado.';
            }

            if ($data['cedula'] === '') {
                $errors['cedula'] =
                    'La identificación es obligatoria para colaboradores.';
            }

            if ($data['idUbicacion'] > 0) {
                $location = $this->repository
                    ->findActiveLocationById(
                        $data['idUbicacion']
                    );

                if ($location === null) {
                    $errors['idUbicacion'] =
                        'Selecciona una ubicación activa.';
                }
            }
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $this->repository->updateProfile(
            $userId,
            [
                'cedula' => $data['cedula'] === ''
                    ? null
                    : $data['cedula'],
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'correo' => $data['correo'],
                'idColaborador' => $isCollaborator
                    ? (int) $current['idColaborador']
                    : null,
                'telefono' => $data['telefono'] === ''
                    ? null
                    : $data['telefono'],
                'cargo' => $data['cargo'] === ''
                    ? null
                    : $data['cargo'],
                'departamento' => $data['departamento'] === ''
                    ? null
                    : $data['departamento'],
                'idUbicacion' => $data['idUbicacion'] > 0
                    ? $data['idUbicacion']
                    : null,
                'observacionesUbicacion' =>
                    $data['observacionesUbicacion'] === ''
                        ? null
                        : $data['observacionesUbicacion'],
            ]
        );

        $updated = $this->repository->findByUserId($userId);

        if ($updated === null) {
            throw new ValidationException([
                'general' =>
                    'El perfil se actualizó, pero no fue posible recargarlo.',
            ]);
        }

        return $updated;
    }

    public function changePassword(
        int $userId,
        array $input
    ): void {
        $currentPassword = (string) (
            $input['contrasenaActual'] ?? ''
        );
        $newPassword = (string) (
            $input['contrasenaNueva'] ?? ''
        );
        $confirmation = (string) (
            $input['confirmarContrasena'] ?? ''
        );

        $errors = [];
        $storedHash = $this->repository->findPasswordHash(
            $userId
        );

        if (
            $storedHash === null
            || !$this->passwordHasher->verificar(
                $currentPassword,
                $storedHash
            )
        ) {
            $errors['contrasenaActual'] =
                'La contraseña actual no es correcta.';
        }

        foreach (
            $this->passwordPolicy->validate($newPassword)
            as $passwordError
        ) {
            $errors['contrasenaNueva'][] = $passwordError;
        }

        if ($newPassword !== $confirmation) {
            $errors['confirmarContrasena'] =
                'La confirmación no coincide con la nueva contraseña.';
        }

        if (
            $storedHash !== null
            && $newPassword !== ''
            && $this->passwordHasher->verificar(
                $newPassword,
                $storedHash
            )
        ) {
            $errors['contrasenaNueva'][] =
                'La nueva contraseña debe ser diferente de la actual.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $newHash = $this->passwordHasher->transformar(
            $newPassword
        );

        $this->repository->updatePassword(
            $userId,
            $newHash
        );
    }

    private function normalizeProfileInput(
        array $input
    ): array {
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
            'correo' => mb_strtolower(
                trim((string) ($input['correo'] ?? ''))
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
            'idUbicacion' => (int) (
                $input['idUbicacion'] ?? 0
            ),
            'observacionesUbicacion' => trim(
                (string) (
                    $input['observacionesUbicacion']
                    ?? ''
                )
            ),
        ];
    }

    private function validateProfile(
        array $data,
        string $role
    ): array {
        $errors = [];

        $nameLength = mb_strlen($data['nombre']);
        if ($nameLength < 2 || $nameLength > 60) {
            $errors['nombre'] =
                'El nombre debe contener entre 2 y 60 caracteres.';
        }

        $lastNameLength = mb_strlen($data['apellido']);
        if ($lastNameLength < 2 || $lastNameLength > 60) {
            $errors['apellido'] =
                'El apellido debe contener entre 2 y 60 caracteres.';
        }

        if (
            $data['cedula'] !== ''
            && mb_strlen($data['cedula']) > 25
        ) {
            $errors['cedula'] =
                'La identificación no puede superar 25 caracteres.';
        }

        if (
            $data['correo'] === ''
            || filter_var(
                $data['correo'],
                FILTER_VALIDATE_EMAIL
            ) === false
            || mb_strlen($data['correo']) > 120
        ) {
            $errors['correo'] =
                'Ingresa un correo electrónico válido.';
        }

        if (
            $data['telefono'] !== ''
            && mb_strlen($data['telefono']) > 25
        ) {
            $errors['telefono'] =
                'El teléfono no puede superar 25 caracteres.';
        }

        if (
            $data['cargo'] !== ''
            && mb_strlen($data['cargo']) > 100
        ) {
            $errors['cargo'] =
                'El cargo no puede superar 100 caracteres.';
        }

        if (
            $data['departamento'] !== ''
            && mb_strlen($data['departamento']) > 100
        ) {
            $errors['departamento'] =
                'El departamento no puede superar 100 caracteres.';
        }

        if (
            $data['observacionesUbicacion'] !== ''
            && mb_strlen(
                $data['observacionesUbicacion']
            ) > 255
        ) {
            $errors['observacionesUbicacion'] =
                'La observación no puede superar 255 caracteres.';
        }

        if (
            $role !== Roles::COLABORADOR
            && (
                $data['telefono'] !== ''
                || $data['cargo'] !== ''
                || $data['departamento'] !== ''
                || $data['idUbicacion'] > 0
            )
        ) {
            // Estos campos simplemente no se persisten para otros roles.
        }

        return $errors;
    }
}
