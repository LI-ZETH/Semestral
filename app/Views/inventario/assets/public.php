<?php
$stateClass = match ($asset['codigoEstado']) {
    'EN_INVENTARIO' => 'badge--active',
    'ASIGNADO' => 'badge--normal',
    'REVISION_TECNICA',
    'EN_REPARACION' => 'badge--warning',
    'DESCARTE',
    'DONADO' => 'badge--inactive',
    default => 'badge--role',
};

$updatedAt = !empty($asset['fechaActualizacion'])
    ? strtotime((string) $asset['fechaActualizacion'])
    : false;
?>

<section class="public-asset-sheet">
    <header class="public-asset-sheet__header">
        <div>
            <span class="section-heading__eyebrow">
                Verificación TrackiT
            </span>

            <h1><?= e($asset['codigoActivo']) ?></h1>

            <p>
                Registro válido dentro del inventario tecnológico.
            </p>
        </div>

        <span class="badge <?= e($stateClass) ?> public-asset-state">
            <?= e($asset['nombreEstado']) ?>
        </span>
    </header>

    <div class="public-asset-sheet__content">
        <div class="public-asset-gallery">
            <?php if ($images === []): ?>
                <div class="empty-state">
                    <p>No hay imágenes públicas disponibles.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($images as $index => $image): ?>
                <button
                    class="asset-gallery-item"
                    type="button"
                    data-gallery-image
                    data-image-src="<?= e(asset_url($image['rutaImagen'])) ?>"
                    data-image-alt="<?= e(
                        'Imagen ' . ($index + 1)
                        . ' de ' . $asset['codigoActivo']
                    ) ?>"
                >
                    <img
                        src="<?= e(asset_url($image['rutaImagen'])) ?>"
                        alt="<?= e(
                            'Imagen ' . ($index + 1)
                            . ' de ' . $asset['codigoActivo']
                        ) ?>"
                    >

                    <span>
                        <?= (bool) $image['esPrincipal']
                            ? 'Imagen principal'
                            : 'Ampliar imagen' ?>
                    </span>
                </button>
            <?php endforeach; ?>
        </div>

        <article class="public-asset-information">
            <span class="section-heading__eyebrow">
                Datos públicos
            </span>

            <h2><?= e($asset['nombreProducto']) ?></h2>

            <p class="public-asset-model">
                <?= e(
                    trim(
                        (string) ($asset['marca'] ?? '')
                        . ' '
                        . (string) ($asset['modelo'] ?? '')
                    ) ?: 'Marca y modelo no definidos'
                ) ?>
            </p>

            <dl class="asset-information-grid public-asset-information-grid">
                <div>
                    <dt>Categoría</dt>
                    <dd><?= e($asset['nombreCategoria']) ?></dd>
                </div>

                <div>
                    <dt>Subcategoría</dt>
                    <dd><?= e($asset['nombreSubcategoria']) ?></dd>
                </div>

                <div>
                    <dt>Tipo de producto</dt>
                    <dd><?= e($asset['tipoProducto']) ?></dd>
                </div>

                <div>
                    <dt>Fecha de ingreso</dt>
                    <dd>
                        <?= !empty($asset['fechaIngreso'])
                            ? e(
                                date(
                                    'd/m/Y',
                                    strtotime(
                                        (string) $asset['fechaIngreso']
                                    )
                                )
                            )
                            : 'No registrada' ?>
                    </dd>
                </div>

                <div>
                    <dt>Estado del registro</dt>
                    <dd>
                        <?= (bool) $asset['activo']
                            ? 'Activo'
                            : 'Inactivo' ?>
                    </dd>
                </div>

                <div>
                    <dt>Última verificación</dt>
                    <dd>
                        <?= $updatedAt !== false
                            ? e(date('d/m/Y H:i', $updatedAt))
                            : 'No registrada' ?>
                    </dd>
                </div>
            </dl>

            <div class="public-asset-privacy">
                <strong>Información protegida</strong>
                <p>
                    Por seguridad, esta ficha no muestra número de serie,
                    dirección IP, costo, ubicación exacta, custodio ni
                    historial interno.
                </p>
            </div>

            <?php if (\App\Core\Auth::can(\App\Core\Permissions::INVENTARIO_VER_TODO)): ?>
                <a
                    class="button"
                    href="<?= e(
                        base_url(
                            'inventario/activos/ver?id='
                            . $asset['idActivo']
                        )
                    ) ?>"
                >
                    Abrir ficha interna
                </a>
            <?php elseif (\App\Core\Auth::check()): ?>
                <a
                    class="button button--secondary"
                    href="<?= e(base_url('panel')) ?>"
                >
                    Volver al panel
                </a>
            <?php else: ?>
                <a
                    class="button button--secondary"
                    href="<?= e(base_url('login')) ?>"
                >
                    Acceso del personal
                </a>
            <?php endif; ?>
        </article>
    </div>
</section>

<dialog class="asset-gallery-dialog" data-gallery-dialog>
    <button
        class="asset-gallery-dialog__close"
        type="button"
        aria-label="Cerrar imagen"
        data-dialog-close
    >
        ×
    </button>

    <img src="" alt="" data-dialog-image>
</dialog>

<script
    src="<?= e(asset_url('assets/js/asset-detail.js')) ?>"
    defer
></script>
