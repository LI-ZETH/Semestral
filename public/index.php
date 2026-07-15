<?php

declare(strict_types=1);

use App\Core\AuditTrail;
use App\Core\Csrf;
use App\Core\Router;
use App\Core\View;

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

/*
|--------------------------------------------------------------------------
| Protección global CSRF
|--------------------------------------------------------------------------
|
| Toda solicitud POST, PUT, PATCH o DELETE deberá incluir un token válido.
|
*/

if (!Csrf::validateRequest()) {
    http_response_code(419);

    View::render(
        'errors/419',
        [
            'title' => 'Sesión de formulario expirada',
        ]
    );

    exit;
}

AuditTrail::scheduleFromRequest(
    $requestMethod,
    $requestUri,
    $_POST
);

$router->dispatch(
    $requestMethod,
    $requestUri
);