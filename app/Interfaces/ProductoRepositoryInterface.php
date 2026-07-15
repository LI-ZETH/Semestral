<?php

declare(strict_types=1);

namespace App\Interfaces;

interface ProductoRepositoryInterface
{
    public function listBySubcategory(int $subcategoryId): ?array;

    public function listActiveSubcategories(): array;

    public function findSubcategoryById(int $subcategoryId): ?array;

    public function findById(int $productId): ?array;

    public function productExists(
        int $subcategoryId,
        string $name,
        string $brand,
        string $model,
        ?int $excludeId = null
    ): bool;

    public function create(array $data): int;

    public function update(int $productId, array $data): void;

    public function setActiveState(int $productId, bool $active): void;
}
