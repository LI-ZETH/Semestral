<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Core\Router;
use App\Controllers\DashboardController;
use App\Controllers\UsuarioController;
use App\Controllers\InventarioController;
use App\Controllers\ModuloController;
use App\Controllers\CategoriaController;
use App\Controllers\SubcategoriaController;
use App\Controllers\ProductoController;
use App\Controllers\ActivoController;
use App\Controllers\AsignacionController;
use App\Controllers\UbicacionController;
use App\Controllers\SolicitudController;
use App\Controllers\ReparacionController;
use App\Controllers\PerfilController;

/**
 * @var Router $router
 */

$router->get(
    '/',
    [
        HomeController::class,
        'index',
    ]
);

$router->get(
    '/inicio',
    [
        HomeController::class,
        'index',
    ]
);

$router->get(
    '/configuracion/primer-administrador',
    [
        AuthController::class,
        'showInitialAdministrator',
    ]
);

$router->post(
    '/configuracion/primer-administrador',
    [
        AuthController::class,
        'storeInitialAdministrator',
    ]
);

$router->get(
    '/login',
    [
        AuthController::class,
        'showLogin',
    ]
);

$router->post(
    '/login',
    [
        AuthController::class,
        'login',
    ]
);

$router->post(
    '/logout',
    [
        AuthController::class,
        'logout',
    ]
);

$router->get(
    '/panel',
    [
        DashboardController::class,
        'index',
    ]
);

$router->get(
    '/usuarios',
    [
        UsuarioController::class,
        'index',
    ]
);

$router->get(
    '/usuarios/crear',
    [
        UsuarioController::class,
        'create',
    ]
);

$router->post(
    '/usuarios/guardar',
    [
        UsuarioController::class,
        'store',
    ]
);

$router->get(
    '/usuarios/editar',
    [
        UsuarioController::class,
        'edit',
    ]
);

$router->post(
    '/usuarios/actualizar',
    [
        UsuarioController::class,
        'update',
    ]
);

$router->post(
    '/usuarios/estado',
    [
        UsuarioController::class,
        'changeState',
    ]
);

$router->post(
    '/usuarios/desbloquear',
    [
        UsuarioController::class,
        'unlock',
    ]
);

$router->get(
    '/inventario',
    [
        InventarioController::class,
        'index',
    ]
);

$router->get(
    '/inventario/categoria',
    [
        InventarioController::class,
        'category',
    ]
);

$router->get(
    '/inventario/producto',
    [
        InventarioController::class,
        'product',
    ]
);

$router->get(
    '/solicitudes',
    [
        ModuloController::class,
        'requests',
    ]
);

$router->get(
    '/inventario/categorias',
    [
        CategoriaController::class,
        'index',
    ]
);

$router->get(
    '/inventario/categorias/crear',
    [
        CategoriaController::class,
        'create',
    ]
);

$router->post(
    '/inventario/categorias/guardar',
    [
        CategoriaController::class,
        'store',
    ]
);

$router->get(
    '/inventario/categorias/editar',
    [
        CategoriaController::class,
        'edit',
    ]
);

$router->post(
    '/inventario/categorias/actualizar',
    [
        CategoriaController::class,
        'update',
    ]
);

$router->post(
    '/inventario/categorias/estado',
    [
        CategoriaController::class,
        'changeState',
    ]
);

$router->get(
    '/inventario/subcategoria',
    [
        InventarioController::class,
        'subcategory',
    ]
);

$router->get(
    '/inventario/subcategorias',
    [
        SubcategoriaController::class,
        'index',
    ]
);

$router->get(
    '/inventario/subcategorias/crear',
    [
        SubcategoriaController::class,
        'create',
    ]
);

$router->post(
    '/inventario/subcategorias/guardar',
    [
        SubcategoriaController::class,
        'store',
    ]
);

$router->get(
    '/inventario/subcategorias/editar',
    [
        SubcategoriaController::class,
        'edit',
    ]
);

$router->post(
    '/inventario/subcategorias/actualizar',
    [
        SubcategoriaController::class,
        'update',
    ]
);

$router->post(
    '/inventario/subcategorias/estado',
    [
        SubcategoriaController::class,
        'changeState',
    ]
);

$router->get(
    '/inventario/productos',
    [ProductoController::class, 'index']
);

$router->get(
    '/inventario/productos/crear',
    [ProductoController::class, 'create']
);

$router->post(
    '/inventario/productos/guardar',
    [ProductoController::class, 'store']
);

$router->get(
    '/inventario/productos/editar',
    [ProductoController::class, 'edit']
);

$router->post(
    '/inventario/productos/actualizar',
    [ProductoController::class, 'update']
);

$router->post(
    '/inventario/productos/estado',
    [ProductoController::class, 'changeState']
);

$router->get(
    '/inventario/activos',
    [
        ActivoController::class,
        'index',
    ]
);

$router->get(
    '/inventario/activos/crear',
    [
        ActivoController::class,
        'create',
    ]
);

$router->post(
    '/inventario/activos/guardar',
    [
        ActivoController::class,
        'store',
    ]
);

$router->get(
    '/inventario/activos/editar',
    [
        ActivoController::class,
        'edit',
    ]
);

$router->post(
    '/inventario/activos/actualizar',
    [
        ActivoController::class,
        'update',
    ]
);

$router->post(
    '/inventario/activos/estado',
    [
        ActivoController::class,
        'changeState',
    ]
);

$router->get(
    '/asignaciones',
    [
        AsignacionController::class,
        'index',
    ]
);

$router->get(
    '/asignaciones/crear',
    [
        AsignacionController::class,
        'create',
    ]
);

$router->post(
    '/asignaciones/guardar',
    [
        AsignacionController::class,
        'store',
    ]
);

$router->get(
    '/asignaciones/devolver',
    [
        AsignacionController::class,
        'returnForm',
    ]
);

$router->post(
    '/asignaciones/devolver',
    [
        AsignacionController::class,
        'storeReturn',
    ]
);

$router->get(
    '/mis-equipos',
    [
        AsignacionController::class,
        'myEquipment',
    ]
);

$router->get(
    '/ubicaciones',
    [
        UbicacionController::class,
        'index',
    ]
);

$router->get(
    '/ubicaciones/crear',
    [
        UbicacionController::class,
        'create',
    ]
);

$router->post(
    '/ubicaciones/guardar',
    [
        UbicacionController::class,
        'store',
    ]
);

$router->get(
    '/ubicaciones/editar',
    [
        UbicacionController::class,
        'edit',
    ]
);

$router->post(
    '/ubicaciones/actualizar',
    [
        UbicacionController::class,
        'update',
    ]
);

$router->post(
    '/ubicaciones/estado',
    [
        UbicacionController::class,
        'changeState',
    ]
);


$router->get(
    '/solicitudes',
    [SolicitudController::class, 'index']
);

$router->get(
    '/solicitudes/crear',
    [SolicitudController::class, 'create']
);

$router->post(
    '/solicitudes/guardar',
    [SolicitudController::class, 'store']
);

$router->get(
    '/solicitudes/reparacion/crear',
    [SolicitudController::class, 'repairCreate']
);

$router->post(
    '/solicitudes/reparacion/guardar',
    [SolicitudController::class, 'repairStore']
);

$router->post(
    '/solicitudes/cancelar',
    [SolicitudController::class, 'cancelNeed']
);

$router->post(
    '/solicitudes/reparacion/cancelar',
    [SolicitudController::class, 'cancelRepair']
);

$router->get(
    '/solicitudes/administrar',
    [SolicitudController::class, 'administration']
);

$router->get(
    '/solicitudes/revisar',
    [SolicitudController::class, 'review']
);

$router->post(
    '/solicitudes/revisar',
    [SolicitudController::class, 'updateReview']
);

$router->get(
    '/solicitudes/reparacion/asignar',
    [SolicitudController::class, 'assignRepair']
);

$router->post(
    '/solicitudes/reparacion/asignar',
    [SolicitudController::class, 'storeRepairAssignment']
);

$router->get(
    '/reparaciones',
    [ReparacionController::class, 'index']
);

$router->get(
    '/reparaciones/gestionar',
    [ReparacionController::class, 'manage']
);

$router->post(
    '/reparaciones/actualizar',
    [ReparacionController::class, 'update']
);

$router->get(
    '/perfil',
    [
        PerfilController::class,
        'show',
    ]
);

$router->get(
    '/perfil/editar',
    [
        PerfilController::class,
        'edit',
    ]
);

$router->post(
    '/perfil/actualizar',
    [
        PerfilController::class,
        'update',
    ]
);

$router->get(
    '/perfil/contrasena',
    [
        PerfilController::class,
        'passwordForm',
    ]
);

$router->post(
    '/perfil/contrasena',
    [
        PerfilController::class,
        'changePassword',
    ]
);