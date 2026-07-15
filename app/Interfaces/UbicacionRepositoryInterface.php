<?php

declare(strict_types=1);

namespace App\Interfaces;

interface UbicacionRepositoryInterface
{
    public function listAll(array $filters = []): array;

    public function findById(int $locationId): ?array;

    public function nameExists(
        string $name,
        ?int $excludeId = null
    ): bool;

    public function create(array $data): int;

    public function update(
        int $locationId,
        array $data
    ): void;

    public function countActiveUsage(int $locationId): array;

    public function setActiveState(
        int $locationId,
        bool $active
    ): void;
}
