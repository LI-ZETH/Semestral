<?php

declare(strict_types=1);

namespace App\Interfaces;

interface InventarioConsultaRepositoryInterface
{
    public function getCategorySummary(): array;

    public function getCategoryProducts(
        int $categoryId
    ): ?array;

    public function getProductDetail(
        int $productId
    ): ?array;
}