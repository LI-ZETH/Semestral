<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Permissions;
use App\Repositories\ActivoDetalleRepository;
use App\Services\ActivoDetalleService;
use App\Services\QrCodeService;
use Throwable;

final class ActivoDetalleController extends Controller
{
    public function show(): void
    {
        Auth::requirePermission(
            Permissions::INVENTARIO_VER_TODO
        );

        $assetId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!is_int($assetId) || $assetId <= 0) {
            $this->renderNotFound();
            return;
        }

        $service = $this->buildService();
        $result = $service->getInternalDetail(
            $assetId
        );

        if ($result === null) {
            $this->renderNotFound();
            return;
        }

        $this->view(
            'inventario/assets/show',
            [
                'title' => 'Ficha del activo',
                'asset' => $result['asset'],
                'images' => $result['images'],
                'movements' => $result['movements'],
                'repairs' => $result['repairs'],
                'publicUrl' => $result['publicUrl'],
                'isLocalAddress' => $service
                    ->isLocalAddress(
                        $result['publicUrl']
                    ),
            ]
        );
    }

    public function publicSheet(): void
    {
        $token = trim(
            (string) ($_GET['token'] ?? '')
        );

        $result = $this->buildService()
            ->getPublicDetail($token);

        if ($result === null) {
            $this->renderNotFound();
            return;
        }

        $this->view(
            'inventario/assets/public',
            [
                'title' => 'Verificación de activo',
                'asset' => $result['asset'],
                'images' => $result['images'],
                'publicUrl' => $result['publicUrl'],
            ]
        );
    }

    public function qr(): void
    {
        $token = trim(
            (string) ($_GET['token'] ?? '')
        );

        $service = $this->buildService();
        $result = $service->getPublicDetail($token);

        if ($result === null) {
            http_response_code(404);
            header('Content-Type: image/svg+xml; charset=UTF-8');
            echo $this->errorSvg('Activo no encontrado');
            exit;
        }

        try {
            $svg = (new QrCodeService())->getSvg(
                $result['publicUrl']
            );

            header('Content-Type: image/svg+xml; charset=UTF-8');
            header('X-Content-Type-Options: nosniff');
            header('Cache-Control: public, max-age=86400');

            if (
                (string) ($_GET['descargar'] ?? '')
                === '1'
            ) {
                $filename = preg_replace(
                    '/[^A-Za-z0-9_-]+/',
                    '-',
                    (string) $result['asset']['codigoActivo']
                );

                header(
                    'Content-Disposition: attachment; filename="QR-'
                    . trim((string) $filename, '-')
                    . '.svg"'
                );
            }

            echo $svg;
            exit;
        } catch (Throwable) {
            http_response_code(503);
            header('Content-Type: image/svg+xml; charset=UTF-8');
            echo $this->errorSvg(
                'QR temporalmente no disponible'
            );
            exit;
        }
    }

    private function buildService(): ActivoDetalleService
    {
        return new ActivoDetalleService(
            new ActivoDetalleRepository()
        );
    }

    private function renderNotFound(): void
    {
        http_response_code(404);

        $this->view(
            'errors/404',
            [
                'title' => 'Activo no encontrado',
                'path' => '/inventario',
            ]
        );
    }

    private function errorSvg(string $message): string
    {
        $safeMessage = htmlspecialchars(
            $message,
            ENT_QUOTES | ENT_XML1,
            'UTF-8'
        );

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<svg xmlns="http://www.w3.org/2000/svg" '
            . 'width="480" height="480" viewBox="0 0 480 480">'
            . '<rect width="480" height="480" fill="#f7f9fc"/>'
            . '<rect x="24" y="24" width="432" height="432" '
            . 'rx="24" fill="#ffffff" stroke="#d9e1eb"/>'
            . '<text x="240" y="226" text-anchor="middle" '
            . 'font-family="Arial, sans-serif" font-size="20" '
            . 'fill="#aa4141">'
            . $safeMessage
            . '</text>'
            . '<text x="240" y="262" text-anchor="middle" '
            . 'font-family="Arial, sans-serif" font-size="14" '
            . 'fill="#687585">Intenta nuevamente con conexión a Internet.</text>'
            . '</svg>';
    }
}
