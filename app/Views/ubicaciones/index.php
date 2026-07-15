<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            Administración
        </span>

        <h1>Ubicaciones</h1>

        <p>
            Administra los edificios, oficinas y demás lugares
            utilizados para localizar colaboradores y activos.
        </p>
    </div>

    <div class="management-header__actions">
        <a
            class="button button--secondary"
            href="<?= e(base_url('asignaciones')) ?>"
        >
            Volver a asignaciones
        </a>

        <a
            class="button button--secondary"
            href="<?= e(base_url('panel')) ?>"
        >
            Volver al panel
        </a>

        <a
            class="button"
            href="<?= e(base_url('ubicaciones/crear')) ?>"
        >
            Registrar ubicación
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
    action="<?= e(base_url('ubicaciones')) ?>"
>
    <div class="filters-grid location-filters-grid">
        <div class="field">
            <label for="buscar">Buscar ubicación</label>

            <input
                id="buscar"
                name="buscar"
                type="search"
                value="<?= e($filters['search'] ?? '') ?>"
                placeholder="Nombre, edificio, piso u oficina"
            >
        </div>

        <div class="field">
            <label for="tipo">Tipo</label>

            <select id="tipo" name="tipo">
                <option value="">Todos los tipos</option>

                <?php foreach ($types as $type): ?>
                    <option
                        value="<?= e($type) ?>"
                        <?= ($filters['type'] ?? '') === $type
                            ? 'selected'
                            : '' ?>
                    >
                        <?= e(match ($type) {
                            'EDIFICIO' => 'Edificio',
                            'OFICINA' => 'Oficina',
                            'CASA' => 'Casa',
                            'BODEGA' => 'Bodega',
                            default => 'Otra',
                        }) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label for="activo">Estado</label>

            <select id="activo" name="activo">
                <option value="">Todas</option>
                <option
                    value="1"
                    <?= ($filters['active'] ?? '') === '1'
                        ? 'selected'
                        : '' ?>
                >
                    Activas
                </option>
                <option
                    value="0"
                    <?= ($filters['active'] ?? '') === '0'
                        ? 'selected'
                        : '' ?>
                >
                    Inactivas
                </option>
            </select>
        </div>

        <div class="filters-actions">
            <a
                class="button button--secondary"
                href="<?= e(base_url('ubicaciones')) ?>"
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
        <table class="data-table location-management-table">
            <thead>
                <tr>
                    <th>Ubicación</th>
                    <th>Tipo</th>
                    <th>Edificio / piso</th>
                    <th>Activos</th>
                    <th>Colaboradores</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($locations === []): ?>
                    <tr>
                        <td class="table-empty" colspan="7">
                            No se encontraron ubicaciones.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($locations as $location): ?>
                    <tr>
                        <td>
                            <div class="location-table-cell">
                                <span class="location-table-icon">
                                    <?= e(
                                        mb_substr(
                                            $location['nombreUbicacion'],
                                            0,
                                            1
                                        )
                                    ) ?>
                                </span>

                                <div>
                                    <strong>
                                        <?= e($location['nombreUbicacion']) ?>
                                    </strong>

                                    <small>
                                        <?= e(
                                            $location['oficina']
                                            ?? $location['direccion']
                                            ?? 'Sin detalle adicional'
                                        ) ?>
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge badge--role">
                                <?= e(match ($location['tipoUbicacion']) {
                                    'EDIFICIO' => 'Edificio',
                                    'OFICINA' => 'Oficina',
                                    'CASA' => 'Casa',
                                    'BODEGA' => 'Bodega',
                                    default => 'Otra',
                                }) ?>
                            </span>
                        </td>

                        <td>
                            <?= e(
                                trim(
                                    ($location['edificio'] ?? '')
                                    . ' '
                                    . ($location['piso'] ?? '')
                                ) ?: 'No especificado'
                            ) ?>
                        </td>

                        <td>
                            <?= e($location['totalActivos']) ?>
                        </td>

                        <td>
                            <?= e($location['totalColaboradores']) ?>
                        </td>

                        <td>
                            <?php if ((bool) $location['activo']): ?>
                                <span class="badge badge--active">
                                    Activa
                                </span>
                            <?php else: ?>
                                <span class="badge badge--inactive">
                                    Inactiva
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="table-actions">
                                <a
                                    class="button button--small button--secondary"
                                    href="<?= e(
                                        base_url(
                                            'ubicaciones/editar?id='
                                            . $location['idUbicacion']
                                        )
                                    ) ?>"
                                >
                                    Editar
                                </a>

                                <form
                                    class="inline-form"
                                    method="POST"
                                    action="<?= e(
                                        base_url('ubicaciones/estado')
                                    ) ?>"
                                >
                                    <?= csrf_field() ?>

                                    <input
                                        type="hidden"
                                        name="idUbicacion"
                                        value="<?= e(
                                            $location['idUbicacion']
                                        ) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="activo"
                                        value="<?= (bool) $location['activo']
                                            ? '0'
                                            : '1' ?>"
                                    >

                                    <button
                                        class="button button--small
                                            <?= (bool) $location['activo']
                                                ? 'button--danger'
                                                : '' ?>"
                                        type="submit"
                                    >
                                        <?= (bool) $location['activo']
                                            ? 'Desactivar'
                                            : 'Activar' ?>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
