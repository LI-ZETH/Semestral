<?php

declare(strict_types=1);

if (!function_exists('base_url')) {
    /**
     * Genera una URL tomando en cuenta la carpeta public del proyecto.
     */
    function base_url(string $path = ''): string
    {
        $scriptName = str_replace(
            '\\',
            '/',
            $_SERVER['SCRIPT_NAME'] ?? ''
        );

        $basePath = str_replace(
            '\\',
            '/',
            dirname($scriptName)
        );

        if ($basePath === '/' || $basePath === '.') {
            $basePath = '';
        }

        if ($path === '') {
            return $basePath . '/';
        }

        return $basePath . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset_url')) {
    /**
     * Genera la URL de un archivo CSS, JS o imagen pública.
     */
    function asset_url(string $path): string
    {
        return base_url($path);
    }
}

if (!function_exists('e')) {
    /**
     * Escapa texto antes de mostrarlo en HTML.
     */
    function e(mixed $value): string
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Obtiene el token CSRF actual.
     */
    function csrf_token(): string
    {
        return \App\Core\Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Genera el campo oculto para formularios POST.
     */
    function csrf_field(): string
    {
        $token = e(
            \App\Core\Csrf::token()
        );

        return sprintf(
            '<input type="hidden" name="_token" value="%s">',
            $token
        );
    }
}

if (!function_exists('flash')) {
    /**
     * Obtiene un mensaje temporal de la sesión.
     */
    function flash(
        string $key,
        mixed $default = null
    ): mixed {
        return \App\Core\Session::getFlash(
            $key,
            $default
        );
    }
}