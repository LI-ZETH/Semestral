<?php

declare(strict_types=1);

namespace App\Interfaces;

interface ActivoRepositoryInterface
{
    public function listByProduct(
        int $productId,
        array $filters = []
    ): ?array;

    public function listActiveProducts(): array;

    public function listAvailableStates(
        ?int $currentStateId = null
    ): array;

    public function listActiveLocations(): array;

    public function findProductById(int $productId): ?array;

    public function findStateById(int $stateId): ?array;

    public function findLocationById(int $locationId): ?array;

    public function findById(int $assetId): ?array;

    public function findImages(int $assetId): array;

    public function findConflicts(
        string $assetCode,
        ?string $serialNumber,
        ?int $excludeId = null
    ): array;

    public function create(array $data): int;

    public function update(
        int $assetId,
        array $data
    ): void;

    public function setActiveState(
        int $assetId,
        bool $active
    ): void;

    public function insertImage(
        int $assetId,
        array $data
    ): int;

    public function deactivateImages(
        int $assetId,
        array $imageIds
    ): void;

    public function clearPrincipalImage(int $assetId): void;

    public function setPrincipalImage(
        int $assetId,
        int $imageId
    ): void;

    public function getNextImageOrder(int $assetId): int;

    public function countActiveImages(int $assetId): int;

    public function hasActiveAssignment(int $assetId): bool;

    public function insertMovement(array $data): void;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollBack(): void;
}
