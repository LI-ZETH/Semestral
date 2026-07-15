<?php

declare(strict_types=1);

namespace App\Interfaces;

interface UsuarioRepositoryInterface
{
    public function administratorExists(): bool;

    public function findConflicts(
        string $cedula,
        string $usuario,
        string $correo
    ): array;

    public function createAdministrator(
        array $userData,
        string $publicKey,
        string $publicKeyFingerprint
    ): int;

    public function findForAuthentication(
        string $identifier
    ): ?array;

    public function recordFailedLogin(
        ?int $userId,
        string $identifier,
        string $ipAddress,
        ?string $userAgent,
        string $description,
        bool $increaseAttempts = true
    ): array;

    public function recordSuccessfulLogin(
        int $userId,
        string $identifier,
        string $ipAddress,
        ?string $userAgent
    ): void;

    public function updatePasswordHash(
        int $userId,
        string $passwordHash
    ): void;
}