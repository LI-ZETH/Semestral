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
| Preparación automática de una instalación clonada
|--------------------------------------------------------------------------
|
| Los archivos database.php, crypto.php y las llaves RSA son locales y no
| deben subirse a Git. Cuando faltan, TrackiT recupera un respaldo local o
| genera una configuración segura a partir de los archivos de ejemplo.
|
*/

if (!function_exists('trackitEnsureDirectory')) {
    function trackitEnsureDirectory(string $directory): void
    {
        if (is_dir($directory)) {
            return;
        }

        if (
            !mkdir($directory, 0775, true)
            && !is_dir($directory)
        ) {
            throw new RuntimeException(
                "No fue posible crear el directorio: {$directory}"
            );
        }
    }
}

if (!function_exists('trackitCopyFirstAvailable')) {
    function trackitCopyFirstAvailable(
        string $destination,
        array $candidates
    ): bool {
        if (is_file($destination)) {
            return true;
        }

        foreach ($candidates as $candidate) {
            if (!is_string($candidate) || !is_file($candidate)) {
                continue;
            }

            trackitEnsureDirectory(dirname($destination));

            if (@copy($candidate, $destination)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('trackitDetectOpenSslConfig')) {
    function trackitDetectOpenSslConfig(): string
    {
        $environmentPath = getenv('OPENSSL_CONF');

        $candidates = [
            is_string($environmentPath) ? $environmentPath : '',
            'C:\\xampp\\apache\\conf\\openssl.cnf',
            'C:\\xampp\\php\\extras\\ssl\\openssl.cnf',
            dirname(PHP_BINARY)
                . DIRECTORY_SEPARATOR
                . 'extras'
                . DIRECTORY_SEPARATOR
                . 'ssl'
                . DIRECTORY_SEPARATOR
                . 'openssl.cnf',
            '/etc/ssl/openssl.cnf',
            '/usr/lib/ssl/openssl.cnf',
        ];

        foreach ($candidates as $candidate) {
            if ($candidate !== '' && is_file($candidate)) {
                return $candidate;
            }
        }

        return '';
    }
}

if (!function_exists('trackitWriteCryptoConfiguration')) {
    function trackitWriteCryptoConfiguration(string $path): void
    {
        trackitEnsureDirectory(dirname($path));

        $passphrase = bin2hex(random_bytes(32));
        $opensslConfigPath = trackitDetectOpenSslConfig();

        $content = "<?php\n\n"
            . "declare(strict_types=1);\n\n"
            . "return [\n"
            . "    'private_key_path' => BASE_PATH\n"
            . "        . DIRECTORY_SEPARATOR . 'storage'\n"
            . "        . DIRECTORY_SEPARATOR . 'keys'\n"
            . "        . DIRECTORY_SEPARATOR . 'private.pem',\n\n"
            . "    'public_key_path' => BASE_PATH\n"
            . "        . DIRECTORY_SEPARATOR . 'storage'\n"
            . "        . DIRECTORY_SEPARATOR . 'keys'\n"
            . "        . DIRECTORY_SEPARATOR . 'public.pem',\n\n"
            . "    'private_key_passphrase' => "
            . var_export($passphrase, true)
            . ",\n\n"
            . "    'openssl_config_path' => "
            . var_export($opensslConfigPath, true)
            . ",\n\n"
            . "    'key_bits' => 2048,\n"
            . "    'digest_algorithm' => 'sha256',\n"
            . "];\n";

        $temporaryPath = $path . '.tmp';

        if (
            file_put_contents(
                $temporaryPath,
                $content,
                LOCK_EX
            ) === false
        ) {
            throw new RuntimeException(
                'No fue posible crear config/crypto.php.'
            );
        }

        if (!@rename($temporaryPath, $path)) {
            @unlink($temporaryPath);

            throw new RuntimeException(
                'No fue posible activar config/crypto.php.'
            );
        }

        @chmod($path, 0600);
    }
}

if (!function_exists('trackitPrepareLocalInstallation')) {
    function trackitPrepareLocalInstallation(): void
    {
        $configDirectory = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'config';

        $keysDirectory = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'storage'
            . DIRECTORY_SEPARATOR
            . 'keys';

        $logsDirectory = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'storage'
            . DIRECTORY_SEPARATOR
            . 'logs';

        $qrDirectory = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'storage'
            . DIRECTORY_SEPARATOR
            . 'qrcodes';

        trackitEnsureDirectory($configDirectory);
        trackitEnsureDirectory($keysDirectory);
        trackitEnsureDirectory($logsDirectory);
        trackitEnsureDirectory($qrDirectory);

        $databaseConfig = $configDirectory
            . DIRECTORY_SEPARATOR
            . 'database.php';

        $databaseReady = trackitCopyFirstAvailable(
            $databaseConfig,
            [
                $configDirectory
                    . DIRECTORY_SEPARATOR
                    . 'database.respaldo.php',
                $configDirectory
                    . DIRECTORY_SEPARATOR
                    . 'database.example.php',
            ]
        );

        if (!$databaseReady) {
            throw new RuntimeException(
                'No fue posible preparar config/database.php. '
                . 'Verifica los permisos de la carpeta config.'
            );
        }

        $cryptoConfig = $configDirectory
            . DIRECTORY_SEPARATOR
            . 'crypto.php';

        if (!is_file($cryptoConfig)) {
            $restored = trackitCopyFirstAvailable(
                $cryptoConfig,
                [
                    $configDirectory
                        . DIRECTORY_SEPARATOR
                        . 'crypto.respaldo.php',
                ]
            );

            if (!$restored) {
                trackitWriteCryptoConfiguration($cryptoConfig);
            }
        }

        trackitCopyFirstAvailable(
            $keysDirectory
                . DIRECTORY_SEPARATOR
                . 'private.pem',
            [
                $keysDirectory
                    . DIRECTORY_SEPARATOR
                    . 'private.respaldo.pem',
            ]
        );

        trackitCopyFirstAvailable(
            $keysDirectory
                . DIRECTORY_SEPARATOR
                . 'public.pem',
            [
                $keysDirectory
                    . DIRECTORY_SEPARATOR
                    . 'public.respaldo.pem',
            ]
        );
    }
}

trackitPrepareLocalInstallation();

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
    (string) ($appConfig['name'] ?? 'TrackiT')
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
| Llaves RSA locales
|--------------------------------------------------------------------------
*/

if (!function_exists('trackitEnsureRsaKeys')) {
    function trackitEnsureRsaKeys(): void
    {
        $configurationPath = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'config'
            . DIRECTORY_SEPARATOR
            . 'crypto.php';

        if (!is_file($configurationPath)) {
            return;
        }

        $configuration = require $configurationPath;

        if (!is_array($configuration)) {
            return;
        }

        $privateKeyPath = (string) (
            $configuration['private_key_path'] ?? ''
        );

        $publicKeyPath = (string) (
            $configuration['public_key_path'] ?? ''
        );

        if (
            $privateKeyPath !== ''
            && $publicKeyPath !== ''
            && is_file($privateKeyPath)
            && is_file($publicKeyPath)
        ) {
            return;
        }

        $opensslConfigPath = (string) (
            $configuration['openssl_config_path'] ?? ''
        );

        if (
            $opensslConfigPath === ''
            || !is_file($opensslConfigPath)
        ) {
            $opensslConfigPath = trackitDetectOpenSslConfig();
        }

        if ($opensslConfigPath === '') {
            throw new RuntimeException(
                'No se encontró openssl.cnf para generar las llaves RSA.'
            );
        }

        $generator = new App\Services\Crypto\KeyPairGenerator(
            (int) ($configuration['key_bits'] ?? 2048),
            (string) (
                $configuration['digest_algorithm'] ?? 'sha256'
            ),
            $opensslConfigPath
        );

        $generator->generate(
            $privateKeyPath,
            $publicKeyPath,
            (string) (
                $configuration['private_key_passphrase'] ?? ''
            )
        );
    }
}

try {
    trackitEnsureRsaKeys();
} catch (Throwable $exception) {
    /*
     * La portada y el login pueden seguir funcionando. Los módulos que
     * requieren RSA mostrarán su propio mensaje si OpenSSL no está listo.
     */
    error_log(
        '[TRACKIT CONFIGURACION RSA] '
        . $exception->getMessage()
    );
}

/*
|--------------------------------------------------------------------------
| Servicios transversales
|--------------------------------------------------------------------------
*/

App\Core\ErrorHandler::register();
App\Core\SecurityHeaders::send();
App\Core\Session::start();
