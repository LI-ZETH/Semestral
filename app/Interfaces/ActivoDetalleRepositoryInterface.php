<?php

declare(strict_types=1);

namespace App\Interfaces;

interface ActivoDetalleRepositoryInterface
{
    public function findInternalById(int $assetId): ?array;

    public function findPublicByToken(string $token): ?array;

    public function findImages(int $assetId): array;

    public function findRecentMovements(int $assetId): array;

    public function findRepairs(int $assetId): array;
}
