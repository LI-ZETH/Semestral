<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Session
{
    private const LAST_ACTIVITY_KEY = '_last_activity';
    private const REGENERATED_AT_KEY = '_regenerated_at';
    private const FLASH_NEW_KEY = '_flash_new';
    private const FLASH_OLD_KEY = '_flash_old';

    private function __construct()
    {
    }

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $config = self::getConfiguration();

        /*
        |--------------------------------------------------------------------------
        | Configuración segura
        |--------------------------------------------------------------------------
        */

        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', $config['same_site']);

        session_name($config['name']);

        $isHttps = self::isHttpsRequest();

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => $config['cookie_path'],
            'domain' => $config['cookie_domain'],
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => $config['same_site'],
        ]);

        if (!session_start()) {
            throw new RuntimeException(
                'No fue posible iniciar la sesión.'
            );
        }

        self::rotateFlashData();
        self::validateIdleTimeout(
            $config['idle_timeout']
        );

        self::regeneratePeriodically(
            $config['regeneration_interval']
        );

        $_SESSION[self::LAST_ACTIVITY_KEY] = time();
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public static function get(
        string $key,
        mixed $default = null
    ): mixed {
        return $_SESSION[$key] ?? $default;
    }

    public static function put(
        string $key,
        mixed $value
    ): void {
        $_SESSION[$key] = $value;
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function regenerate(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::start();
        }

        if (!session_regenerate_id(true)) {
            throw new RuntimeException(
                'No fue posible renovar la sesión.'
            );
        }

        $_SESSION[self::REGENERATED_AT_KEY] = time();
    }

    public static function flash(
        string $key,
        mixed $value
    ): void {
        $_SESSION[self::FLASH_NEW_KEY][$key] = $value;
    }

    public static function getFlash(
        string $key,
        mixed $default = null
    ): mixed {
        return $_SESSION[self::FLASH_OLD_KEY][$key]
            ?? $default;
    }

    public static function hasFlash(string $key): bool
    {
        return isset(
            $_SESSION[self::FLASH_OLD_KEY][$key]
        );
    }

    public static function destroy(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $_SESSION = [];

        $cookieParameters = session_get_cookie_params();

        if (ini_get('session.use_cookies')) {
            setcookie(
                session_name(),
                '',
                [
                    'expires' => time() - 42000,
                    'path' => $cookieParameters['path'],
                    'domain' => $cookieParameters['domain'],
                    'secure' => $cookieParameters['secure'],
                    'httponly' => $cookieParameters['httponly'],
                    'samesite' => $cookieParameters['samesite']
                        ?? 'Lax',
                ]
            );
        }

        session_destroy();
    }

    private static function getConfiguration(): array
    {
        $configurationPath = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'config'
            . DIRECTORY_SEPARATOR
            . 'session.php';

        if (!is_file($configurationPath)) {
            throw new RuntimeException(
                'No existe config/session.php.'
            );
        }

        $configuration = require $configurationPath;

        $requiredFields = [
            'name',
            'idle_timeout',
            'regeneration_interval',
            'cookie_path',
            'cookie_domain',
            'same_site',
        ];

        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $configuration)) {
                throw new RuntimeException(
                    "Falta la configuración de sesión: {$field}."
                );
            }
        }

        return $configuration;
    }

    private static function validateIdleTimeout(
        int $idleTimeout
    ): void {
        $lastActivity = $_SESSION[
            self::LAST_ACTIVITY_KEY
        ] ?? null;

        if (!is_int($lastActivity)) {
            return;
        }

        if ((time() - $lastActivity) <= $idleTimeout) {
            return;
        }

        /*
         * Se elimina la sesión anterior y se genera un nuevo ID.
         */

        $_SESSION = [];

        if (!session_regenerate_id(true)) {
            throw new RuntimeException(
                'No fue posible renovar una sesión expirada.'
            );
        }

        $_SESSION['session_expired'] = true;
        $_SESSION[self::REGENERATED_AT_KEY] = time();
    }

    private static function regeneratePeriodically(
        int $interval
    ): void {
        $regeneratedAt = $_SESSION[
            self::REGENERATED_AT_KEY
        ] ?? null;

        if (
            is_int($regeneratedAt)
            && (time() - $regeneratedAt) < $interval
        ) {
            return;
        }

        self::regenerate();
    }

    private static function rotateFlashData(): void
    {
        $_SESSION[self::FLASH_OLD_KEY] =
            $_SESSION[self::FLASH_NEW_KEY] ?? [];

        $_SESSION[self::FLASH_NEW_KEY] = [];
    }

    private static function isHttpsRequest(): bool
    {
        $https = $_SERVER['HTTPS'] ?? '';

        return $https !== ''
            && strtolower((string) $https) !== 'off';
    }
}