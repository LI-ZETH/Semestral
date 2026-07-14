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
    '/asignaciones',
    [
        ModuloController::class,
        'assignments',
    ]
);

$router->get(
    '/reparaciones',
    [
        ModuloController::class,
        'repairs',
    ]
);

$router->get(
    '/mis-equipos',
    [
        ModuloController::class,
        'myEquipment',
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
    '/perfil',
    [
        ModuloController::class,
        'profile',
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