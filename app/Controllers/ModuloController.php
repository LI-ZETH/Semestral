<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Permissions;
use App\Core\Roles;

final class ModuloController extends Controller
{
    public function assignments(): void
    {
        Auth::requireRole(
            Roles::ADMINISTRADOR
        );

        $this->renderPlaceholder(
            'Asignaciones',
            'Aquí se gestionarán las entregas, devoluciones '
            . 'y custodios de los activos.'
        );
    }

    public function repairs(): void
    {
        Auth::requireAnyRole([
            Roles::ADMINISTRADOR,
            Roles::TECNICO,
        ]);

        $this->renderPlaceholder(
            'Reparaciones',
            'Aquí aparecerán los equipos que necesitan '
            . 'revisión o reparación técnica.'
        );
    }

    public function myEquipment(): void
    {
        Auth::requirePermission(
            Permissions::INVENTARIO_VER_PROPIO
        );

        $this->renderPlaceholder(
            'Mis equipos',
            'Aquí podrás consultar los activos que se '
            . 'encuentran bajo tu custodia.'
        );
    }

    public function requests(): void
    {
        Auth::requirePermission(
            Permissions::SOLICITUDES_CREAR
        );

        $this->renderPlaceholder(
            'Solicitudes',
            'Aquí podrás solicitar un nuevo equipo o '
            . 'registrar una reparación.'
        );
    }

    public function profile(): void
    {
        Auth::requirePermission(
            Permissions::PERFIL_VER
        );

        $this->renderPlaceholder(
            'Mi información',
            'Aquí podrás consultar y actualizar tu '
            . 'información personal y ubicación.'
        );
    }

    private function renderPlaceholder(
        string $title,
        string $description
    ): void {
        $this->view(
            'modules/placeholder',
            [
                'title' => $title,
                'moduleTitle' => $title,
                'description' => $description,
            ]
        );
    }
}