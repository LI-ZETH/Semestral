<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Roles;

final class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireAuth();

        $role = Auth::role();

        $this->view(
            'dashboard/index',
            [
                'title' => 'Panel principal',
                'user' => Auth::user(),
                'role' => $role,
                'modules' => $this->getModules($role),
                'success' => flash('success'),
            ]
        );
    }

    private function getModules(?string $role): array
    {
        return match ($role) {
            Roles::ADMINISTRADOR => [
                [
                    'number' => '01',
                    'title' => 'Usuarios',
                    'description' =>
                        'Registrar, consultar, editar, activar, '
                        . 'desactivar y desbloquear cuentas.',
                    'status' => 'Abrir módulo',
                    'url' => base_url('usuarios'),
                ],
                [
                    'number' => '02',
                    'title' => 'Inventario general',
                    'description' =>
                        'Consultar categorías, productos, activos, '
                        . 'cantidad, estado y custodio actual.',
                    'status' => 'Abrir módulo',
                    'url' => base_url('inventario'),
                ],
                [
                    'number' => '03',
                    'title' => 'Asignaciones',
                    'description' =>
                        'Consultar quién posee cada equipo y el '
                        . 'historial de entregas y devoluciones.',
                    'status' => 'Abrir módulo',
                    'url' => base_url('asignaciones'),
                ],
                [
                    'number' => '04',
                    'title' => 'Solicitudes y reparaciones',
                    'description' =>
                        'Revisar necesidades, asignar reportes a técnicos '
                        . 'y supervisar reparaciones.',
                    'status' => 'Abrir módulo',
                    'url' => base_url('solicitudes/administrar'),
                ],
                [
                    'number' => '05',
                    'title' => 'Reportes y auditoría',
                    'description' =>
                        'Consultar estadísticas, depreciación, presupuestos, '
                        . 'movimientos, accesos y la bitácora firmada.',
                    'status' => 'Abrir módulo',
                    'url' => base_url('reportes'),
                ],
                [
                    'number' => '06',
                    'title' => 'Licencias de software',
                    'description' =>
                        'Controlar proveedores, puestos, vencimientos, '
                        . 'renovaciones y asignaciones de software.',
                    'status' => 'Abrir módulo',
                    'url' => base_url('licencias'),
                ],
                [
                    'number' => '07',
                    'title' => 'Bajas de activos',
                    'description' =>
                        'Formalizar descartes y donaciones conservando '
                        . 'la trazabilidad completa del inventario.',
                    'status' => 'Abrir módulo',
                    'url' => base_url('bajas'),
                ],
            ],

            Roles::TECNICO => [
                [
                    'number' => '01',
                    'title' => 'Reparaciones',
                    'description' =>
                        'Consultar equipos que necesitan revisión '
                        . 'o reparación y actualizar su estado.',
                    'status' => 'Abrir módulo',
                    'url' => base_url('reparaciones'),
                ],
                [
                    'number' => '02',
                    'title' => 'Ubicación del solicitante',
                    'description' =>
                        'Consultar edificio, piso y oficina de '
                        . 'la persona que registró la solicitud.',
                    'status' => 'Abrir módulo',
                    'url' => base_url('reparaciones'),
                ],
                [
                    'number' => '03',
                    'title' => 'Inventario técnico',
                    'description' =>
                        'Consultar información de los equipos '
                        . 'durante el proceso técnico.',
                    'status' => 'Abrir módulo',
                    'url' => base_url('inventario'),
                ],
            ],

            Roles::COLABORADOR => [
                [
                    'number' => '01',
                    'title' => 'Mis equipos',
                    'description' =>
                        'Consultar los activos que se encuentran '
                        . 'actualmente bajo tu custodia.',
                    'status' => 'Abrir módulo',
                    'url' => base_url('mis-equipos'),
                ],
                [
                    'number' => '02',
                    'title' => 'Solicitudes',
                    'description' =>
                        'Solicitar un equipo nuevo o registrar '
                        . 'una necesidad de reparación.',
                    'status' => 'Abrir módulo',
                    'url' => base_url('solicitudes'),
                ],
                [
                    'number' => '03',
                    'title' => 'Mi información',
                    'description' =>
                        'Consultar y actualizar tus datos '
                        . 'personales y ubicación.',
                    'status' => 'Abrir módulo',
                    'url' => base_url('perfil'),
                ],
                [
                    'number' => '04',
                    'title' => 'Mis licencias',
                    'description' =>
                        'Consultar el software y los puestos de licencia '
                        . 'asignados a tu cuenta.',
                    'status' => 'Abrir módulo',
                    'url' => base_url('mis-licencias'),
                ],
            ],

            default => [],
        };
    }
}