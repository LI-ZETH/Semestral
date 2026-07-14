<?php

declare(strict_types=1);

namespace App\Core;

final class Permissions
{
    /*
    |--------------------------------------------------------------------------
    | Usuarios
    |--------------------------------------------------------------------------
    */

    public const USUARIOS_VER = 'usuarios.ver';
    public const USUARIOS_CREAR = 'usuarios.crear';
    public const USUARIOS_EDITAR = 'usuarios.editar';
    public const USUARIOS_CAMBIAR_ESTADO =
        'usuarios.cambiar_estado';
    public const USUARIOS_DESBLOQUEAR =
        'usuarios.desbloquear';

    /*
    |--------------------------------------------------------------------------
    | Inventario
    |--------------------------------------------------------------------------
    */

    public const INVENTARIO_VER_TODO =
        'inventario.ver_todo';
    public const INVENTARIO_GESTIONAR =
        'inventario.gestionar';
    public const INVENTARIO_VER_PROPIO =
        'inventario.ver_propio';

    /*
    |--------------------------------------------------------------------------
    | Reparaciones
    |--------------------------------------------------------------------------
    */

    public const REPARACIONES_VER =
        'reparaciones.ver';
    public const REPARACIONES_GESTIONAR =
        'reparaciones.gestionar';
    public const UBICACION_SOLICITANTE_VER =
        'ubicacion_solicitante.ver';

    /*
    |--------------------------------------------------------------------------
    | Solicitudes
    |--------------------------------------------------------------------------
    */

    public const SOLICITUDES_CREAR =
        'solicitudes.crear';
    public const SOLICITUDES_VER_PROPIAS =
        'solicitudes.ver_propias';
    public const SOLICITUDES_VER_TODAS =
        'solicitudes.ver_todas';

    /*
    |--------------------------------------------------------------------------
    | Perfil
    |--------------------------------------------------------------------------
    */

    public const PERFIL_VER =
        'perfil.ver';
    public const PERFIL_EDITAR =
        'perfil.editar';

    private const ROLE_PERMISSIONS = [
        Roles::ADMINISTRADOR => [
            '*',
        ],

        Roles::TECNICO => [
            self::INVENTARIO_VER_TODO,
            self::REPARACIONES_VER,
            self::REPARACIONES_GESTIONAR,
            self::UBICACION_SOLICITANTE_VER,
            self::PERFIL_VER,
            self::PERFIL_EDITAR,
        ],

        Roles::COLABORADOR => [
            self::INVENTARIO_VER_PROPIO,
            self::SOLICITUDES_CREAR,
            self::SOLICITUDES_VER_PROPIAS,
            self::PERFIL_VER,
            self::PERFIL_EDITAR,
        ],
    ];

    private function __construct()
    {
    }

    public static function roleHasPermission(
        string $role,
        string $permission
    ): bool {
        $permissions = self::ROLE_PERMISSIONS[$role]
            ?? [];

        if (in_array('*', $permissions, true)) {
            return true;
        }

        return in_array(
            $permission,
            $permissions,
            true
        );
    }
}