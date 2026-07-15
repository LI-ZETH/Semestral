<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Router
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(
        string $path,
        mixed $handler
    ): void {
        $this->addRoute(
            'GET',
            $path,
            $handler
        );
    }

    public function post(
        string $path,
        mixed $handler
    ): void {
        $this->addRoute(
            'POST',
            $path,
            $handler
        );
    }

    private function addRoute(
        string $method,
        string $path,
        mixed $handler
    ): void {
        $normalizedPath = $this->normalizeRoutePath($path);

        if (!$this->isValidHandler($handler)) {
            throw new RuntimeException(
                "El manejador de la ruta {$normalizedPath} no es válido."
            );
        }

        $this->routes[$method][$normalizedPath] = $handler;
    }

    public function dispatch(
        string $requestMethod,
        string $requestUri
    ): void {
        $method = strtoupper($requestMethod);
        $path = $this->normalizeRequestPath($requestUri);

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            if ($this->pathExistsInAnotherMethod($path, $method)) {
                http_response_code(405);

                View::render(
                    'errors/405',
                    [
                        'title' => 'Método no permitido',
                        'path' => $path,
                    ]
                );

                return;
            }

            http_response_code(404);

            View::render(
                'errors/404',
                [
                    'title' => 'Página no encontrada',
                    'path' => $path,
                ]
            );

            return;
        }

        $this->executeHandler($handler);
    }

    private function executeHandler(mixed $handler): void
    {
        /*
         * Ejemplo:
         * [HomeController::class, 'index']
         */
        if (
            is_array($handler)
            && count($handler) === 2
            && is_string($handler[0])
            && is_string($handler[1])
        ) {
            $controllerClass = $handler[0];
            $method = $handler[1];

            if (!class_exists($controllerClass)) {
                throw new RuntimeException(
                    "El controlador {$controllerClass} no existe."
                );
            }

            $controller = new $controllerClass();

            if (!method_exists($controller, $method)) {
                throw new RuntimeException(
                    "El método {$method} no existe en el controlador."
                );
            }

            $controller->{$method}();

            return;
        }

        if (is_callable($handler)) {
            $handler();

            return;
        }

        throw new RuntimeException(
            'No fue posible ejecutar el manejador de la ruta.'
        );
    }

    private function isValidHandler(mixed $handler): bool
    {
        if (is_callable($handler)) {
            return true;
        }

        return is_array($handler)
            && count($handler) === 2
            && is_string($handler[0])
            && is_string($handler[1]);
    }

    private function normalizeRoutePath(string $path): string
    {
        $path = '/' . trim($path, '/');

        return $path === '/'
            ? '/'
            : rtrim($path, '/');
    }

    private function normalizeRequestPath(string $requestUri): string
    {
        $path = parse_url(
            $requestUri,
            PHP_URL_PATH
        );

        if (!is_string($path) || $path === '') {
            $path = '/';
        }

        $scriptName = str_replace(
            '\\',
            '/',
            $_SERVER['SCRIPT_NAME'] ?? ''
        );

        $basePath = str_replace(
            '\\',
            '/',
            dirname($scriptName)
        );

        if (
            $basePath !== '/'
            && $basePath !== '.'
            && $basePath !== ''
            && str_starts_with($path, $basePath)
        ) {
            $path = substr(
                $path,
                strlen($basePath)
            );
        }

        return $this->normalizeRoutePath($path);
    }

    private function pathExistsInAnotherMethod(
        string $path,
        string $currentMethod
    ): bool {
        foreach ($this->routes as $method => $routes) {
            if ($method === $currentMethod) {
                continue;
            }

            if (array_key_exists($path, $routes)) {
                return true;
            }
        }

        return false;
    }
}