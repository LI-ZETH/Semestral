<?php

declare(strict_types=1);

namespace App\Core;

final class SecurityHeaders
{
    private function __construct()
    {
    }

    public static function send(): void
    {
        if (headers_sent()) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Evita que el navegador interprete archivos con otro MIME
        |--------------------------------------------------------------------------
        */

        header('X-Content-Type-Options: nosniff');

        /*
        |--------------------------------------------------------------------------
        | Evita que la aplicación sea cargada dentro de un iframe
        |--------------------------------------------------------------------------
        */

        header('X-Frame-Options: DENY');

        /*
        |--------------------------------------------------------------------------
        | Controla la información enviada en Referer
        |--------------------------------------------------------------------------
        */

        header(
            'Referrer-Policy: strict-origin-when-cross-origin'
        );

        /*
        |--------------------------------------------------------------------------
        | Desactiva funciones del navegador que el sistema no utiliza
        |--------------------------------------------------------------------------
        */

        header(
            'Permissions-Policy: '
            . 'camera=(), '
            . 'microphone=(), '
            . 'geolocation=()'
        );

        /*
        |--------------------------------------------------------------------------
        | Content Security Policy
        |--------------------------------------------------------------------------
        |
        | Los CSS, JavaScript e imágenes deben cargarse desde el proyecto.
        |
        */

        $contentSecurityPolicy = [
            "default-src 'self'",
            "script-src 'self'",
            "style-src 'self'",
            "img-src 'self' data:",
            "font-src 'self'",
            "connect-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ];

        header(
            'Content-Security-Policy: '
            . implode('; ', $contentSecurityPolicy)
        );

        /*
        |--------------------------------------------------------------------------
        | HSTS
        |--------------------------------------------------------------------------
        |
        | Solo debe enviarse mediante HTTPS.
        | En localhost normalmente no se enviará.
        |
        */

        if (self::isHttpsRequest()) {
            header(
                'Strict-Transport-Security: '
                . 'max-age=31536000; includeSubDomains'
            );
        }
    }

    private static function isHttpsRequest(): bool
    {
        $https = $_SERVER['HTTPS'] ?? '';

        return $https !== ''
            && strtolower((string) $https) !== 'off';
    }
}