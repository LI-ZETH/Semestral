<?php

declare(strict_types=1);

namespace App\Core;

final class Roles
{
    public const ADMINISTRADOR = 'Administrador';
    public const TECNICO = 'Técnico';
    public const COLABORADOR = 'Colaborador';

    private function __construct()
    {
    }

    public static function all(): array
    {
        return [
            self::ADMINISTRADOR,
            self::TECNICO,
            self::COLABORADOR,
        ];
    }

    public static function isValid(string $role): bool
    {
        return in_array(
            $role,
            self::all(),
            true
        );
    }
}