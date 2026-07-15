<?php

declare(strict_types=1);

namespace App\Interfaces;

interface LicenciaRepositoryInterface
{
    public function listAll(array $filters = []): array;

    public function listEligibleAssets(): array;

    public function findEligibleAsset(int $assetId): ?array;

    public function findById(int $licenseId, bool $lock = false): ?array;

    public function findByAssetId(int $assetId): ?array;

    public function create(array $data): int;

    public function update(int $licenseId, array $data): void;

    public function listActiveCollaborators(): array;

    public function findActiveCollaborator(int $collaboratorId): ?array;

    public function listAssignments(int $licenseId): array;

    public function countActiveAssignments(int $licenseId): int;

    public function hasActiveAssignment(
        int $licenseId,
        int $collaboratorId
    ): bool;

    public function createAssignment(array $data): int;

    public function findAssignmentById(
        int $assignmentId,
        bool $lock = false
    ): ?array;

    public function revokeAssignment(int $assignmentId): void;

    public function listMyLicenses(int $userId): array;

    public function getUserPasswordHash(int $userId): ?string;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollBack(): void;
}
