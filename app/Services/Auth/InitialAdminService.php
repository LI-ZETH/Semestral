<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Core\ValidationException;
use App\Interfaces\UsuarioRepositoryInterface;
use App\Services\Crypto\PasswordHasherService;
use App\Services\Crypto\RsaSignatureService;

final class InitialAdminService
{
    public function __construct(
        private readonly UsuarioRepositoryInterface $userRepository,
        private readonly PasswordHasherService $passwordHasher,
        private readonly PasswordPolicy $passwordPolicy,
        private readonly RsaSignatureService $rsaService
    ) {
    }

    public function register(array $input): int
    {
        if ($this->userRepository->administratorExists()) {
            throw new ValidationException([
                'general' =>
                    'El administrador inicial ya fue registrado.',
            ]);
        }

        $data = $this->normalize($input);
        $errors = $this->validate($data);

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $conflicts = $this->userRepository->findConflicts(
            $data['cedula'],
            $data['usuario'],
            $data['correo']
        );

        if ($conflicts !== []) {
            throw new ValidationException($conflicts);
        }

        $passwordHash = $this->passwordHasher->transformar(
            $data['password']
        );

        return $this->userRepository->createAdministrator(
            [
                'cedula' => $data['cedula'],
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'usuario' => $data['usuario'],
                'correo' => $data['correo'],
                'passwordHash' => $passwordHash,
            ],
            $this->rsaService->getPublicKeyContent(),
            $this->rsaService->getPublicKeyFingerprint()
        );
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

            'password' => (string) (
                $input['password'] ?? ''
            ),

            'passwordConfirmation' => (string) (
                $input['password_confirmation'] ?? ''
            ),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (
            !preg_match(
                '/^[A-Za-z0-9-]{5,25}$/',
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
                'El usuario debe contener entre 4 y 40 caracteres.';
        }

        if (
            !filter_var(
                $data['correo'],
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $errors['correo'] =
                'Introduce un correo válido.';
        }

        $passwordErrors = $this->passwordPolicy->validate(
            $data['password']
        );

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

        return $errors;
    }
}