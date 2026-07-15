<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\ReporteRepositoryInterface;

final class ReporteService
{
    public function __construct(
        private readonly ReporteRepositoryInterface $repository
    ) {
    }

    public function dashboard(): array
    {
        return [
            'summary' => $this->repository
                ->getDashboardSummary(),
            'categories' => $this->repository
                ->getCategorySummary(),
        ];
    }

    public function inventory(array $input): array
    {
        $filters = [
            'category' => max(
                0,
                (int) ($input['categoria'] ?? 0)
            ),
            'state' => max(
                0,
                (int) ($input['estado'] ?? 0)
            ),
            'search' => trim(
                (string) ($input['buscar'] ?? '')
            ),
        ];

        return [
            'filters' => $filters,
            'categories' => $this->repository
                ->getActiveCategories(),
            'states' => $this->repository
                ->getActiveStates(),
            'rows' => $this->repository
                ->getInventory($filters),
        ];
    }

    public function depreciation(array $input): array
    {
        $days = (int) ($input['dias'] ?? 180);

        if ($days < 0) {
            $days = 0;
        }

        if ($days > 3650) {
            $days = 3650;
        }

        return [
            'days' => $days,
            'rows' => $this->repository
                ->getDepreciation($days),
        ];
    }

    public function needs(array $input): array
    {
        $period = strtoupper(
            trim((string) ($input['periodo'] ?? ''))
        );

        if (!in_array(
            $period,
            ['', 'INMEDIATA', 'ANUAL', 'QUINQUENAL'],
            true
        )) {
            $period = '';
        }

        $filters = [
            'year' => max(
                0,
                (int) ($input['anio'] ?? 0)
            ),
            'period' => $period,
            'status' => trim(
                (string) ($input['estado'] ?? '')
            ),
        ];

        $rows = $this->repository->getNeeds($filters);

        $estimatedTotal = 0.0;
        $requestedUnits = 0;

        foreach ($rows as $row) {
            $requestedUnits += (int) $row['cantidad'];

            if ($row['costoEstimado'] !== null) {
                $estimatedTotal += (
                    (float) $row['costoEstimado']
                    * (int) $row['cantidad']
                );
            }
        }

        return [
            'filters' => $filters,
            'years' => $this->repository->getNeedYears(),
            'rows' => $rows,
            'estimatedTotal' => $estimatedTotal,
            'requestedUnits' => $requestedUnits,
        ];
    }

    public function movements(array $input): array
    {
        $movementType = $input['tipoMovimiento']
            ?? $input['movimiento']
            ?? $input['tipo']
            ?? '';

        if ((string) $movementType === 'movimientos') {
            $movementType = '';
        }

        $filters = [
            'type' => strtoupper(
                trim((string) $movementType)
            ),
            'search' => trim(
                (string) ($input['buscar'] ?? '')
            ),
        ];

        return [
            'filters' => $filters,
            'rows' => $this->repository
                ->getMovements($filters),
        ];
    }

    public function accesses(array $input): array
    {
        $result = (string) ($input['resultado'] ?? '');

        if (!in_array($result, ['', '0', '1'], true)) {
            $result = '';
        }

        $filters = [
            'result' => $result,
            'search' => trim(
                (string) ($input['buscar'] ?? '')
            ),
        ];

        return [
            'filters' => $filters,
            'rows' => $this->repository
                ->getLoginHistory($filters),
        ];
    }

    public function audit(array $input): array
    {
        $filters = [
            'module' => strtoupper(
                trim((string) ($input['modulo'] ?? ''))
            ),
            'search' => trim(
                (string) ($input['buscar'] ?? '')
            ),
        ];

        return [
            'filters' => $filters,
            'rows' => $this->repository
                ->getAuditHistory($filters),
        ];
    }
}
