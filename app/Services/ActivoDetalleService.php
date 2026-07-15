<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\ActivoDetalleRepositoryInterface;
use DateTimeImmutable;
use Throwable;

final class ActivoDetalleService
{
    public function __construct(
        private readonly ActivoDetalleRepositoryInterface $repository
    ) {
    }

    public function getInternalDetail(int $assetId): ?array
    {
        $asset = $this->repository->findInternalById(
            $assetId
        );

        if ($asset === null) {
            return null;
        }

        $asset = $this->appendDepreciation($asset);

        return [
            'asset' => $asset,
            'images' => $this->repository->findImages(
                $assetId
            ),
            'movements' => $this->repository
                ->findRecentMovements($assetId),
            'repairs' => $this->repository
                ->findRepairs($assetId),
            'publicUrl' => $this->buildPublicUrl(
                (string) $asset['qrToken']
            ),
        ];
    }

    public function getPublicDetail(string $token): ?array
    {
        if (!$this->isValidToken($token)) {
            return null;
        }

        $asset = $this->repository->findPublicByToken(
            $token
        );

        if ($asset === null) {
            return null;
        }

        return [
            'asset' => $asset,
            'images' => $this->repository->findImages(
                (int) $asset['idActivo']
            ),
            'publicUrl' => $this->buildPublicUrl($token),
        ];
    }

    public function isValidToken(string $token): bool
    {
        return preg_match(
            '/^[a-f0-9]{64}$/i',
            $token
        ) === 1;
    }

    public function buildPublicUrl(string $token): string
    {
        $https = strtolower(
            (string) ($_SERVER['HTTPS'] ?? '')
        );

        $scheme = (
            $https !== ''
            && $https !== 'off'
        ) ? 'https' : 'http';

        $host = (string) (
            $_SERVER['HTTP_HOST']
            ?? $_SERVER['SERVER_NAME']
            ?? 'localhost'
        );

        if (
            preg_match(
                '/^[a-z0-9.\-:\[\]]+$/i',
                $host
            ) !== 1
        ) {
            $host = 'localhost';
        }

        return $scheme
            . '://'
            . $host
            . base_url(
                'activo/ficha?token='
                . rawurlencode($token)
            );
    }

    public function isLocalAddress(string $url): bool
    {
        $host = strtolower(
            (string) parse_url($url, PHP_URL_HOST)
        );

        return in_array(
            $host,
            ['localhost', '127.0.0.1', '::1'],
            true
        );
    }

    private function appendDepreciation(array $asset): array
    {
        $cost = max(0.0, (float) $asset['costo']);
        $residual = max(
            0.0,
            min(
                $cost,
                (float) $asset['valorResidual']
            )
        );
        $lifeMonths = (int) (
            $asset['vidaUtilMesesAplicada']
            ?? 0
        );

        $elapsedMonths = 0;

        try {
            $acquisitionDate = new DateTimeImmutable(
                (string) $asset['fechaAdquisicion']
            );
            $today = new DateTimeImmutable('today');

            if ($today > $acquisitionDate) {
                $interval = $acquisitionDate->diff($today);
                $elapsedMonths = (
                    ((int) $interval->y * 12)
                    + (int) $interval->m
                );
            }
        } catch (Throwable) {
            $elapsedMonths = 0;
        }

        $progress = $lifeMonths > 0
            ? min(1, $elapsedMonths / $lifeMonths)
            : 0.0;

        $bookValue = $lifeMonths > 0
            ? max(
                $residual,
                $cost - (($cost - $residual) * $progress)
            )
            : $cost;

        $asset['mesesTranscurridos'] = $elapsedMonths;
        $asset['porcentajeVidaConsumida'] = round(
            $progress * 100,
            2
        );
        $asset['valorLibroEstimado'] = round(
            $bookValue,
            2
        );

        return $asset;
    }
}
