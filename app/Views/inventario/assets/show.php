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

$formatDate = static function (mixed $value): string {
    if (empty($value)) {
        return 'No registrada';
    }

    $timestamp = strtotime((string) $value);

    return $timestamp !== false
        ? date('d/m/Y', $timestamp)
        : (string) $value;
};

$formatDateTime = static function (mixed $value): string {
    if (empty($value)) {
        return 'No registrada';
    }

    $timestamp = strtotime((string) $value);

    return $timestamp !== false
        ? date('d/m/Y H:i', $timestamp)
        : (string) $value;
};
?>

<section class="asset-detail-hero">
    <div class="asset-detail-hero__content">
        <span class="section-heading__eyebrow">
            <?= e($asset['nombreCategoria']) ?>
            ·
            <?= e($asset['nombreSubcategoria']) ?>
        </span>

        <div class="asset-detail-title-row">
            <div>
                <h1><?= e($asset['codigoActivo']) ?></h1>

                <p>
                    <?= e($asset['nombreProducto']) ?>
                    <?php if (
                        trim(
                            (string) ($asset['marca'] ?? '')
                            . ' '
                            . (string) ($asset['modelo'] ?? '')
                        ) !== ''
                    ): ?>
                        ·
                        <?= e(
                            trim(
                                (string) ($asset['marca'] ?? '')
                                . ' '
                                . (string) ($asset['modelo'] ?? '')
                            )
                        ) ?>
                    <?php endif; ?>
                </p>
            </div>

            <span class="badge <?= e($stateClass) ?> asset-detail-state">
                <?= e($asset['nombreEstado']) ?>
            </span>
        </div>

        <div class="management-header__actions asset-detail-actions">
            <a
                class="button button--secondary"
                href="<?= e(
                    base_url(
                        'inventario/activos?producto='
                        . $asset['idProducto']
                    )
                ) ?>"
            >
                Volver a copias
            </a>

            <?php if (
                \App\Core\Auth::can(
                    \App\Core\Permissions::INVENTARIO_GESTIONAR
                )
            ): ?>
                <?php if (!empty($asset['idBaja'])): ?>
                    <a
                        class="button button--warning"
                        href="<?= e(
                            base_url(
                                'bajas/ver?id='
                                . $asset['idBaja']
                            )
                        ) ?>"
                    >
                        Ver baja registrada
                    </a>
                <?php else: ?>
                    <a
                        class="button button--secondary"
                        href="<?= e(
                            base_url(
                                'inventario/activos/editar?id='
                                . $asset['idActivo']
                            )
                        ) ?>"
                    >
                        Editar copia
                    </a>

                    <?php if (
                        (bool) $asset['activo']
                        && in_array(
                            $asset['codigoEstado'],
                            [
                                'EN_INVENTARIO',
                                'REVISION_TECNICA',
                            ],
                            true
                        )
                    ): ?>
                        <a
                            class="button button--warning"
                            href="<?= e(
                                base_url(
                                    'bajas/crear?activo='
                                    . $asset['idActivo']
                                )
                            ) ?>"
                        >
                            Registrar baja
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>

            <button
                class="button"
                type="button"
                data-print-page
            >
                Imprimir ficha
            </button>
        </div>
    </div>
</section>

<?php if (!empty($asset['idBaja'])): ?>
    <div class="alert alert--warning">
        Esta copia tiene una baja definitiva registrada como
        <strong><?= e($asset['nombreTipoBaja'] ?? 'baja') ?></strong>.
        Su información se conserva únicamente para trazabilidad.
    </div>
<?php endif; ?>

<?php if ($isLocalAddress): ?>
    <div class="alert alert--warning asset-qr-warning">
        El QR está usando una dirección <strong>localhost</strong>.
        Para escanearlo desde un teléfono, abre TrackiT usando la
        dirección IP local de la computadora antes de descargar o
        imprimir el código.
    </div>
<?php endif; ?>

<section class="asset-detail-layout">
    <div class="asset-detail-main">
        <article class="asset-detail-card">
            <div class="asset-detail-card__header">
                <div>
                    <span class="section-heading__eyebrow">
                        Identificación
                    </span>
                    <h2>Información de la copia</h2>
                </div>
            </div>

            <dl class="asset-information-grid">
                <div>
                    <dt>Código</dt>
                    <dd><?= e($asset['codigoActivo']) ?></dd>
                </div>

                <div>
                    <dt>Número de serie</dt>
                    <dd>
                        <?= e(
                            $asset['numeroSerie']
                            ?? 'No registrado'
                        ) ?>
                    </dd>
                </div>

                <div>
                    <dt>Dirección IP</dt>
                    <dd>
                        <?= e(
                            $asset['direccionIP']
                            ?? 'No registrada'
                        ) ?>
                    </dd>
                </div>

                <div>
                    <dt>Tipo</dt>
                    <dd><?= e($asset['tipoProducto']) ?></dd>
                </div>

                <div>
                    <dt>Producto</dt>
                    <dd><?= e($asset['nombreProducto']) ?></dd>
                </div>

                <div>
                    <dt>Marca y modelo</dt>
                    <dd>
                        <?= e(
                            trim(
                                (string) ($asset['marca'] ?? '')
                                . ' '
                                . (string) ($asset['modelo'] ?? '')
                            ) ?: 'No definidos'
                        ) ?>
                    </dd>
                </div>

                <div>
                    <dt>Fecha de adquisición</dt>
                    <dd><?= e($formatDate($asset['fechaAdquisicion'])) ?></dd>
                </div>

                <div>
                    <dt>Fecha de ingreso</dt>
                    <dd><?= e($formatDate($asset['fechaIngreso'])) ?></dd>
                </div>

                <div>
                    <dt>Vida útil</dt>
                    <dd>
                        <?= (int) ($asset['vidaUtilMesesAplicada'] ?? 0) > 0
                            ? e($asset['vidaUtilMesesAplicada']) . ' meses'
                            : 'No definida' ?>
                    </dd>
                </div>

                <div>
                    <dt>Fin estimado de vida útil</dt>
                    <dd><?= e($formatDate($asset['fechaFinVidaUtil'])) ?></dd>
                </div>

                <div>
                    <dt>Registro</dt>
                    <dd>
                        <?= (bool) $asset['activo']
                            ? 'Activo'
                            : 'Inactivo' ?>
                    </dd>
                </div>

                <div>
                    <dt>Última actualización</dt>
                    <dd><?= e($formatDateTime($asset['fechaActualizacion'])) ?></dd>
                </div>
            </dl>

            <?php if (!empty($asset['observaciones'])): ?>
                <div class="asset-detail-note">
                    <strong>Observaciones</strong>
                    <p><?= nl2br(e($asset['observaciones'])) ?></p>
                </div>
            <?php endif; ?>
        </article>

        <article class="asset-detail-card">
            <div class="asset-detail-card__header">
                <div>
                    <span class="section-heading__eyebrow">
                        Ubicación y custodia
                    </span>
                    <h2>Responsabilidad actual</h2>
                </div>
            </div>

            <div class="asset-responsibility-grid">
                <section>
                    <h3>Ubicación</h3>

                    <dl class="asset-compact-list">
                        <div>
                            <dt>Nombre</dt>
                            <dd>
                                <?= e(
                                    $asset['nombreUbicacion']
                                    ?? 'Sin ubicación'
                                ) ?>
                            </dd>
                        </div>
                        <div>
                            <dt>Edificio</dt>
                            <dd><?= e($asset['edificio'] ?? 'No indicado') ?></dd>
                        </div>
                        <div>
                            <dt>Piso</dt>
                            <dd><?= e($asset['piso'] ?? 'No indicado') ?></dd>
                        </div>
                        <div>
                            <dt>Oficina</dt>
                            <dd><?= e($asset['oficina'] ?? 'No indicada') ?></dd>
                        </div>
                    </dl>
                </section>

                <section>
                    <h3>Custodio</h3>

                    <?php if (!empty($asset['idColaborador'])): ?>
                        <dl class="asset-compact-list">
                            <div>
                                <dt>Colaborador</dt>
                                <dd><?= e($asset['nombreColaborador']) ?></dd>
                            </div>
                            <div>
                                <dt>Correo</dt>
                                <dd><?= e($asset['correoColaborador']) ?></dd>
                            </div>
                            <div>
                                <dt>Teléfono</dt>
                                <dd>
                                    <?= e(
                                        $asset['telefonoColaborador']
                                        ?? 'No registrado'
                                    ) ?>
                                </dd>
                            </div>
                            <div>
                                <dt>Entrega</dt>
                                <dd><?= e($formatDateTime($asset['fechaEntrega'])) ?></dd>
                            </div>
                        </dl>
                    <?php else: ?>
                        <div class="empty-state asset-empty-compact">
                            <p>Esta copia no tiene un custodio activo.</p>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </article>

        <article class="asset-detail-card">
            <div class="asset-detail-card__header">
                <div>
                    <span class="section-heading__eyebrow">
                        Evidencia visual
                    </span>
                    <h2>Galería de imágenes</h2>
                </div>

                <span class="badge badge--normal">
                    <?= e(count($images)) ?> imagen(es)
                </span>
            </div>

            <div class="asset-gallery-grid">
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
                                ? 'Principal'
                                : 'Ver imagen' ?>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>
        </article>

        <article class="asset-detail-card">
            <div class="asset-detail-card__header">
                <div>
                    <span class="section-heading__eyebrow">
                        Trazabilidad
                    </span>
                    <h2>Movimientos recientes</h2>
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table asset-history-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Movimiento</th>
                            <th>Cambio</th>
                            <th>Realizado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($movements === []): ?>
                            <tr>
                                <td class="table-empty" colspan="4">
                                    No hay movimientos registrados.
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($movements as $movement): ?>
                            <tr>
                                <td><?= e($formatDateTime($movement['fechaMovimiento'])) ?></td>
                                <td>
                                    <strong><?= e($movement['tipoMovimiento']) ?></strong>
                                    <?php if (!empty($movement['descripcion'])): ?>
                                        <small class="asset-table-description">
                                            <?= e($movement['descripcion']) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (
                                        !empty($movement['estadoAnterior'])
                                        || !empty($movement['estadoNuevo'])
                                    ): ?>
                                        <?= e($movement['estadoAnterior'] ?? '—') ?>
                                        →
                                        <?= e($movement['estadoNuevo'] ?? '—') ?>
                                    <?php elseif (
                                        !empty($movement['ubicacionAnterior'])
                                        || !empty($movement['ubicacionNueva'])
                                    ): ?>
                                        <?= e($movement['ubicacionAnterior'] ?? '—') ?>
                                        →
                                        <?= e($movement['ubicacionNueva'] ?? '—') ?>
                                    <?php else: ?>
                                        Sin cambio asociado
                                    <?php endif; ?>
                                </td>
                                <td><?= e($movement['realizadoPor']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="asset-detail-card">
            <div class="asset-detail-card__header">
                <div>
                    <span class="section-heading__eyebrow">
                        Servicio técnico
                    </span>
                    <h2>Historial de reparaciones</h2>
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table asset-history-table">
                    <thead>
                        <tr>
                            <th>Inicio</th>
                            <th>Estado</th>
                            <th>Técnico</th>
                            <th>Falla y diagnóstico</th>
                            <th>Costo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($repairs === []): ?>
                            <tr>
                                <td class="table-empty" colspan="5">
                                    Esta copia no tiene reparaciones registradas.
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($repairs as $repair): ?>
                            <tr>
                                <td><?= e($formatDateTime($repair['fechaInicio'])) ?></td>
                                <td>
                                    <span class="badge badge--warning">
                                        <?= e($repair['estadoReparacion']) ?>
                                    </span>
                                </td>
                                <td><?= e($repair['tecnico']) ?></td>
                                <td>
                                    <strong><?= e($repair['descripcionFalla']) ?></strong>
                                    <?php if (!empty($repair['diagnostico'])): ?>
                                        <small class="asset-table-description">
                                            <?= e($repair['diagnostico']) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    B/ <?= e(
                                        number_format(
                                            (float) $repair['costoReparacion'],
                                            2
                                        )
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </div>

    <aside class="asset-detail-sidebar">
        <article class="asset-qr-card">
            <span class="section-heading__eyebrow">
                Código QR
            </span>

            <h2>Ficha verificable</h2>

            <div class="asset-qr-card__image">
                <img
                    src="<?= e(
                        base_url(
                            'activo/qr?token='
                            . rawurlencode($asset['qrToken'])
                        )
                    ) ?>"
                    alt="Código QR del activo <?= e($asset['codigoActivo']) ?>"
                >
            </div>

            <p>
                Al escanearlo se abre una ficha pública que no muestra
                costo, IP, custodio ni otros datos internos.
            </p>

            <div class="asset-qr-card__actions">
                <a
                    class="button button--secondary"
                    href="<?= e($publicUrl) ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Abrir ficha pública
                </a>

                <a
                    class="button"
                    href="<?= e(
                        base_url(
                            'activo/qr?token='
                            . rawurlencode($asset['qrToken'])
                            . '&descargar=1'
                        )
                    ) ?>"
                >
                    Descargar QR
                </a>

                <button
                    class="button button--secondary"
                    type="button"
                    data-copy-url
                    data-copy-value="<?= e($publicUrl) ?>"
                >
                    Copiar enlace
                </button>

                <span class="asset-copy-feedback" data-copy-feedback></span>
            </div>
        </article>

        <article class="asset-financial-card">
            <span class="section-heading__eyebrow">
                Control financiero
            </span>

            <h2>Depreciación estimada</h2>

            <div class="asset-financial-value">
                <span>Valor en libros</span>
                <strong>
                    B/ <?= e(
                        number_format(
                            (float) $asset['valorLibroEstimado'],
                            2
                        )
                    ) ?>
                </strong>
            </div>

            <dl class="asset-compact-list">
                <div>
                    <dt>Costo</dt>
                    <dd>
                        B/ <?= e(
                            number_format(
                                (float) $asset['costo'],
                                2
                            )
                        ) ?>
                    </dd>
                </div>
                <div>
                    <dt>Valor residual</dt>
                    <dd>
                        B/ <?= e(
                            number_format(
                                (float) $asset['valorResidual'],
                                2
                            )
                        ) ?>
                    </dd>
                </div>
                <div>
                    <dt>Vida consumida</dt>
                    <dd>
                        <?= e($asset['porcentajeVidaConsumida']) ?>%
                    </dd>
                </div>
                <div>
                    <dt>Meses transcurridos</dt>
                    <dd><?= e($asset['mesesTranscurridos']) ?></dd>
                </div>
            </dl>

            <progress
                class="asset-life-progress"
                max="100"
                value="<?= e(
                    min(
                        100,
                        max(
                            0,
                            (float) $asset['porcentajeVidaConsumida']
                        )
                    )
                ) ?>"
            >
                <?= e($asset['porcentajeVidaConsumida']) ?>%
            </progress>
        </article>
    </aside>
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
