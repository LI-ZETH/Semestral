<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

final class Logger
{
    private function __construct()
    {
    }

    public static function error(
        Throwable $exception,
        string $errorId
    ): void {
        $logDirectory = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'storage'
            . DIRECTORY_SEPARATOR
            . 'logs';

        if (
            !is_dir($logDirectory)
            && !mkdir($logDirectory, 0775, true)
            && !is_dir($logDirectory)
        ) {
            return;
        }

        $logFile = $logDirectory
            . DIRECTORY_SEPARATOR
            . 'app-'
            . date('Y-m-d')
            . '.log';

        $record = [
            'fecha' => date('Y-m-d H:i:s'),
            'codigo_error' => $errorId,
            'tipo' => $exception::class,
            'mensaje' => $exception->getMessage(),
            'archivo' => $exception->getFile(),
            'linea' => $exception->getLine(),
            'metodo_http' => $_SERVER['REQUEST_METHOD'] ?? null,
            'ruta' => $_SERVER['REQUEST_URI'] ?? null,
            'direccion_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'traza' => $exception->getTraceAsString(),
        ];

        $json = json_encode(
            $record,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
        );

        if (!is_string($json)) {
            return;
        }

        file_put_contents(
            $logFile,
            $json . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}