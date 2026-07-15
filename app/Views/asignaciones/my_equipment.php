<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            Mi inventario
        </span>

        <h1>Mis equipos</h1>

        <p>
            Consulta las copias que se encuentran actualmente
            bajo tu responsabilidad.
        </p>
    </div>

    <a
        class="button button--secondary"
        href="<?= e(base_url('panel')) ?>"
    >
        Volver al panel
    </a>
</section>

<div class="my-equipment-grid">
    <?php if ($assignments === []): ?>
        <div class="empty-state">
            <h2>No tienes equipos asignados</h2>

            <p>
                Actualmente no existen copias registradas bajo
                tu custodia.
            </p>
        </div>
    <?php endif; ?>

    <?php foreach ($assignments as $assignment): ?>
        <article class="my-equipment-card">
            <div class="my-equipment-card__media">
                <?php if (!empty($assignment['imagenPrincipal'])): ?>
                    <img
                        src="<?= e(
                            asset_url($assignment['imagenPrincipal'])
                        ) ?>"
                        alt="<?= e($assignment['nombreProducto']) ?>"
                    >
                <?php else: ?>
                    <span>
                        <?= e(
                            mb_substr(
                                $assignment['codigoActivo'],
                                0,
                                1
                            )
                        ) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="my-equipment-card__body">
                <span class="badge badge--active">
                    <?= e($assignment['nombreEstado']) ?>
                </span>

                <h2><?= e($assignment['nombreProducto']) ?></h2>

                <p class="my-equipment-card__model">
                    <?= e(
                        trim(
                            ($assignment['marca'] ?? '')
                            . ' '
                            . ($assignment['modelo'] ?? '')
                        ) ?: 'Sin marca o modelo definido'
                    ) ?>
                </p>

                <dl class="equipment-detail-list">
                    <div>
                        <dt>Código</dt>
                        <dd><?= e($assignment['codigoActivo']) ?></dd>
                    </div>

                    <div>
                        <dt>Número de serie</dt>
                        <dd>
                            <?= e(
                                $assignment['numeroSerie']
                                ?? 'No registrado'
                            ) ?>
                        </dd>
                    </div>

                    <div>
                        <dt>Ubicación</dt>
                        <dd>
                            <?= e(
                                $assignment['nombreUbicacion']
                                ?? 'Sin ubicación'
                            ) ?>
                        </dd>
                    </div>

                    <div>
                        <dt>Fecha de entrega</dt>
                        <dd><?= e($assignment['fechaEntrega']) ?></dd>
                    </div>
                </dl>

                <?php if (!empty($assignment['observacionesEntrega'])): ?>
                    <div class="equipment-observation">
                        <strong>Observaciones de entrega</strong>

                        <p>
                            <?= e($assignment['observacionesEntrega']) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="my-equipment-card__actions">
                    <a
                        class="button button--small"
                        href="<?= e(
                            base_url(
                                'solicitudes/reparacion/crear?activo='
                                . $assignment['idActivo']
                            )
                        ) ?>"
                    >
                        Reportar reparación
                    </a>
</div>
            </div>
        </article>
    <?php endforeach; ?>
</div>
