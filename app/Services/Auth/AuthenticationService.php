<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Core\ValidationException;
use App\Interfaces\UsuarioRepositoryInterface;
use App\Services\Crypto\PasswordHasherService;

final class AuthenticationService
{
    private const DUMMY_PASSWORD_HASH =
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';

    public function __construct(
        private readonly UsuarioRepositoryInterface $userRepository,
        private readonly PasswordHasherService $passwordHasher
    ) {
    }

    public function authenticate(
        array $input,
        string $ipAddress,
        ?string $userAgent
    ): array {
        $identifier = trim(
            (string) ($input['identifier'] ?? '')
        );

        $password = (string) (
            $input['password'] ?? ''
        );

        $errors = $this->validateInput(
            $identifier,
            $password
        );

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $user = $this->userRepository
            ->findForAuthentication($identifier);

        if ($user === null) {
            $this->passwordHasher->verificar(
                $password,
                self::DUMMY_PASSWORD_HASH
            );

            $this->userRepository->recordFailedLogin(
                null,
                $identifier,
                $ipAddress,
                $userAgent,
                'No existe una cuenta asociada al identificador ingresado.',
                false
            );

            throw new ValidationException([
                'general' =>
                'No existe una cuenta asociada a los datos ingresados. '
                . 'Contacte al administrador del sistema.',
            ]);
        }

        $userId = (int) $user['idUsuario'];

        if (!(bool) $user['activo']) {
            $this->userRepository->recordFailedLogin(
                $userId,
                $identifier,
                $ipAddress,
                $userAgent,
                'La cuenta está inactiva.',
                false
            );

            throw new ValidationException([
                'general' =>
                    'La cuenta está desactivada. '
                    . 'Contacta a un administrador.',
            ]);
        }

        if (!(bool) ($user['rolActivo'] ?? false)) {
            $this->userRepository->recordFailedLogin(
                $userId,
                $identifier,
                $ipAddress,
                $userAgent,
                'El rol asignado está inactivo.',
                false
            );

            throw new ValidationException([
                'general' =>
                    'El rol de esta cuenta no está disponible. '
                    . 'Contacta a un administrador.',
            ]);
        }

        if ((bool) $user['bloqueado']) {
            $this->userRepository->recordFailedLogin(
                $userId,
                $identifier,
                $ipAddress,
                $userAgent,
                'Intento de acceso a una cuenta bloqueada.',
                false
            );

            throw new ValidationException([
                'general' =>
                    'La cuenta está bloqueada. '
                    . 'Contacta a un administrador.',
            ]);
        }

        $validPassword = $this->passwordHasher->verificar(
            $password,
            (string) $user['passwordHash']
        );

        if (!$validPassword) {
            $result = $this->userRepository
                ->recordFailedLogin(
                    $userId,
                    $identifier,
                    $ipAddress,
                    $userAgent,
                    'Contraseña incorrecta.',
                    true
                );

            if ($result['blocked']) {
                throw new ValidationException([
                    'general' =>
                        'La cuenta fue bloqueada después '
                        . 'de tres intentos fallidos.',
                ]);
            }

            throw new ValidationException([
                'general' =>
                    'Contraseña incorrecta. Intentos restantes: '
                    . $result['remainingAttempts']
                    . '.',
            ]);
        }

        if (
            $this->passwordHasher->necesitaActualizacion(
                (string) $user['passwordHash']
            )
        ) {
            $newHash = $this->passwordHasher->transformar(
                $password
            );

            $this->userRepository->updatePasswordHash(
                $userId,
                $newHash
            );
        }

        $this->userRepository->recordSuccessfulLogin(
            $userId,
            $identifier,
            $ipAddress,
            $userAgent
        );

        return [
            'idUsuario' => $userId,
            'cedula' => (string) $user['cedula'],
            'nombre' => (string) $user['nombre'],
            'apellido' => (string) $user['apellido'],
            'usuario' => (string) $user['usuario'],
            'correo' => (string) $user['correo'],
            'idRol' => (int) $user['idRol'],
            'nombreRol' => (string) $user['nombreRol'],
        ];
    }

    private function validateInput(
        string $identifier,
        string $password
    ): array {
        $errors = [];

        if ($identifier === '') {
            $errors['identifier'] =
                'Introduce tu usuario, correo o cédula.';
        }

        if (mb_strlen($identifier) > 120) {
            $errors['identifier'] =
                'El identificador es demasiado extenso.';
        }

        if ($password === '') {
            $errors['password'] =
                'Introduce tu contraseña.';
        }

        if (strlen($password) > 255) {
            $errors['password'] =
                'La contraseña es demasiado extensa.';
        }

        return $errors;
    }
}
