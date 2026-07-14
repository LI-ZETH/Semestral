<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Core\Router;
use App\Controllers\DashboardController;

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
    '/prueba-error',
    static function (): void {
        throw new RuntimeException(
            'Error intencional para comprobar el manejador.'
        );
    }
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