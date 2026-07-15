<?php

declare(strict_types=1);

namespace App\Interfaces;

interface ReporteRepositoryInterface
{
    public function getDashboardSummary(): array;

    public function getCategorySummary(): array;

    public function getActiveCategories(): array;

    public function getActiveStates(): array;

    public function getInventory(array $filters = []): array;

    public function getDepreciation(int $maxDays): array;

    public function getNeedYears(): array;

    public function getNeeds(array $filters = []): array;

    public function getMovements(array $filters = []): array;

    public function getLoginHistory(array $filters = []): array;

    public function getAuditHistory(array $filters = []): array;
}
