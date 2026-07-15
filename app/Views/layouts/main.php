<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="Sistema de gestión de activos tecnológicos Tránsito CMDB"
    >

    <title>
        <?= e($title ?? APP_NAME) ?> | <?= e(APP_NAME) ?>
    </title>

    <link
        rel="stylesheet"
        href="<?= e(asset_url('assets/css/app.css')) ?>"
    >
</head>

<body>
    <?php
    require BASE_PATH
        . '/app/Views/layouts/header.php';
    ?>

    <main class="page-container">
        <?= $content ?>
    </main>

    <?php
    require BASE_PATH
        . '/app/Views/layouts/footer.php';
    ?>
</body>
</html>