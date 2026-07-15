<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Core\Database;

$checks = [];

$checks[] = [
    'PHP 8.1 o superior',
    version_compare(PHP_VERSION, '8.1.0', '>='),
    PHP_VERSION,
];

foreach (['pdo_mysql', 'openssl', 'fileinfo', 'mbstring'] as $extension) {
    $checks[] = [
        "Extensión {$extension}",
        extension_loaded($extension),
        extension_loaded($extension) ? 'habilitada' : 'no habilitada',
    ];
}

$requiredFiles = [
    'config/database.php',
    'config/crypto.php',
    'storage/keys/private.pem',
    'storage/keys/public.pem',
];

foreach ($requiredFiles as $relativePath) {
    $absolutePath = BASE_PATH
        . DIRECTORY_SEPARATOR
        . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

    $checks[] = [
        $relativePath,
        is_file($absolutePath),
        is_file($absolutePath) ? 'listo' : 'faltante',
    ];
}

try {
    Database::getConnection();
    $checks[] = [
        'Conexión con la base Inventario',
        true,
        'correcta',
    ];
} catch (Throwable $exception) {
    $checks[] = [
        'Conexión con la base Inventario',
        false,
        $exception->getMessage(),
    ];
}

$failed = false;

echo PHP_EOL;
echo 'TrackiT - Diagnóstico de instalación' . PHP_EOL;
echo str_repeat('=', 42) . PHP_EOL;

foreach ($checks as [$label, $valid, $detail]) {
    $status = $valid ? '[OK]' : '[FALLO]';
    echo sprintf(
        "%-8s %-38s %s%s",
        $status,
        $label,
        $detail,
        PHP_EOL
    );

    if (!$valid) {
        $failed = true;
    }
}

echo PHP_EOL;

if ($failed) {
    echo 'Corrige los elementos marcados y vuelve a ejecutar:' . PHP_EOL;
    echo 'php scripts/setup_project.php' . PHP_EOL;
    exit(1);
}

echo 'La instalación está lista para usarse.' . PHP_EOL;
exit(0);
