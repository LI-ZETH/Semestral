<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            Soporte técnico
        </span>

        <h1>Reparaciones</h1>

        <p>
            <?= $administrator
                ? 'Consulta y supervisa todas las reparaciones asignadas.'
                : 'Consulta los equipos que tienes asignados para revisión y reparación.' ?>
        </p>
    </div>

    <div class="management-header__actions">
        <a
            class="button button--secondary"
            href="<?= e(base_url('panel')) ?>"
        >
            Volver al panel
        </a>

        <?php if ($administrator): ?>
            <a
                class="button"
                href="<?= e(base_url('solicitudes/administrar')) ?>"
            >
                Administrar solicitudes
            </a>
        <?php endif; ?>
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

<form class="filters-card" method="GET">
    <div class="filters-grid request-filters-grid">
        <div class="field">
            <label for="buscar">Buscar</label>
            <input
                id="buscar"
                name="buscar"
                type="search"
                value="<?= e($filters['search'] ?? '') ?>"
                placeholder="Código, producto o colaborador"
            >
        </div>

        <div class="field">
            <label for="estado">Estado</label>
            <select id="estado" name="estado">
                <option value="">Todos</option>
                <?php foreach ([
                    'Pendiente',
                    'En proceso',
                    'Finalizada',
                    'No reparable',
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
                href="<?= e(base_url('reparaciones')) ?>"
            >
                Limpiar
            </a>
        </div>
    </div>
</form>

<div class="repair-task-grid">
    <?php if ($tasks === []): ?>
        <div class="empty-state">
            <h2>No hay reparaciones asignadas</h2>
            <p>
                No existen reparaciones que coincidan con los filtros.
            </p>
        </div>
    <?php endif; ?>

    <?php foreach ($tasks as $task): ?>
        <article class="repair-task-card">
            <div class="repair-task-card__header">
                <div>
                    <span class="repair-task-card__code">
                        <?= e($task['codigoActivo']) ?>
                    </span>

                    <h2><?= e($task['nombreProducto']) ?></h2>

                    <p>
                        <?= e(trim(
                            ($task['marca'] ?? '')
                            . ' '
                            . ($task['modelo'] ?? '')
                        )) ?>
                    </p>
                </div>

                <span class="priority-badge priority-badge--<?= e(strtolower($task['prioridad'])) ?>">
                    <?= e($task['prioridad']) ?>
                </span>
            </div>

            <div class="repair-task-card__status">
                <span class="badge badge--normal">
                    <?= e($task['estadoReparacion']) ?>
                </span>

                <span>
                    Solicitado:
                    <?= e(date(
                        'd/m/Y H:i',
                        strtotime($task['fechaSolicitud'])
                    )) ?>
                </span>
            </div>

            <div class="repair-task-card__body">
                <div>
                    <span>Falla reportada</span>
                    <p><?= e($task['descripcionFalla']) ?></p>
                </div>

                <div>
                    <span>Solicitante</span>
                    <p>
                        <?= e($task['colaboradorNombre']) ?>
                        <?= e($task['colaboradorApellido']) ?>
                        ·
                        <?= e($task['colaboradorCorreo']) ?>
                    </p>
                </div>

                <div>
                    <span>Ubicación</span>
                    <p>
                        <?= e($task['nombreUbicacion'] ?? 'Sin ubicación') ?>
                        <?php if (!empty($task['edificio'])): ?>
                            · <?= e($task['edificio']) ?>
                        <?php endif; ?>
                        <?php if (!empty($task['piso'])): ?>
                            · <?= e($task['piso']) ?>
                        <?php endif; ?>
                        <?php if (!empty($task['oficina'])): ?>
                            · <?= e($task['oficina']) ?>
                        <?php endif; ?>
                    </p>
                </div>

                <?php if ($administrator): ?>
                    <div>
                        <span>Técnico asignado</span>
                        <p><?= e($task['tecnicoAsignado']) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="repair-task-card__actions">
                <a
                    class="button"
                    href="<?= e(base_url(
                        'reparaciones/gestionar?id='
                        . $task['idSolicitudReparacion']
                    )) ?>"
                >
                    Gestionar reparación
                </a>
            </div>
        </article>
    <?php endforeach; ?>
</div>
