<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class QrCodeService
{
    private const ENDPOINT = 'https://quickchart.io/qr';

    public function getSvg(string $content): string
    {
        $content = trim($content);

        if ($content === '') {
            throw new RuntimeException(
                'El contenido del código QR está vacío.'
            );
        }

        $cacheDirectory = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'storage'
            . DIRECTORY_SEPARATOR
            . 'qrcodes';

        if (
            !is_dir($cacheDirectory)
            && !mkdir($cacheDirectory, 0775, true)
            && !is_dir($cacheDirectory)
        ) {
            throw new RuntimeException(
                'No fue posible preparar la carpeta de códigos QR.'
            );
        }

        $cachePath = $cacheDirectory
            . DIRECTORY_SEPARATOR
            . hash('sha256', $content)
            . '.svg';

        if (is_file($cachePath)) {
            $cached = file_get_contents($cachePath);

            if (is_string($cached) && $cached !== '') {
                return $cached;
            }
        }

        $query = http_build_query(
            [
                'text' => $content,
                'size' => 480,
                'format' => 'svg',
                'margin' => 2,
                'ecLevel' => 'M',
            ],
            '',
            '&',
            PHP_QUERY_RFC3986
        );

        $svg = $this->download(
            self::ENDPOINT . '?' . $query
        );

        if (
            stripos($svg, '<svg') === false
            || stripos($svg, '<script') !== false
            || stripos($svg, '<foreignObject') !== false
        ) {
            throw new RuntimeException(
                'El proveedor no devolvió un código QR válido.'
            );
        }

        file_put_contents(
            $cachePath,
            $svg,
            LOCK_EX
        );

        return $svg;
    }

    private function download(string $url): string
    {
        if (function_exists('curl_init')) {
            $handle = curl_init($url);

            if ($handle === false) {
                throw new RuntimeException(
                    'No fue posible iniciar la conexión QR.'
                );
            }

            curl_setopt_array(
                $handle,
                [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => false,
                    CURLOPT_CONNECTTIMEOUT => 7,
                    CURLOPT_TIMEOUT => 12,
                    CURLOPT_SSL_VERIFYPEER => true,
                    CURLOPT_SSL_VERIFYHOST => 2,
                    CURLOPT_HTTPHEADER => [
                        'Accept: image/svg+xml',
                        'User-Agent: TrackiT-CMDB/1.0',
                    ],
                ]
            );

            $response = curl_exec($handle);
            $status = (int) curl_getinfo(
                $handle,
                CURLINFO_RESPONSE_CODE
            );
            $error = curl_error($handle);

            curl_close($handle);

            if (
                !is_string($response)
                || $response === ''
                || $status < 200
                || $status >= 300
            ) {
                throw new RuntimeException(
                    $error !== ''
                        ? 'No fue posible generar el QR: ' . $error
                        : 'El servicio de QR no respondió correctamente.'
                );
            }

            return $response;
        }

        if ((bool) ini_get('allow_url_fopen')) {
            $context = stream_context_create(
                [
                    'http' => [
                        'method' => 'GET',
                        'timeout' => 12,
                        'header' => implode(
                            "\r\n",
                            [
                                'Accept: image/svg+xml',
                                'User-Agent: TrackiT-CMDB/1.0',
                            ]
                        ),
                    ],
                    'ssl' => [
                        'verify_peer' => true,
                        'verify_peer_name' => true,
                    ],
                ]
            );

            $response = @file_get_contents(
                $url,
                false,
                $context
            );

            if (is_string($response) && $response !== '') {
                return $response;
            }
        }

        throw new RuntimeException(
            'Activa la extensión cURL de PHP o allow_url_fopen para generar códigos QR.'
        );
    }
}
