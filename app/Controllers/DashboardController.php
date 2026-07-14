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
                    'status' => 'Siguiente módulo',
                ],
                [
                    'number' => '02',
                    'title' => 'Inventario general',
                    'description' =>
                        'Consultar categorías, productos, activos, '
                        . 'cantidad, estado y custodio actual.',
                    'status' => 'En preparación',
                ],
                [
                    'number' => '03',
                    'title' => 'Asignaciones',
                    'description' =>
                        'Consultar quién posee cada equipo y '
                        . 'el historial de entregas y devoluciones.',
                    'status' => 'En preparación',
                ],
            ],

            Roles::TECNICO => [
                [
                    'number' => '01',
                    'title' => 'Reparaciones',
                    'description' =>
                        'Consultar equipos que necesitan revisión '
                        . 'o reparación y actualizar su estado.',
                    'status' => 'En preparación',
                ],
                [
                    'number' => '02',
                    'title' => 'Ubicación del solicitante',
                    'description' =>
                        'Consultar edificio, piso y oficina de '
                        . 'la persona que registró la solicitud.',
                    'status' => 'En preparación',
                ],
                [
                    'number' => '03',
                    'title' => 'Inventario técnico',
                    'description' =>
                        'Consultar información de los equipos '
                        . 'requeridos durante el proceso técnico.',
                    'status' => 'En preparación',
                ],
            ],

            Roles::COLABORADOR => [
                [
                    'number' => '01',
                    'title' => 'Mis equipos',
                    'description' =>
                        'Consultar todos los activos que se '
                        . 'encuentran actualmente bajo tu custodia.',
                    'status' => 'En preparación',
                ],
                [
                    'number' => '02',
                    'title' => 'Solicitudes',
                    'description' =>
                        'Solicitar un equipo nuevo o registrar '
                        . 'una necesidad de reparación.',
                    'status' => 'En preparación',
                ],
                [
                    'number' => '03',
                    'title' => 'Mi información',
                    'description' =>
                        'Consultar y actualizar tus datos '
                        . 'personales y tu ubicación.',
                    'status' => 'En preparación',
                ],
            ],

            default => [],
        };
    }
}