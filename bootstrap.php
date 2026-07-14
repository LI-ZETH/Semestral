<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Ruta principal del proyecto
|--------------------------------------------------------------------------
*/

define('BASE_PATH', __DIR__);

/*
|--------------------------------------------------------------------------
| Autocargador de clases
|--------------------------------------------------------------------------
|
| Convierte una clase como:
| App\Core\Database
|
| En el archivo:
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