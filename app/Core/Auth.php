<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    private const USER_SESSION_KEY = 'auth_user';
    private const AUTHENTICATED_AT_KEY = 'authenticated_at';

    private function __construct()
    {
    }

    public static function login(array $user): void
    {
        /*
         * El ID se renueva antes de establecer la identidad
         * autenticada.
         */
        Session::regenerate();

        Session::put(
            self::USER_SESSION_KEY,
            [
                'idUsuario' => (int) $user['idUsuario'],
                'cedula' => (string) $user['cedula'],
                'nombre' => (string) $user['nombre'],
                'apellido' => (string) $user['apellido'],
                'usuario' => (string) $user['usuario'],
                'correo' => (string) $user['correo'],
                'idRol' => (int) $user['idRol'],
                'nombreRol' => (string) $user['nombreRol'],
            ]
        );

        Session::put(
            self::AUTHENTICATED_AT_KEY,
            time()
        );

        Csrf::rotate();
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    public static function check(): bool
    {
        $user = Session::get(
            self::USER_SESSION_KEY
        );

        return is_array($user)
            && isset($user['idUsuario']);
    }

    public static function guest(): bool
    {
        return !self::check();
    }

    public static function user(): ?array
    {
        $user = Session::get(
            self::USER_SESSION_KEY
        );

        return is_array($user)
            ? $user
            : null;
    }

    public static function id(): ?int
    {
        $user = self::user();

        return isset($user['idUsuario'])
            ? (int) $user['idUsuario']
            : null;
    }

    public static function role(): ?string
    {
        $user = self::user();

        return isset($user['nombreRol'])
            ? (string) $user['nombreRol']
            : null;
    }

    public static function hasRole(string $role): bool
    {
        return self::check()
            && self::role() === $role;
    }

    public static function requireAuth(): void
    {
        if (self::check()) {
            return;
        }

        Session::flash(
            'warning',
            'Debes iniciar sesión para acceder a esa sección.'
        );

        header(
            'Location: ' . base_url('login')
        );

        exit;
    }

    public static function requireGuest(): void
    {
        if (self::guest()) {
            return;
        }

        header(
            'Location: ' . base_url('panel')
        );

        exit;
    }

    public static function requireRole(string $role): void
    {
        self::requireAuth();

        if (self::hasRole($role)) {
            return;
        }

        http_response_code(403);

        View::render(
            'errors/403',
            [
                'title' => 'Acceso denegado',
            ]
        );

        exit;
    }
}