<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            <?= e($product['nombreCategoria']) ?>
            ·
            <?= e($product['nombreSubcategoria']) ?>
        </span>

        <h1>Copias de <?= e($product['nombreProducto']) ?></h1>

        <p>
            <?= e(
                trim(
                    ($product['marca'] ?? '')
                    . ' '
                    . ($product['modelo'] ?? '')
                ) ?: 'Producto sin marca o modelo definido.'
            ) ?>
        </p>
    </div>

    <div class="management-header__actions">
        <a
            class="button button--secondary"
            href="<?= e(
                base_url(
                    'inventario/productos?subcategoria='
                    . $product['idSubcategoria']
                )
            ) ?>"
        >
            Volver a productos
        </a>

        <a
            class="button button--secondary"
            href="<?= e(
                base_url(
                    'inventario/producto?id='
                    . $product['idProducto']
                )
            ) ?>"
        >
            Ver inventario
        </a>

        <?php if (($product['tipoProducto'] ?? '') === 'LICENCIA'): ?>
            <a
                class="button button--secondary"
                href="<?= e(base_url('licencias')) ?>"
            >
                Gestionar licencias
            </a>
        <?php endif; ?>

        <a
            class="button"
            href="<?= e(
                base_url(
                    'inventario/activos/crear?producto='
                    . $product['idProducto']
                )
            ) ?>"
        >
            Registrar copia
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
    action="<?= e(base_url('inventario/activos')) ?>"
>
    <input
        type="hidden"
        name="producto"
        value="<?= e($product['idProducto']) ?>"
    >

    <div class="filters-grid asset-filters-grid">
        <div class="field">
            <label for="buscar">Buscar copia</label>

            <input
                id="buscar"
                name="buscar"
                type="search"
                value="<?= e($filters['search'] ?? '') ?>"
                placeholder="Código, serie, IP o ubicación"
            >
        </div>

        <div class="field">
            <label for="estado">Estado operativo</label>

            <select id="estado" name="estado">
                <option value="">Todos los estados</option>

                <?php foreach ($states as $state): ?>
                    <option
                        value="<?= e($state['idEstadoActivo']) ?>"
                        <?= (
                            (int) ($filters['state'] ?? 0)
                            === (int) $state['idEstadoActivo']
                        ) ? 'selected' : '' ?>
                    >
                        <?= e($state['nombreEstado']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label for="activo">Registro</label>

            <select id="activo" name="activo">
                <option value="">Todos</option>
                <option
                    value="1"
                    <?= ($filters['active'] ?? '') === '1'
                        ? 'selected'
                        : '' ?>
                >
                    Activos
                </option>
                <option
                    value="0"
                    <?= ($filters['active'] ?? '') === '0'
                        ? 'selected'
                        : '' ?>
                >
                    Inactivos
                </option>
            </select>
        </div>

        <div class="filters-actions">
            <a
                class="button button--secondary"
                href="<?= e(
                    base_url(
                        'inventario/activos?producto='
                        . $product['idProducto']
                    )
                ) ?>"
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
        <table class="data-table asset-management-table">
            <thead>
                <tr>
                    <th>Copia</th>
                    <th>Estado</th>
                    <th>Ubicación</th>
                    <th>Costo</th>
                    <th>Adquisición</th>
                    <th>Imágenes</th>
                    <th>Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($assets === []): ?>
                    <tr>
                        <td class="table-empty" colspan="8">
                            No se encontraron copias para este producto.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($assets as $asset): ?>
                    <tr>
                        <td>
                            <div class="asset-table-cell">
                                <?php if (!empty($asset['imagenPrincipal'])): ?>
                                    <img
                                        src="<?= e(
                                            asset_url(
                                                $asset['imagenPrincipal']
                                            )
                                        ) ?>"
                                        alt=""
                                    >
                                <?php else: ?>
                                    <span class="asset-image-placeholder">
                                        <?= e(
                                            mb_substr(
                                                $asset['codigoActivo'],
                                                0,
                                                1
                                            )
                                        ) ?>
                                    </span>
                                <?php endif; ?>

                                <div>
                                    <strong>
                                        <?= e($asset['codigoActivo']) ?>
                                    </strong>

                                    <span>
                                        Serie:
                                        <?= e(
                                            $asset['numeroSerie']
                                            ?? 'No registrada'
                                        ) ?>
                                    </span>

                                    <?php if (!empty($asset['direccionIP'])): ?>
                                        <small>
                                            IP: <?= e($asset['direccionIP']) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>

                        <td>
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
                            ?>

                            <span class="badge <?= e($stateClass) ?>">
                                <?= e($asset['nombreEstado']) ?>
                            </span>
                        </td>

                        <td>
                            <?= e(
                                $asset['nombreUbicacion']
                                ?? 'Sin ubicación'
                            ) ?>
                        </td>

                        <td>
                            B/ <?= e(
                                number_format(
                                    (float) $asset['costo'],
                                    2
                                )
                            ) ?>
                        </td>

                        <td>
                            <?= e($asset['fechaAdquisicion']) ?>
                        </td>

                        <td>
                            <span
                                class="badge <?= (int) $asset['cantidadImagenes'] >= 2
                                    ? 'badge--active'
                                    : 'badge--blocked' ?>"
                            >
                                <?= e($asset['cantidadImagenes']) ?>
                                imagen(es)
                            </span>
                        </td>

                        <td>
                            <?php if ((bool) $asset['activo']): ?>
                                <span class="badge badge--active">
                                    Activo
                                </span>
                            <?php else: ?>
                                <span class="badge badge--inactive">
                                    Inactivo
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="table-actions">
                                <a
                                    class="button button--small button--secondary"
                                    href="<?= e(
                                        base_url(
                                            'inventario/activos/ver?id='
                                            . $asset['idActivo']
                                        )
                                    ) ?>"
                                >
                                    Ficha
                                </a>

                            <?php if (
                                (bool) $asset['activo']
                                && $asset['codigoEstado'] === 'EN_INVENTARIO'
                            ): ?>
                                <a
                                    class="button button--small"
                                    href="<?= e(
                                        base_url(
                                            'asignaciones/crear?activo='
                                            . $asset['idActivo']
                                        )
                                    ) ?>"
                                >
                                    Asignar
                                </a>
                            <?php endif; ?>
                            
                                <a
                                    class="button button--small button--secondary"
                                    href="<?= e(
                                        base_url(
                                            'inventario/activos/editar?id='
                                            . $asset['idActivo']
                                        )
                                    ) ?>"
                                >
                                    Editar
                                </a>

                                <form
                                    class="inline-form"
                                    method="POST"
                                    action="<?= e(
                                        base_url(
                                            'inventario/activos/estado'
                                        )
                                    ) ?>"
                                >
                                    <?= csrf_field() ?>

                                    <input
                                        type="hidden"
                                        name="idActivo"
                                        value="<?= e($asset['idActivo']) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="idProducto"
                                        value="<?= e($product['idProducto']) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="activo"
                                        value="<?= (bool) $asset['activo']
                                            ? '0'
                                            : '1' ?>"
                                    >

                                    <button
                                        class="button button--small
                                            <?= (bool) $asset['activo']
                                                ? 'button--danger'
                                                : '' ?>"
                                        type="submit"
                                    >
                                        <?= (bool) $asset['activo']
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
