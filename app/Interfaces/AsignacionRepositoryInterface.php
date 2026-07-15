<?php

declare(strict_types=1);

namespace App\Interfaces;

interface AsignacionRepositoryInterface
{
    public function listAll(array $filters = []): array;

    public function listAvailableAssets(): array;

    public function listActiveCollaborators(): array;

    public function listActiveLocations(): array;

    public function listReturnReasons(): array;

    public function listReturnStates(): array;

    public function findAssetForAssignment(
        int $assetId,
        bool $lock = false
    ): ?array;

    public function findCollaborator(int $collaboratorId): ?array;

    public function findLocation(int $locationId): ?array;

    public function findStateByCode(string $code): ?array;

    public function findStateById(int $stateId): ?array;

    public function findReturnReason(int $reasonId): ?array;

    public function findActiveAssignment(
        int $assignmentId,
        bool $lock = false
    ): ?array;

    public function hasActiveAssignment(int $assetId): bool;

    public function createAssignment(array $data): int;

    public function updateAssetStateAndLocation(
        int $assetId,
        int $stateId,
        int $locationId
    ): void;

    public function setCollaboratorCurrentLocation(
        int $collaboratorId,
        int $locationId,
        ?string $observations = null
    ): void;

    public function createReturn(array $data): int;

    public function completeAssignment(int $assignmentId): void;

    public function insertMovement(array $data): void;

    public function listMyActiveAssignments(int $userId): array;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollBack(): void;
}
