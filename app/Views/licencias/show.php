<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">Licencia registrada</span>
        <h1><?= e($license['nombreProducto']) ?></h1>
        <p>
            <?= e($license['codigoActivo']) ?> ·
            <?= e($license['tipoLicencia']) ?>
        </p>
    </div>

    <div class="management-header__actions">
        <a class="button button--secondary" href="<?= e(base_url('licencias')) ?>">
            Volver a licencias
        </a>
        <a
            class="button button--secondary"
            href="<?= e(base_url('licencias/editar?id=' . $license['idLicencia'])) ?>"
        >
            Editar
        </a>
        <?php if ($availableSeats > 0): ?>
            <a
                class="button"
                href="<?= e(base_url('licencias/asignar?id=' . $license['idLicencia'])) ?>"
            >
                Asignar puesto
            </a>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($success)): ?>
    <div class="alert alert--success"><?= e($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert--error"><?= e($error) ?></div>
<?php endif; ?>

<section class="license-detail-layout">
    <article class="license-detail-card">
        <div class="license-detail-card__heading">
            <div>
                <span class="section-heading__eyebrow">Información general</span>
                <h2><?= e($license['nombreProducto']) ?></h2>
            </div>
            <span class="badge badge--normal">
                <?= e($license['nombreEstado']) ?>
            </span>
        </div>

        <dl class="license-detail-list">
            <div><dt>Código de inventario</dt><dd><?= e($license['codigoActivo']) ?></dd></div>
            <div><dt>Proveedor</dt><dd><?= e($license['proveedor'] ?? 'No registrado') ?></dd></div>
            <div><dt>Tipo de licencia</dt><dd><?= e($license['tipoLicencia']) ?></dd></div>
            <div><dt>Fecha de inicio</dt><dd><?= e($license['fechaInicio'] ?? 'No registrada') ?></dd></div>
            <div><dt>Fecha de expiración</dt><dd><?= e($license['fechaExpiracion'] ?? 'Sin expiración') ?></dd></div>
            <div><dt>Renovación</dt><dd><?= (bool) $license['renovacionAutomatica'] ? 'Automática' : 'Manual' ?></dd></div>
            <div><dt>Puestos totales</dt><dd><?= e($license['cantidadPuestos']) ?></dd></div>
            <div><dt>Puestos asignados</dt><dd><?= e($activeAssignments) ?></dd></div>
            <div><dt>Puestos disponibles</dt><dd><?= e($availableSeats) ?></dd></div>
            <div><dt>Costo registrado</dt><dd>B/. <?= e(number_format((float) $license['costo'], 2)) ?></dd></div>
        </dl>

        <?php if (!empty($license['urlAcceso'])): ?>
            <a
                class="license-access-link"
                href="<?= e($license['urlAcceso']) ?>"
                target="_blank"
                rel="noopener noreferrer"
            >
                Abrir portal de acceso ↗
            </a>
        <?php endif; ?>

        <?php if (!empty($license['observaciones'])): ?>
            <div class="equipment-observation">
                <strong>Observaciones</strong>
                <p><?= nl2br(e($license['observaciones'])) ?></p>
            </div>
        <?php endif; ?>
    </article>

    <aside class="license-security-card">
        <span class="section-heading__eyebrow">Seguridad</span>
        <h2>Clave de licencia</h2>

        <?php if (!empty($license['claveCifrada'])): ?>
            <div class="license-key-mask">••••-••••-••••-••••</div>
            <p>
                La clave está cifrada con la llave pública RSA del sistema.
                Para verla debes confirmar tu contraseña actual.
            </p>
            <a
                class="button button--secondary"
                href="<?= e(base_url('licencias/clave?id=' . $license['idLicencia'])) ?>"
            >
                Mostrar clave
            </a>
        <?php else: ?>
            <p>No existe una clave almacenada para esta licencia.</p>
        <?php endif; ?>
    </aside>
</section>

<section class="dashboard-section">
    <div class="section-heading license-assignment-heading">
        <div>
            <span class="section-heading__eyebrow">Distribución</span>
            <h2>Asignaciones de puestos</h2>
        </div>
        <?php if ($availableSeats > 0): ?>
            <a
                class="button"
                href="<?= e(base_url('licencias/asignar?id=' . $license['idLicencia'])) ?>"
            >
                Asignar puesto
            </a>
        <?php endif; ?>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="data-table license-assignment-table">
                <thead>
                    <tr>
                        <th>Colaborador</th>
                        <th>Correo asignado</th>
                        <th>Fecha</th>
                        <th>Asignó</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($assignments === []): ?>
                        <tr>
                            <td class="table-empty" colspan="6">
                                No existen puestos asignados.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($assignments as $assignment): ?>
                        <tr>
                            <td>
                                <strong><?= e($assignment['nombreColaborador']) ?></strong><br>
                                <small><?= e($assignment['identificacion']) ?></small>
                            </td>
                            <td><?= e($assignment['correoAsignado'] ?? $assignment['correo']) ?></td>
                            <td><?= e($assignment['fechaAsignacion']) ?></td>
                            <td><?= e($assignment['nombreUsuarioAsigna']) ?></td>
                            <td>
                                <span class="badge <?= $assignment['estadoAsignacion'] === 'ACTIVA' ? 'badge--active' : 'badge--inactive' ?>">
                                    <?= e($assignment['estadoAsignacion']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($assignment['estadoAsignacion'] === 'ACTIVA'): ?>
                                    <form
                                        method="POST"
                                        action="<?= e(base_url('licencias/asignacion/revocar')) ?>"
                                        onsubmit="return confirm('¿Revocar esta asignación de licencia?');"
                                    >
                                        <?= csrf_field() ?>
                                        <input
                                            type="hidden"
                                            name="idAsignacionLicencia"
                                            value="<?= e($assignment['idAsignacionLicencia']) ?>"
                                        >
                                        <button class="button button--small button--danger" type="submit">
                                            Revocar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">
                                        <?= e($assignment['fechaRevocacion'] ?? '') ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
