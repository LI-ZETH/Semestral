<?php

declare(strict_types=1);

namespace App\Core;

use ErrorException;
use Throwable;

final class ErrorHandler
{
    private const FATAL_ERRORS = [
        E_ERROR,
        E_PARSE,
        E_CORE_ERROR,
        E_COMPILE_ERROR,
        E_USER_ERROR,
    ];

    private function __construct()
    {
    }

    public static function register(): void
    {
        error_reporting(E_ALL);

        ini_set('display_errors', '0');

        set_error_handler(
            static function (
                int $severity,
                string $message,
                string $file,
                int $line
            ): bool {
                if (!(error_reporting() & $severity)) {
                    return false;
                }

                throw new ErrorException(
                    $message,
                    0,
                    $severity,
                    $file,
                    $line
                );
            }
        );

        set_exception_handler(
            static function (Throwable $exception): void {
                self::handle($exception);
            }
        );

        register_shutdown_function(
            static function (): void {
                self::handleShutdown();
            }
        );
    }

    private static function handle(
        Throwable $exception
    ): void {
        $errorId = self::generateErrorId();

        Logger::error(
            $exception,
            $errorId
        );

        if (!headers_sent()) {
            http_response_code(500);
        }

        if (PHP_SAPI === 'cli') {
            fwrite(
                STDERR,
                "Error interno. Código: {$errorId}"
                . PHP_EOL
            );

            return;
        }

        try {
            View::render(
                'errors/500',
                [
                    'title' => 'Error interno',
                    'errorId' => $errorId,
                ]
            );
        } catch (Throwable) {
            self::renderFallback($errorId);
        }
    }

    private static function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error === null) {
            return;
        }

        if (!in_array($error['type'], self::FATAL_ERRORS, true)) {
            return;
        }

        $exception = new ErrorException(
            $error['message'],
            0,
            $error['type'],
            $error['file'],
            $error['line']
        );

        self::handle($exception);
    }

    private static function generateErrorId(): string
    {
        try {
            return strtoupper(
                bin2hex(random_bytes(4))
            );
        } catch (Throwable) {
            return strtoupper(
                substr(
                    hash('sha256', uniqid('', true)),
                    0,
                    8
                )
            );
        }
    }

    private static function renderFallback(
        string $errorId
    ): void {
        $safeErrorId = htmlspecialchars(
            $errorId,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        echo <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta
                name="viewport"
                content="width=device-width, initial-scale=1.0"
            >
            <title>Error interno</title>
        </head>
        <body>
            <h1>Ocurrió un error interno</h1>
            <p>
                Código de referencia:
                <strong>{$safeErrorId}</strong>
            </p>
        </body>
        </html>
        HTML;
    }
}