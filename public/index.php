<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Core\Database;

try {
    $connection = Database::getConnection();

    $databaseName = $connection
        ->query('SELECT DATABASE()')
        ->fetchColumn();

    $tableCount = $connection
        ->query(
            "
            SELECT COUNT(*)
            FROM information_schema.tables
            WHERE table_schema = DATABASE()
            "
        )
        ->fetchColumn();
} catch (Throwable $exception) {
    http_response_code(500);

    $databaseName = null;
    $tableCount = 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tránsito CMDB</title>
</head>

<body>
    <main>
        <h1>Tránsito CMDB</h1>

        <?php if ($databaseName !== null): ?>
            <h2>Conexión establecida correctamente</h2>

            <p>
                Base de datos:
                <strong>
                    <?= htmlspecialchars(
                        (string) $databaseName,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>
            </p>

            <p>
                Tablas y vistas encontradas:
                <strong>
                    <?= (int) $tableCount ?>
                </strong>
            </p>
        <?php else: ?>
            <h2>No fue posible conectar con la base de datos</h2>

            <p>
                Revisa XAMPP y el archivo
                <code>config/database.php</code>.
            </p>
        <?php endif; ?>
    </main>
</body>
</html>