<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Core\Router;
use App\Controllers\DashboardController;
use App\Controllers\UsuarioController;

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