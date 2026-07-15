<?php

declare(strict_types=1);

namespace App\Interfaces;

interface BajaActivoRepositoryInterface
{
    public function listAll(array $filters = []): array;

    public function listEligibleAssets(): array;

    public function listTypes(): array;

    public function findById(int $disposalId): ?array;

    public function findAssetById(
        int $assetId,
        bool $forUpdate = false
    ): ?array;

    public function findTypeById(int $typeId): ?array;

    public function findStateByCode(string $stateCode): ?array;

    public function hasRegisteredDisposal(int $assetId): bool;

    public function hasActiveAssignment(int $assetId): bool;

    public function hasOpenRepair(int $assetId): bool;

    public function hasOpenRepairRequest(int $assetId): bool;

    public function hasActiveLicenseAssignments(int $assetId): bool;

    public function create(array $data): int;

    public function updateAssetState(
        int $assetId,
        int $stateId
    ): void;

    public function insertMovement(array $data): void;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollBack(): void;
}
