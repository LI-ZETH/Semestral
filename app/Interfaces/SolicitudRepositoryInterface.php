<?php

declare(strict_types=1);

namespace App\Interfaces;

interface SolicitudRepositoryInterface
{
    public function listMyNeedRequests(int $userId): array;

    public function listMyRepairRequests(int $userId): array;

    public function listActiveSubcategories(): array;

    public function listActiveProducts(): array;

    public function listMyAssignedAssets(int $userId): array;

    public function findCollaboratorByUserId(int $userId): ?array;

    public function findNeedStateByName(string $name): ?array;

    public function createNeedRequest(array $data): int;

    public function findAssignedAssetForUser(
        int $assetId,
        int $userId
    ): ?array;

    public function findCurrentLocationForCollaborator(
        int $collaboratorId
    ): ?array;

    public function hasOpenRepairRequest(int $assetId): bool;

    public function createRepairRequest(array $data): int;

    public function cancelOwnNeedRequest(
        int $requestId,
        int $userId
    ): bool;

    public function cancelOwnRepairRequest(
        int $requestId,
        int $userId
    ): bool;

    public function listAllNeedRequests(array $filters = []): array;

    public function listAllRepairRequests(array $filters = []): array;

    public function findNeedRequest(int $requestId): ?array;

    public function listNeedReviewStates(): array;

    public function updateNeedReview(
        int $requestId,
        array $data
    ): void;

    public function listActiveTechnicians(): array;

    public function findRepairRequestForAssignment(
        int $requestId,
        bool $lock = false
    ): ?array;

    public function findTechnician(int $userId): ?array;

    public function findRepairStateByName(string $name): ?array;

    public function createRepair(array $data): int;

    public function assignRepairRequest(
        int $requestId,
        array $data
    ): void;

    public function findAssetStateByCode(string $code): ?array;

    public function updateAssetState(
        int $assetId,
        int $stateId
    ): void;

    public function insertMovement(array $data): void;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollBack(): void;
}
