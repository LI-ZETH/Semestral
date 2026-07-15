<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            Inventario
        </span>

        <h1>Asignaciones de activos</h1>

        <p>
            Entrega copias a colaboradores, consulta quién tiene
            cada equipo y registra sus devoluciones.
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
            href="<?= e(base_url('ubicaciones')) ?>"
        >
            Administrar ubicaciones
        </a>

        <a
            class="button"
            href="<?= e(base_url('asignaciones/crear')) ?>"
        >
            Nueva asignación
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

<form
    class="filters-card"
    method="GET"
    action="<?= e(base_url('asignaciones')) ?>"
>
    <div class="filters-grid assignment-filters-grid">
        <div class="field">
            <label for="buscar">Buscar</label>

            <input
                id="buscar"
                name="buscar"
                type="search"
                value="<?= e($filters['search'] ?? '') ?>"
                placeholder="Código, producto, nombre o correo"
            >
        </div>

        <div class="field">
            <label for="estado">Estado de asignación</label>

            <select id="estado" name="estado">
                <option value="">Todas</option>
                <option
                    value="ACTIVA"
                    <?= ($filters['status'] ?? '') === 'ACTIVA'
                        ? 'selected'
                        : '' ?>
                >
                    Activas
                </option>
                <option
                    value="DEVUELTA"
                    <?= ($filters['status'] ?? '') === 'DEVUELTA'
                        ? 'selected'
                        : '' ?>
                >
                    Devueltas
                </option>
                <option
                    value="CANCELADA"
                    <?= ($filters['status'] ?? '') === 'CANCELADA'
                        ? 'selected'
                        : '' ?>
                >
                    Canceladas
                </option>
            </select>
        </div>

        <div class="filters-actions">
            <a
                class="button button--secondary"
                href="<?= e(base_url('asignaciones')) ?>"
            >
                Limpiar
            </a>

            <button class="button" type="submit">
                Filtrar
            </button>
        </div>
    </div>
</form>

<div class="table-card">
    <div class="table-responsive">
        <table class="data-table assignment-management-table">
            <thead>
                <tr>
                    <th>Activo</th>
                    <th>Colaborador</th>
                    <th>Ubicación</th>
                    <th>Entrega</th>
                    <th>Estado</th>
                    <th>Devolución</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($assignments === []): ?>
                    <tr>
                        <td class="table-empty" colspan="7">
                            No se encontraron asignaciones.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($assignments as $assignment): ?>
                    <tr>
                        <td>
                            <div class="assignment-asset-cell">
                                <?php if (!empty($assignment['imagenPrincipal'])): ?>
                                    <img
                                        src="<?= e(
                                            asset_url(
                                                $assignment['imagenPrincipal']
                                            )
                                        ) ?>"
                                        alt=""
                                    >
                                <?php else: ?>
                                    <span class="assignment-asset-placeholder">
                                        <?= e(
                                            mb_substr(
                                                $assignment['codigoActivo'],
                                                0,
                                                1
                                            )
                                        ) ?>
                                    </span>
                                <?php endif; ?>

                                <div>
                                    <strong>
                                        <?= e($assignment['codigoActivo']) ?>
                                    </strong>

                                    <span>
                                        <?= e($assignment['nombreProducto']) ?>
                                    </span>

                                    <small>
                                        Serie:
                                        <?= e(
                                            $assignment['numeroSerie']
                                            ?? 'No registrada'
                                        ) ?>
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="assignment-person-cell">
                                <strong>
                                    <?= e(
                                        $assignment['nombreColaborador']
                                        . ' '
                                        . $assignment['apellidoColaborador']
                                    ) ?>
                                </strong>

                                <span>
                                    <?= e(
                                        $assignment['departamento']
                                        ?? 'Sin departamento'
                                    ) ?>
                                </span>

                                <small>
                                    <?= e($assignment['correoColaborador']) ?>
                                </small>
                            </div>
                        </td>

                        <td>
                            <?= e(
                                $assignment['nombreUbicacion']
                                ?? 'Sin ubicación'
                            ) ?>
                        </td>

                        <td>
                            <div class="assignment-date-cell">
                                <strong>
                                    <?= e($assignment['fechaEntrega']) ?>
                                </strong>

                                <small>
                                    Entregó:
                                    <?= e(
                                        $assignment['nombreUsuarioEntrega']
                                        . ' '
                                        . $assignment['apellidoUsuarioEntrega']
                                    ) ?>
                                </small>
                            </div>
                        </td>

                        <td>
                            <?php
                            $assignmentClass = match (
                                $assignment['estadoAsignacion']
                            ) {
                                'ACTIVA' => 'badge--active',
                                'DEVUELTA' => 'badge--normal',
                                default => 'badge--inactive',
                            };
                            ?>

                            <span class="badge <?= e($assignmentClass) ?>">
                                <?= e(match (
                                    $assignment['estadoAsignacion']
                                ) {
                                    'ACTIVA' => 'Activa',
                                    'DEVUELTA' => 'Devuelta',
                                    default => 'Cancelada',
                                }) ?>
                            </span>
                        </td>

                        <td>
                            <?php if (
                                $assignment['estadoAsignacion'] === 'DEVUELTA'
                            ): ?>
                                <div class="assignment-return-cell">
                                    <strong>
                                        <?= e(
                                            $assignment['fechaRecepcion']
                                            ?? $assignment['fechaDevolucion']
                                            ?? 'Registrada'
                                        ) ?>
                                    </strong>

                                    <span>
                                        <?= e(
                                            $assignment['nombreMotivo']
                                            ?? 'Sin motivo'
                                        ) ?>
                                    </span>

                                    <small>
                                        <?= e(match (
                                            $assignment['condicionRecepcion']
                                            ?? ''
                                        ) {
                                            'BUENO' => 'Bueno',
                                            'DANADO' => 'Dañado',
                                            'INCOMPLETO' => 'Incompleto',
                                            default => 'No verificado',
                                        }) ?>
                                    </small>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">
                                    Pendiente
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="table-actions">
                                <a
                                    class="button button--small button--secondary"
                                    href="<?= e(
                                        base_url(
                                            'inventario/producto?id='
                                            . $assignment['idProducto']
                                        )
                                    ) ?>"
                                >
                                    Ver activo
                                </a>

                                <?php if (
                                    $assignment['estadoAsignacion']
                                    === 'ACTIVA'
                                ): ?>
                                    <a
                                        class="button button--small"
                                        href="<?= e(
                                            base_url(
                                                'asignaciones/devolver?id='
                                                . $assignment['idAsignacion']
                                            )
                                        ) ?>"
                                    >
                                        Registrar devolución
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
