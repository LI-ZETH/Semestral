<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $connection = null;

    private function __construct()
    {
    }

    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $configPath = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'config'
            . DIRECTORY_SEPARATOR
            . 'database.php';

        if (!is_file($configPath)) {
            throw new RuntimeException(
                'No existe el archivo config/database.php.'
            );
        }

        $config = require $configPath;

        self::validateConfiguration($config);

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            self::$connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            return self::$connection;
        } catch (PDOException $exception) {
            throw new RuntimeException(
                'No se pudo establecer conexión con la base de datos.',
                0,
                $exception
            );
        }
    }

    private static function validateConfiguration(array $config): void
    {
        $requiredFields = [
            'host',
            'port',
            'database',
            'username',
            'password',
            'charset',
        ];

        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $config)) {
                throw new RuntimeException(
                    "Falta la configuración de base de datos: {$field}."
                );
            }
        }
    }
}