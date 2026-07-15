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
        content="TrackiT: plataforma CMDB para inventario, asignaciones, licencias, reparaciones y trazabilidad de activos tecnológicos."
    >

    <title>
        <?= e($title ?? APP_NAME) ?> | <?= e(APP_NAME) ?>
    </title>

    <link
        rel="stylesheet"
        href="<?= e(asset_url('assets/css/public-site.css')) ?>"
    >

    <link
        rel="stylesheet"
        href="<?= e(asset_url('assets/css/clone-setup-patch.css')) ?>"
    >
</head>

<body class="public-site <?= e($bodyClass ?? '') ?>">
    <?php
    require BASE_PATH
        . '/app/Views/public/partials/header.php';
    ?>

    <main class="public-main">
        <?= $content ?>
    </main>

    <?php
    require BASE_PATH
        . '/app/Views/public/partials/footer.php';
    ?>
</body>
</html>
