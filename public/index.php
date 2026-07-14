<?php

declare(strict_types=1);

use App\Core\Router;

require_once dirname(__DIR__) . '/bootstrap.php';

$router = new Router();

$routesPath = BASE_PATH
    . DIRECTORY_SEPARATOR
    . 'routes'
    . DIRECTORY_SEPARATOR
    . 'web.php';

if (!is_file($routesPath)) {
    throw new RuntimeException(
        'No se encontró el archivo routes/web.php.'
    );
}

require $routesPath;

/*
|--------------------------------------------------------------------------
| Despacho de la solicitud
|--------------------------------------------------------------------------
|
| El parámetro route sirve también como alternativa durante las pruebas:
| public/index.php?route=/inicio
|
*/

$requestUri = isset($_GET['route'])
    ? (string) $_GET['route']
    : (string) ($_SERVER['REQUEST_URI'] ?? '/');

$requestMethod = (string) (
    $_SERVER['REQUEST_METHOD'] ?? 'GET'
);

$router->dispatch(
    $requestMethod,
    $requestUri
);