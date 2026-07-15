<?php

declare(strict_types=1);

use App\Services\Crypto\CanonicalJson;
use App\Services\Crypto\PasswordHasherService;
use App\Services\Crypto\RsaSignatureService;

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
    /*
    |--------------------------------------------------------------------------
    | Prueba de contraseña
    |--------------------------------------------------------------------------
    */

    $passwordHasher = new PasswordHasherService();

    $plainPassword = 'PruebaSegura123!';

    $passwordHash = $passwordHasher->transformar(
        $plainPassword
    );

    $passwordIsValid = $passwordHasher->verificar(
        $plainPassword,
        $passwordHash
    );

    /*
    |--------------------------------------------------------------------------
    | Prueba de firma RSA
    |--------------------------------------------------------------------------
    */

    $rsaService = new RsaSignatureService(
        (string) $configuration['private_key_path'],
        (string) $configuration['public_key_path'],
        (string) $configuration['private_key_passphrase']
    );

    $auditData = [
        'accion' => 'REGISTRAR_ACTIVO',
        'idUsuario' => 1,
        'tabla' => 'Activo',
        'idRegistro' => 25,
        'datos' => [
            'codigoActivo' => 'TRK-LAP-000025',
            'estado' => 'EN_INVENTARIO',
        ],
    ];

    $payload = CanonicalJson::encode(
        $auditData
    );

    $signature = $rsaService->transformar(
        $payload
    );

    $validSignature = $rsaService->verificar(
        $payload,
        $signature
    );

    $modifiedData = $auditData;
    $modifiedData['accion'] = 'DATOS_MODIFICADOS';

    $modifiedPayload = CanonicalJson::encode(
        $modifiedData
    );

    $modifiedSignatureIsValid = $rsaService->verificar(
        $modifiedPayload,
        $signature
    );

    echo 'Contraseña válida: '
        . ($passwordIsValid ? 'SÍ' : 'NO')
        . PHP_EOL;

    echo 'Firma original válida: '
        . ($validSignature ? 'SÍ' : 'NO')
        . PHP_EOL;

    echo 'Firma después de modificar datos: '
        . ($modifiedSignatureIsValid ? 'SÍ' : 'NO')
        . PHP_EOL;

    echo 'Huella de la llave pública: '
        . $rsaService->getPublicKeyFingerprint()
        . PHP_EOL;
} catch (Throwable $exception) {
    fwrite(
        STDERR,
        'Error: '
        . $exception->getMessage()
        . PHP_EOL
    );

    exit(1);
}