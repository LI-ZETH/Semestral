<?php

declare(strict_types=1);

use App\Services\Crypto\KeyPairGenerator;

require_once dirname(__DIR__) . '/bootstrap.php';

$configurationPath = BASE_PATH
    . DIRECTORY_SEPARATOR
    . 'config'
    . DIRECTORY_SEPARATOR
    . 'crypto.php';

if (!is_file($configurationPath)) {
    fwrite(
        STDERR,
        'No existe config/crypto.php.'
        . PHP_EOL
    );

    exit(1);
}

$configuration = require $configurationPath;

try {
    $generator = new KeyPairGenerator(
        (int) $configuration['key_bits'],
        (string) $configuration['digest_algorithm'],
        trim(
        (string) $configuration['openssl_config_path']
    )
    );

    $generator->generate(
        (string) $configuration['private_key_path'],
        (string) $configuration['public_key_path'],
        (string) $configuration['private_key_passphrase']
    );

    fwrite(
        STDOUT,
        'Par de llaves RSA generado correctamente.'
        . PHP_EOL
    );

    fwrite(
        STDOUT,
        'Llave privada: '
        . $configuration['private_key_path']
        . PHP_EOL
    );

    fwrite(
        STDOUT,
        'Llave pública: '
        . $configuration['public_key_path']
        . PHP_EOL
    );
} catch (Throwable $exception) {
    fwrite(
        STDERR,
        'Error: '
        . $exception->getMessage()
        . PHP_EOL
    );

    exit(1);
}