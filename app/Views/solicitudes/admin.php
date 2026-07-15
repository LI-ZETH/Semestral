<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            Administración
        </span>

        <h1>Solicitudes y reparaciones</h1>

        <p>
            Revisa las necesidades registradas por colaboradores y
            asigna los reportes de reparación a técnicos.
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
            class="button"
            href="<?= e(base_url('reparaciones')) ?>"
        >
            Ver reparaciones
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
            Adquisiciones y necesidades
        </span>

        <h2>Solicitudes generales</h2>
    </div>

    <form class="filters-card" method="GET">
        <div class="filters-grid request-filters-grid">
            <div class="field">
                <label for="buscar">Buscar</label>
                <input
                    id="buscar"
                    name="buscar"
                    type="search"
                    value="<?= e($filters['search'] ?? '') ?>"
                    placeholder="Título, nombre o correo"
                >
            </div>

            <div class="field">
                <label for="estado">Estado</label>
                <select id="estado" name="estado">
                    <option value="">Todos</option>
                    <?php foreach ([
                        'En espera',
                        'En trámite',
                        'Aprobada',
                        'Rechazada',
                        'Atendida',
                        'Cancelada',
                    ] as $status): ?>
                        <option
                            value="<?= e($status) ?>"
                            <?= (($filters['status'] ?? '') === $status)
                                ? 'selected'
                                : '' ?>
                        >
                            <?= e($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filters-actions">
                <button class="button" type="submit">Filtrar</button>
                <a
                    class="button button--secondary"
                    href="<?= e(base_url('solicitudes/administrar')) ?>"
                >
                    Limpiar
                </a>
            </div>
        </div>
    </form>

    <div class="table-card">
        <div class="table-responsive">
            <table class="data-table request-admin-table">
                <thead>
                    <tr>
                        <th>Solicitante</th>
                        <th>Solicitud</th>
                        <th>Tipo</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($needs === []): ?>
                        <tr>
                            <td class="table-empty" colspan="7">
                                No hay solicitudes con esos filtros.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($needs as $request): ?>
                        <tr>
                            <td>
                                <div class="request-primary-cell">
                                    <strong>
                                        <?= e($request['nombre']) ?>
                                        <?= e($request['apellido']) ?>
                                    </strong>
                                    <span><?= e($request['correo']) ?></span>
                                </div>
                            </td>

                            <td>
                                <div class="request-primary-cell">
                                    <strong><?= e($request['titulo']) ?></strong>
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
                                <a
                                    class="button button--small"
                                    href="<?= e(base_url(
                                        'solicitudes/revisar?id='
                                        . $request['idSolicitud']
                                    )) ?>"
                                >
                                    Revisar
                                </a>
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

    <form class="filters-card" method="GET">
        <div class="filters-grid request-filters-grid">
            <div class="field">
                <label for="buscarReparacion">Buscar</label>
                <input
                    id="buscarReparacion"
                    name="buscarReparacion"
                    type="search"
                    value="<?= e($filters['repairSearch'] ?? '') ?>"
                    placeholder="Código, producto o colaborador"
                >
            </div>

            <div class="field">
                <label for="estadoReparacion">Estado</label>
                <select
                    id="estadoReparacion"
                    name="estadoReparacion"
                >
                    <option value="">Todos</option>
                    <?php foreach ([
                        'EN_ESPERA' => 'En espera',
                        'ASIGNADA' => 'Asignada',
                        'EN_PROCESO' => 'En proceso',
                        'FINALIZADA' => 'Finalizada',
                        'RECHAZADA' => 'Rechazada',
                        'CANCELADA' => 'Cancelada',
                    ] as $value => $label): ?>
                        <option
                            value="<?= e($value) ?>"
                            <?= (($filters['repairStatus'] ?? '') === $value)
                                ? 'selected'
                                : '' ?>
                        >
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filters-actions">
                <button class="button" type="submit">Filtrar</button>
                <a
                    class="button button--secondary"
                    href="<?= e(base_url('solicitudes/administrar')) ?>"
                >
                    Limpiar
                </a>
            </div>
        </div>
    </form>

    <div class="table-card">
        <div class="table-responsive">
            <table class="data-table repair-admin-table">
                <thead>
                    <tr>
                        <th>Solicitante</th>
                        <th>Equipo</th>
                        <th>Reporte</th>
                        <th>Ubicación</th>
                        <th>Técnico</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($repairs === []): ?>
                        <tr>
                            <td class="table-empty" colspan="7">
                                No hay reportes con esos filtros.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($repairs as $repair): ?>
                        <tr>
                            <td>
                                <div class="request-primary-cell">
                                    <strong>
                                        <?= e($repair['nombre']) ?>
                                        <?= e($repair['apellido']) ?>
                                    </strong>
                                    <span><?= e($repair['correo']) ?></span>
                                </div>
                            </td>

                            <td>
                                <div class="request-primary-cell">
                                    <strong><?= e($repair['codigoActivo']) ?></strong>
                                    <span><?= e($repair['nombreProducto']) ?></span>
                                </div>
                            </td>

                            <td>
                                <div class="request-primary-cell">
                                    <strong><?= e($repair['titulo']) ?></strong>
                                    <span><?= e($repair['prioridad']) ?></span>
                                </div>
                            </td>

                            <td>
                                <?= e($repair['nombreUbicacion'] ?? 'Sin ubicación') ?>
                                <?php if (!empty($repair['edificio'])): ?>
                                    <small class="table-location-detail">
                                        <?= e($repair['edificio']) ?>
                                        <?= !empty($repair['piso'])
                                            ? ' · ' . e($repair['piso'])
                                            : '' ?>
                                        <?= !empty($repair['oficina'])
                                            ? ' · ' . e($repair['oficina'])
                                            : '' ?>
                                    </small>
                                <?php endif; ?>
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
                                <div class="table-actions">
                                    <?php if ($repair['estadoSolicitud'] === 'EN_ESPERA'): ?>
                                        <a
                                            class="button button--small"
                                            href="<?= e(base_url(
                                                'solicitudes/reparacion/asignar?id='
                                                . $repair['idSolicitudReparacion']
                                            )) ?>"
                                        >
                                            Asignar técnico
                                        </a>
                                    <?php elseif (in_array(
                                        $repair['estadoSolicitud'],
                                        ['ASIGNADA', 'EN_PROCESO'],
                                        true
                                    )): ?>
                                        <a
                                            class="button button--small button--secondary"
                                            href="<?= e(base_url(
                                                'reparaciones/gestionar?id='
                                                . $repair['idSolicitudReparacion']
                                            )) ?>"
                                        >
                                            Ver reparación
                                        </a>
                                    <?php else: ?>
                                        <span class="table-muted-text">
                                            Cerrada
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
