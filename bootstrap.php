<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Constantes principales
|--------------------------------------------------------------------------
*/

define('BASE_PATH', __DIR__);
define('APP_NAME', 'Tránsito CMDB');

/*
|--------------------------------------------------------------------------
| Autocargador de clases
|--------------------------------------------------------------------------
|
| App\Core\Database se convierte en:
| app/Core/Database.php
|
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
        'No se encontró app/Helpers/functions.php.'
    );
}

require_once $helpersPath;