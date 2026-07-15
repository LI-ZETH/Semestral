<?php

declare(strict_types=1);

namespace App\Interfaces;

interface CategoriaRepositoryInterface
{
    public function listAll(): array;

    public function findById(int $categoryId): ?array;

    public function nameExists(
        string $name,
        ?int $excludeId = null
    ): bool;

    public function create(array $data): int;

    public function update(
        int $categoryId,
        array $data
    ): void;

    public function setActiveState(
        int $categoryId,
        bool $active
    ): void;
}