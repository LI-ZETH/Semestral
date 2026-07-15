<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    private const SESSION_KEY = '_csrf_token';
    private const FORM_FIELD = '_token';

    private function __construct()
    {
    }

    public static function token(): string
    {
        $token = Session::get(
            self::SESSION_KEY
        );

        if (
            !is_string($token)
            || strlen($token) !== 64
        ) {
            $token = bin2hex(
                random_bytes(32)
            );

            Session::put(
                self::SESSION_KEY,
                $token
            );
        }

        return $token;
    }

    public static function validate(
        ?string $submittedToken
    ): bool {
        if (
            !is_string($submittedToken)
            || $submittedToken === ''
        ) {
            return false;
        }

        $sessionToken = Session::get(
            self::SESSION_KEY
        );

        if (
            !is_string($sessionToken)
            || $sessionToken === ''
        ) {
            return false;
        }

        return hash_equals(
            $sessionToken,
            $submittedToken
        );
    }

    public static function validateRequest(): bool
    {
        $method = strtoupper(
            (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')
        );

        /*
         * Las solicitudes de consulta no modifican información.
         */

        if (
            $method === 'GET'
            || $method === 'HEAD'
            || $method === 'OPTIONS'
        ) {
            return true;
        }

        $submittedToken = $_POST[
            self::FORM_FIELD
        ] ?? null;

        /*
         * Los futuros Fetch/AJAX también podrán enviar:
         * X-CSRF-Token: token
         */

        if (!is_string($submittedToken)) {
            $submittedToken = $_SERVER[
                'HTTP_X_CSRF_TOKEN'
            ] ?? null;
        }

        return self::validate(
            is_string($submittedToken)
                ? $submittedToken
                : null
        );
    }

    public static function rotate(): void
    {
        Session::put(
            self::SESSION_KEY,
            bin2hex(random_bytes(32))
        );
    }
}