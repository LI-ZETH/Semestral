<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Core\Router;

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