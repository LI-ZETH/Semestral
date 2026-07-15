<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            Portal del colaborador
        </span>

        <h1>Mis solicitudes</h1>

        <p>
            Consulta tus necesidades registradas y reportes
            de reparación.
        </p>
    </div>

    <div class="management-header__actions">
        <a
            class="button button--secondary"
            href="<?= e(base_url('panel')) ?>"
        >
            Volver al panel
        </a>

        <a
            class="button button--secondary"
            href="<?= e(base_url('solicitudes/reparacion/crear')) ?>"
        >
            Reportar reparación
        </a>

        <a
            class="button"
            href="<?= e(base_url('solicitudes/crear')) ?>"
        >
            Nueva solicitud
        </a>
    </div>
</section>

<?php if (!empty($success)): ?>
    <div class="alert alert--success">
        <?= e($success) ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert--error">
        <?= e($error) ?>
    </div>
<?php endif; ?>

<section class="request-section">
    <div class="section-heading">
        <span class="section-heading__eyebrow">
            Equipos, software y licencias
        </span>

        <h2>Solicitudes de necesidad</h2>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="data-table request-table">
                <thead>
                    <tr>
                        <th>Solicitud</th>
                        <th>Tipo</th>
                        <th>Prioridad</th>
                        <th>Cantidad</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($needs === []): ?>
                        <tr>
                            <td class="table-empty" colspan="7">
                                No has registrado solicitudes.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($needs as $request): ?>
                        <tr>
                            <td>
                                <div class="request-primary-cell">
                                    <strong>
                                        <?= e($request['titulo']) ?>
                                    </strong>

                                    <span>
                                        <?= e(
                                            $request['nombreProducto']
                                            ?? $request['nombreSubcategoria']
                                            ?? 'Sin producto específico'
                                        ) ?>
                                    </span>
                                </div>
                            </td>

                            <td><?= e($request['tipoSolicitud']) ?></td>

                            <td>
                                <span class="priority-badge priority-badge--<?= e(strtolower($request['prioridad'])) ?>">
                                    <?= e($request['prioridad']) ?>
                                </span>
                            </td>

                            <td><?= e($request['cantidad']) ?></td>

                            <td>
                                <span class="badge badge--normal">
                                    <?= e($request['nombreEstado']) ?>
                                </span>
                            </td>

                            <td>
                                <?= e(date(
                                    'd/m/Y H:i',
                                    strtotime($request['fechaSolicitud'])
                                )) ?>
                            </td>

                            <td>
                                <?php if ($request['nombreEstado'] === 'En espera'): ?>
                                    <form
                                        class="inline-form"
                                        method="POST"
                                        action="<?= e(base_url('solicitudes/cancelar')) ?>"
                                    >
                                        <?= csrf_field() ?>

                                        <input
                                            type="hidden"
                                            name="idSolicitud"
                                            value="<?= e($request['idSolicitud']) ?>"
                                        >

                                        <button
                                            class="button button--small button--danger"
                                            type="submit"
                                        >
                                            Cancelar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="table-muted-text">
                                        Procesada
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

<section class="request-section">
    <div class="section-heading">
        <span class="section-heading__eyebrow">
            Soporte técnico
        </span>

        <h2>Reportes de reparación</h2>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="data-table request-table">
                <thead>
                    <tr>
                        <th>Equipo</th>
                        <th>Reporte</th>
                        <th>Prioridad</th>
                        <th>Técnico</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($repairs === []): ?>
                        <tr>
                            <td class="table-empty" colspan="7">
                                No has reportado reparaciones.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($repairs as $repair): ?>
                        <tr>
                            <td>
                                <div class="request-primary-cell">
                                    <strong>
                                        <?= e($repair['codigoActivo']) ?>
                                    </strong>

                                    <span>
                                        <?= e($repair['nombreProducto']) ?>
                                        <?= e(trim(
                                            ($repair['marca'] ?? '')
                                            . ' '
                                            . ($repair['modelo'] ?? '')
                                        )) ?>
                                    </span>
                                </div>
                            </td>

                            <td><?= e($repair['titulo']) ?></td>

                            <td>
                                <span class="priority-badge priority-badge--<?= e(strtolower($repair['prioridad'])) ?>">
                                    <?= e($repair['prioridad']) ?>
                                </span>
                            </td>

                            <td>
                                <?= e(
                                    $repair['tecnicoAsignado']
                                    ?? 'Sin asignar'
                                ) ?>
                            </td>

                            <td>
                                <span class="badge badge--normal">
                                    <?= e(
                                        $repair['estadoReparacion']
                                        ?? str_replace('_', ' ', $repair['estadoSolicitud'])
                                    ) ?>
                                </span>
                            </td>

                            <td>
                                <?= e(date(
                                    'd/m/Y H:i',
                                    strtotime($repair['fechaSolicitud'])
                                )) ?>
                            </td>

                            <td>
                                <?php if ($repair['estadoSolicitud'] === 'EN_ESPERA'): ?>
                                    <form
                                        class="inline-form"
                                        method="POST"
                                        action="<?= e(base_url('solicitudes/reparacion/cancelar')) ?>"
                                    >
                                        <?= csrf_field() ?>

                                        <input
                                            type="hidden"
                                            name="idSolicitudReparacion"
                                            value="<?= e($repair['idSolicitudReparacion']) ?>"
                                        >

                                        <button
                                            class="button button--small button--danger"
                                            type="submit"
                                        >
                                            Cancelar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="table-muted-text">
                                        En seguimiento
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
