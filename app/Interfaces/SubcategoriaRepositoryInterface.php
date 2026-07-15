<?php

declare(strict_types=1);

namespace App\Interfaces;

interface SubcategoriaRepositoryInterface
{
    public function listByCategory(int $categoryId): ?array;

    public function listActiveCategories(): array;

    public function findCategoryById(int $categoryId): ?array;

    public function findById(int $subcategoryId): ?array;

    public function nameExists(
        int $categoryId,
        string $name,
        ?int $excludeId = null
    ): bool;

    public function create(array $data): int;

    public function update(
        int $subcategoryId,
        array $data
    ): void;

    public function setActiveState(
        int $subcategoryId,
        bool $active
    ): void;
}