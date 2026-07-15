<?php

declare(strict_types=1);

namespace App\Interfaces;

interface ReparacionRepositoryInterface
{
    public function listTasks(
        int $userId,
        bool $administrator,
        array $filters = []
    ): array;

    public function findTask(
        int $repairRequestId,
        int $userId,
        bool $administrator,
        bool $lock = false
    ): ?array;

    public function listWorkStates(): array;

    public function findRepairStateById(int $stateId): ?array;

    public function findAssetStateByCode(string $code): ?array;

    public function hasActiveAssignment(int $assetId): bool;

    public function updateRepair(
        int $repairId,
        array $data
    ): void;

    public function updateRepairRequest(
        int $repairRequestId,
        array $data
    ): void;

    public function updateAssetState(
        int $assetId,
        int $stateId
    ): void;

    public function insertMovement(array $data): void;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollBack(): void;
}
