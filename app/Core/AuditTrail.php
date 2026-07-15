<?php

declare(strict_types=1);

namespace App\Core;

use App\Services\AuditoriaService;
use Throwable;

final class AuditTrail
{
    private const REDACTED_VALUE = '[PROTEGIDO]';

    private function __construct()
    {
    }

    public static function scheduleFromRequest(
        string $requestMethod,
        string $requestUri,
        array $postData
    ): void {
        if (
            strtoupper($requestMethod) !== 'POST'
            || !Auth::check()
        ) {
            return;
        }

        $path = self::normalizePath($requestUri);

        if (in_array($path, ['/login', '/logout'], true)) {
            return;
        }

        $userId = Auth::id();

        if ($userId === null) {
            return;
        }

        $event = [
            'idUsuario' => $userId,
            'modulo' => self::resolveModule($path),
            'accion' => self::resolveAction($path),
            'tablaAfectada' => self::resolveTable($path),
            'idRegistro' => self::resolveRecordId($postData),
            'descripcion' =>
                'Solicitud confirmada mediante la ruta ' . $path . '.',
            'datosNuevos' => self::sanitizeData($postData),
            'direccionIP' => ClientInfo::ipAddress(),
            'userAgent' => ClientInfo::userAgent(),
        ];

        register_shutdown_function(
            static function () use ($event): void {
                $statusCode = http_response_code();

                if (
                    is_int($statusCode)
                    && $statusCode >= 400
                ) {
                    return;
                }

                $lastError = error_get_last();

                if (
                    is_array($lastError)
                    && in_array(
                        $lastError['type'] ?? null,
                        [
                            E_ERROR,
                            E_PARSE,
                            E_CORE_ERROR,
                            E_COMPILE_ERROR,
                            E_USER_ERROR,
                        ],
                        true
                    )
                ) {
                    return;
                }

                try {
                    AuditoriaService::build()
                        ->record($event);
                } catch (Throwable $exception) {
                    error_log(
                        '[AUDITORIA] '
                        . $exception->getMessage()
                    );
                }
            }
        );
    }

    public static function recordReadEvent(array $event): void
    {
        try {
            AuditoriaService::build()->record([
                ...$event,
                'idUsuario' => $event['idUsuario']
                    ?? Auth::id(),
                'direccionIP' => $event['direccionIP']
                    ?? ClientInfo::ipAddress(),
                'userAgent' => $event['userAgent']
                    ?? ClientInfo::userAgent(),
            ]);
        } catch (Throwable $exception) {
            error_log(
                '[AUDITORIA] '
                . $exception->getMessage()
            );
        }
    }

    private static function normalizePath(string $requestUri): string
    {
        $path = parse_url(
            $requestUri,
            PHP_URL_PATH
        );

        if (!is_string($path) || $path === '') {
            return '/';
        }

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

        if (
            $basePath !== '/'
            && $basePath !== '.'
            && $basePath !== ''
            && str_starts_with($path, $basePath)
        ) {
            $path = substr(
                $path,
                strlen($basePath)
            );
        }

        $path = '/' . trim($path, '/');

        return $path === '/'
            ? '/'
            : rtrim($path, '/');
    }

    private static function resolveModule(string $path): string
    {
        $segments = array_values(
            array_filter(
                explode('/', trim($path, '/'))
            )
        );

        if ($segments === []) {
            return 'SISTEMA';
        }

        return strtoupper(
            str_replace(
                '-',
                '_',
                (string) $segments[0]
            )
        );
    }

    private static function resolveAction(string $path): string
    {
        $segments = array_values(
            array_filter(
                explode('/', trim($path, '/'))
            )
        );

        $lastSegment = strtolower(
            (string) end($segments)
        );

        return match ($lastSegment) {
            'guardar' => 'CREAR',
            'actualizar' => 'ACTUALIZAR',
            'estado' => 'CAMBIAR_ESTADO',
            'desbloquear' => 'DESBLOQUEAR',
            'devolver' => 'DEVOLVER',
            'cancelar' => 'CANCELAR',
            'asignar' => 'ASIGNAR',
            'contrasena' => 'CAMBIAR_CONTRASENA',
            'revisar' => 'REVISAR',
            default => strtoupper(
                str_replace('-', '_', $lastSegment)
            ),
        };
    }

    private static function resolveTable(string $path): ?string
    {
        return match (true) {
            str_starts_with($path, '/usuarios') => 'Usuario',
            str_starts_with($path, '/inventario/categorias') => 'Categoria',
            str_starts_with($path, '/inventario/subcategorias') => 'Subcategoria',
            str_starts_with($path, '/inventario/productos') => 'Producto',
            str_starts_with($path, '/inventario/activos') => 'Activo',
            str_starts_with($path, '/asignaciones/devolver') => 'DevolucionActivo',
            str_starts_with($path, '/asignaciones') => 'AsignacionActivo',
            str_starts_with($path, '/ubicaciones') => 'Ubicacion',
            str_starts_with($path, '/solicitudes/reparacion') => 'SolicitudReparacion',
            str_starts_with($path, '/solicitudes') => 'SolicitudNecesidad',
            str_starts_with($path, '/reparaciones') => 'Reparacion',
            str_starts_with($path, '/perfil') => 'Usuario',
            default => null,
        };
    }

    private static function resolveRecordId(
        array $postData
    ): ?string {
        $possibleFields = [
            'idAuditoria',
            'idUsuario',
            'idCategoria',
            'idSubcategoria',
            'idProducto',
            'idActivo',
            'idAsignacion',
            'idUbicacion',
            'idSolicitud',
            'idSolicitudReparacion',
            'idReparacion',
        ];

        foreach ($possibleFields as $field) {
            if (!isset($postData[$field])) {
                continue;
            }

            $value = trim((string) $postData[$field]);

            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }

    private static function sanitizeData(
        array $data
    ): array {
        $sanitized = [];

        foreach ($data as $key => $value) {
            $normalizedKey = strtolower((string) $key);

            if ($normalizedKey === '_token') {
                continue;
            }

            if (self::isSensitiveKey($normalizedKey)) {
                $sanitized[$key] = self::REDACTED_VALUE;
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = self::sanitizeData($value);
                continue;
            }

            $stringValue = trim((string) $value);

            $sanitized[$key] = mb_substr(
                $stringValue,
                0,
                1500
            );
        }

        return $sanitized;
    }

    private static function isSensitiveKey(
        string $key
    ): bool {
        foreach (
            [
                'password',
                'contrasena',
                'contraseña',
                'secret',
                'token',
                'llave',
                'firma',
                'clave',
            ] as $sensitiveTerm
        ) {
            if (str_contains($key, $sensitiveTerm)) {
                return true;
            }
        }

        return false;
    }
}
