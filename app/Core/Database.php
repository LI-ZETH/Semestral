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

        [$config, $source] = self::loadConfiguration();

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
                (string) $config['username'],
                (string) $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            return self::$connection;
        } catch (PDOException $exception) {
            throw new RuntimeException(
                'No se pudo conectar con la base de datos "'
                . (string) $config['database']
                . '" usando '
                . basename($source)
                . '. Verifica que MySQL esté iniciado, que hayas '
                . 'importado database/inventario.sql y que las '
                . 'credenciales locales sean correctas.',
                0,
                $exception
            );
        }
    }

    /**
     * @return array{0: array, 1: string}
     */
    private static function loadConfiguration(): array
    {
        $configDirectory = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'config';

        $candidates = [
            $configDirectory
                . DIRECTORY_SEPARATOR
                . 'database.php',
            $configDirectory
                . DIRECTORY_SEPARATOR
                . 'database.respaldo.php',
            $configDirectory
                . DIRECTORY_SEPARATOR
                . 'database.example.php',
        ];

        foreach ($candidates as $candidate) {
            if (!is_file($candidate)) {
                continue;
            }

            $config = require $candidate;

            if (!is_array($config)) {
                throw new RuntimeException(
                    basename($candidate)
                    . ' debe devolver un arreglo de configuración.'
                );
            }

            return [$config, $candidate];
        }

        throw new RuntimeException(
            'No existe una configuración de base de datos. '
            . 'Se esperaba config/database.php o '
            . 'config/database.example.php.'
        );
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
