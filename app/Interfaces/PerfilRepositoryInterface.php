<?php

declare(strict_types=1);

namespace App\Interfaces;

interface PerfilRepositoryInterface
{
    public function findByUserId(int $userId): ?array;

    public function listActiveLocations(): array;

    public function getLocationHistory(int $collaboratorId): array;

    public function emailExistsForAnotherUser(
        int $userId,
        string $email
    ): bool;

    public function identificationExistsForAnotherUser(
        int $userId,
        string $identification
    ): bool;

    public function findActiveLocationById(
        int $locationId
    ): ?array;

    public function updateProfile(
        int $userId,
        array $data
    ): void;

    public function findPasswordHash(
        int $userId
    ): ?string;

    public function updatePassword(
        int $userId,
        string $passwordHash
    ): void;
}
