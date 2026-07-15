<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            <?= e($subcategory['nombreCategoria']) ?>
            ·
            <?= e($subcategory['nombreSubcategoria']) ?>
        </span>

        <h1>Administrar productos</h1>

        <p>
            Registra los modelos generales que pertenecen a esta
            subcategoría y consulta cuántas copias tiene cada uno.
        </p>
    </div>

    <div class="management-header__actions">
        <a
            class="button button--secondary"
            href="<?= e(
                base_url(
                    'inventario/subcategorias?categoria='
                    . $subcategory['idCategoria']
                )
            ) ?>"
        >
            Volver a subcategorías
        </a>

        <a
            class="button button--secondary"
            href="<?= e(
                base_url(
                    'inventario/subcategoria?id='
                    . $subcategory['idSubcategoria']
                )
            ) ?>"
        >
            Ver inventario
        </a>

        <a
            class="button"
            href="<?= e(
                base_url(
                    'inventario/productos/crear?subcategoria='
                    . $subcategory['idSubcategoria']
                )
            ) ?>"
        >
            Registrar producto
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

<div class="table-card">
    <div class="table-responsive">
        <table class="data-table product-management-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Vida útil</th>
                    <th>Copias</th>
                    <th>Disponibles</th>
                    <th>Asignadas</th>
                    <th>Servicio técnico</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($products === []): ?>
                    <tr>
                        <td class="table-empty" colspan="9">
                            No existen productos registrados en esta
                            subcategoría.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <div class="product-table-cell">
                                <?php if (!empty($product['imagen'])): ?>
                                    <img
                                        src="<?= e(
                                            asset_url($product['imagen'])
                                        ) ?>"
                                        alt=""
                                    >
                                <?php else: ?>
                                    <span class="product-initial">
                                        <?= e(
                                            mb_substr(
                                                $product['nombreProducto'],
                                                0,
                                                1
                                            )
                                        ) ?>
                                    </span>
                                <?php endif; ?>

                                <div>
                                    <strong>
                                        <?= e($product['nombreProducto']) ?>
                                    </strong>

                                    <small>
                                        <?= e(
                                            trim(
                                                ($product['marca'] ?? '')
                                                . ' '
                                                . ($product['modelo'] ?? '')
                                            ) ?: 'Sin marca o modelo'
                                        ) ?>
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge badge--role">
                                <?= e(
                                    match ($product['tipoProducto']) {
                                        'SOFTWARE' => 'Software',
                                        'LICENCIA' => 'Licencia',
                                        default => 'Hardware',
                                    }
                                ) ?>
                            </span>
                        </td>

                        <td>
                            <?= $product['vidaUtilMeses'] !== null
                                ? e($product['vidaUtilMeses']) . ' meses'
                                : 'No definida' ?>
                        </td>

                        <td><?= e($product['totalActivos']) ?></td>
                        <td><?= e($product['disponibles']) ?></td>
                        <td><?= e($product['asignados']) ?></td>
                        <td><?= e($product['enServicioTecnico']) ?></td>

                        <td>
                            <?php if ((bool) $product['activo']): ?>
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
                                            'inventario/producto?id='
                                            . $product['idProducto']
                                        )
                                    ) ?>"
                                >
                                    Ver copias
                                </a>

                                <a
                                    class="button button--small button--secondary"
                                    href="<?= e(
                                        base_url(
                                            'inventario/productos/editar?id='
                                            . $product['idProducto']
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
                                            'inventario/productos/estado'
                                        )
                                    ) ?>"
                                >
                                    <?= csrf_field() ?>

                                    <input
                                        type="hidden"
                                        name="idProducto"
                                        value="<?= e($product['idProducto']) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="idSubcategoria"
                                        value="<?= e(
                                            $subcategory['idSubcategoria']
                                        ) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="activo"
                                        value="<?= (bool) $product['activo']
                                            ? '0'
                                            : '1' ?>"
                                    >

                                    <button
                                        class="button button--small
                                            <?= (bool) $product['activo']
                                                ? 'button--danger'
                                                : '' ?>"
                                        type="submit"
                                    >
                                        <?= (bool) $product['activo']
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
