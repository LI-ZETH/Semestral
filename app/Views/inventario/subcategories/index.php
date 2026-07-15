<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            <?= e($category['nombreCategoria']) ?>
        </span>

        <h1>Administrar subcategorías</h1>

        <p>
            Organiza los tipos de productos que pertenecen
            a esta categoría.
        </p>
    </div>

    <div class="management-header__actions">
        <a
            class="button button--secondary"
            href="<?= e(
                base_url('inventario/categorias')
            ) ?>"
        >
            Volver a categorías
        </a>

        <a
            class="button"
            href="<?= e(
                base_url(
                    'inventario/subcategorias/crear?categoria='
                    . $category['idCategoria']
                )
            ) ?>"
        >
            Registrar subcategoría
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
        <table class="data-table">
            <thead>
                <tr>
                    <th>Subcategoría</th>
                    <th>Productos</th>
                    <th>Activos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($subcategories === []): ?>
                    <tr>
                        <td
                            class="table-empty"
                            colspan="5"
                        >
                            No existen subcategorías registradas.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach (
                    $subcategories as $subcategory
                ): ?>
                    <tr>
                        <td>
                            <div class="subcategory-table-cell">
                                <?php if (
                                    !empty($subcategory['imagen'])
                                ): ?>
                                    <img
                                        src="<?= e(
                                            asset_url(
                                                $subcategory['imagen']
                                            )
                                        ) ?>"
                                        alt=""
                                    >
                                <?php else: ?>
                                    <span class="subcategory-initial">
                                        <?= e(
                                            mb_substr(
                                                $subcategory[
                                                    'nombreSubcategoria'
                                                ],
                                                0,
                                                1
                                            )
                                        ) ?>
                                    </span>
                                <?php endif; ?>

                                <div>
                                    <strong>
                                        <?= e(
                                            $subcategory[
                                                'nombreSubcategoria'
                                            ]
                                        ) ?>
                                    </strong>

                                    <small>
                                        <?= e(
                                            $subcategory[
                                                'descripcion'
                                            ]
                                            ?? 'Sin descripción'
                                        ) ?>
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <?= e(
                                $subcategory['totalProductos']
                            ) ?>
                        </td>

                        <td>
                            <?= e(
                                $subcategory['totalActivos']
                            ) ?>
                        </td>

                        <td>
                            <?php if (
                                (bool) $subcategory['activo']
                            ): ?>
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
                                    class="button button--small
                                        button--secondary"
                                    href="<?= e(
                                        base_url(
                                            'inventario/subcategorias/editar?id='
                                            . $subcategory[
                                                'idSubcategoria'
                                            ]
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
                                            'inventario/subcategorias/estado'
                                        )
                                    ) ?>"
                                >
                                    <?= csrf_field() ?>

                                    <input
                                        type="hidden"
                                        name="idSubcategoria"
                                        value="<?= e(
                                            $subcategory[
                                                'idSubcategoria'
                                            ]
                                        ) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="idCategoria"
                                        value="<?= e(
                                            $category['idCategoria']
                                        ) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="activo"
                                        value="<?= (bool) $subcategory['activo']
                                            ? '0'
                                            : '1' ?>"
                                    >

                                    <button
                                        class="button button--small
                                            <?= (bool) $subcategory['activo']
                                                ? 'button--danger'
                                                : '' ?>"
                                        type="submit"
                                    >
                                        <?= (bool) $subcategory['activo']
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