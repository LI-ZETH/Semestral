<?php
$formatDateTime = static function (mixed $value): string {
    if ($value === null || $value === '') {
        return 'No registrada';
    }

    $timestamp = strtotime((string) $value);

    return $timestamp !== false
        ? date('d/m/Y H:i', $timestamp)
        : (string) $value;
};
?>

<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            Baja #<?= e($disposal['idBaja']) ?>
        </span>

        <h1>
            <?= e($disposal['nombreTipo']) ?> de activo
        </h1>

        <p>
            Registro definitivo de salida del inventario para la copia
            <?= e($disposal['codigoActivo']) ?>.
        </p>
    </div>

    <div class="management-header__actions">
        <a
            class="button button--secondary"
            href="<?= e(base_url('bajas')) ?>"
        >
            Volver a bajas
        </a>

        <a
            class="button"
            href="<?= e(
                base_url(
                    'inventario/activos/ver?id='
                    . $disposal['idActivo']
                )
            ) ?>"
        >
            Ver ficha del activo
        </a>
    </div>
</section>

<?php if (!empty($success)): ?>
    <div class="alert alert--success">
        <?= e($success) ?>
    </div>
<?php endif; ?>

<section class="disposal-detail-layout">
    <article class="disposal-detail-card">
        <div class="disposal-detail-asset">
            <div class="disposal-detail-asset__media">
                <?php if (!empty($disposal['imagenPrincipal'])): ?>
                    <img
                        src="<?= e(
                            asset_url(
                                $disposal['imagenPrincipal']
                            )
                        ) ?>"
                        alt="<?= e($disposal['codigoActivo']) ?>"
                    >
                <?php else: ?>
                    <span>
                        <?= e(
                            mb_substr(
                                $disposal['codigoActivo'],
                                0,
                                1
                            )
                        ) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div>
                <span class="badge <?= $disposal['codigoTipo'] === 'DONACION'
                    ? 'badge--normal'
                    : 'badge--inactive' ?>">
                    <?= e($disposal['nombreTipo']) ?>
                </span>

                <h2><?= e($disposal['codigoActivo']) ?></h2>

                <p><?= e($disposal['nombreProducto']) ?></p>

                <small>
                    <?= e(
                        trim(
                            ($disposal['marca'] ?? '')
                            . ' '
                            . ($disposal['modelo'] ?? '')
                        ) ?: 'Sin marca o modelo'
                    ) ?>
                </small>
            </div>
        </div>

        <dl class="disposal-detail-grid">
            <div>
                <dt>Estado final</dt>
                <dd><?= e($disposal['nombreEstado']) ?></dd>
            </div>

            <div>
                <dt>Fecha de baja</dt>
                <dd><?= e($formatDateTime($disposal['fechaBaja'])) ?></dd>
            </div>

            <div>
                <dt>Número de serie</dt>
                <dd>
                    <?= e(
                        $disposal['numeroSerie']
                        ?? 'No registrado'
                    ) ?>
                </dd>
            </div>

            <div>
                <dt>Ubicación al cerrar</dt>
                <dd>
                    <?= e(
                        $disposal['nombreUbicacion']
                        ?? 'Sin ubicación'
                    ) ?>
                </dd>
            </div>

            <div>
                <dt>Costo registrado</dt>
                <dd>
                    B/ <?= e(
                        number_format(
                            (float) $disposal['costo'],
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
                            (float) $disposal['valorResidual'],
                            2
                        )
                    ) ?>
                </dd>
            </div>

            <div>
                <dt>Registrado por</dt>
                <dd><?= e($disposal['registradoPor']) ?></dd>
            </div>

            <div>
                <dt>Documento</dt>
                <dd>
                    <?= e(
                        $disposal['documentoReferencia']
                        ?? 'Sin referencia'
                    ) ?>
                </dd>
            </div>
        </dl>
    </article>

    <article class="disposal-detail-card">
        <div class="disposal-text-section">
            <span class="section-heading__eyebrow">
                Justificación
            </span>

            <h2>Motivo de la baja</h2>

            <p><?= nl2br(e($disposal['motivo'])) ?></p>
        </div>

        <div class="disposal-text-section">
            <span class="section-heading__eyebrow">
                Evaluación
            </span>

            <h2>Opinión técnica</h2>

            <p>
                <?= !empty($disposal['opinionTecnica'])
                    ? nl2br(e($disposal['opinionTecnica']))
                    : 'No se registró una opinión técnica adicional.' ?>
            </p>
        </div>

        <?php if ($disposal['codigoTipo'] === 'DONACION'): ?>
            <div class="disposal-donation-summary">
                <span class="section-heading__eyebrow">
                    Entrega externa
                </span>

                <h2>Información de la donación</h2>

                <dl class="disposal-detail-grid">
                    <div>
                        <dt>Entidad beneficiaria</dt>
                        <dd>
                            <?= e($disposal['entidadBeneficiaria']) ?>
                        </dd>
                    </div>

                    <div>
                        <dt>Responsable de recepción</dt>
                        <dd>
                            <?= e($disposal['responsableDonacion']) ?>
                        </dd>
                    </div>
                </dl>
            </div>
        <?php endif; ?>
    </article>
</section>
