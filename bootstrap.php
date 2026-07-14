<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Ruta principal
|--------------------------------------------------------------------------
*/

define('BASE_PATH', __DIR__);

/*
|--------------------------------------------------------------------------
| Configuración de la aplicación
|--------------------------------------------------------------------------
*/

$appConfigPath = BASE_PATH
    . DIRECTORY_SEPARATOR
    . 'config'
    . DIRECTORY_SEPARATOR
    . 'app.php';

if (!is_file($appConfigPath)) {
    throw new RuntimeException(
        'No existe el archivo config/app.php.'
    );
}

$appConfig = require $appConfigPath;

define(
    'APP_NAME',
    (string) ($appConfig['name'] ?? 'Tránsito CMDB')
);

define(
    'APP_DEBUG',
    (bool) ($appConfig['debug'] ?? false)
);

date_default_timezone_set(
    (string) ($appConfig['timezone'] ?? 'America/Panama')
);

/*
|--------------------------------------------------------------------------
| Autocargador
|--------------------------------------------------------------------------
*/

spl_autoload_register(
    static function (string $className): void {
        $namespaceBase = 'App\\';

        if (!str_starts_with($className, $namespaceBase)) {
            return;
        }

        $relativeClass = substr(
            $className,
            strlen($namespaceBase)
        );

        $relativePath = str_replace(
            '\\',
            DIRECTORY_SEPARATOR,
            $relativeClass
        );

        $filePath = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'app'
            . DIRECTORY_SEPARATOR
            . $relativePath
            . '.php';

        if (is_file($filePath)) {
            require_once $filePath;
        }
    }
);

/*
|--------------------------------------------------------------------------
| Funciones auxiliares
|--------------------------------------------------------------------------
*/

$helpersPath = BASE_PATH
    . DIRECTORY_SEPARATOR
    . 'app'
    . DIRECTORY_SEPARATOR
    . 'Helpers'
    . DIRECTORY_SEPARATOR
    . 'functions.php';

if (!is_file($helpersPath)) {
    throw new RuntimeException(
        'No existe app/Helpers/functions.php.'
    );
}

require_once $helpersPath;

/*
|--------------------------------------------------------------------------
| Control global de errores
|--------------------------------------------------------------------------
*/

App\Core\ErrorHandler::register();