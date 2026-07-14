<?php

declare(strict_types=1);

namespace App\Interfaces;

interface UsuarioManagementRepositoryInterface
{
    public function listUsers(array $filters = []): array;

    public function listActiveRoles(): array;

    public function findById(int $userId): ?array;

    public function findConflictsExcluding(
        int $userId,
        string $cedula,
        string $usuario,
        string $correo
    ): array;

    public function createUser(
        array $userData,
        ?array $collaboratorData
    ): int;

    public function updateUser(
        int $userId,
        array $userData,
        ?array $collaboratorData
    ): void;

    public function setActiveState(
        int $userId,
        bool $active
    ): void;

    public function unlock(int $userId): void;

    public function countActiveAdministrators(): int;
}