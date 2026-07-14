<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            Inventario
        </span>

        <h1>Administrar categorías</h1>

        <p>
            Registra, actualiza, activa o desactiva
            las categorías principales del inventario.
        </p>
    </div>

    <div class="management-header__actions">
        <a
            class="button button--secondary"
            href="<?= e(base_url('inventario')) ?>"
        >
            Ver inventario
        </a>

        <a
            class="button"
            href="<?= e(
                base_url(
                    'inventario/categorias/crear'
                )
            ) ?>"
        >
            Registrar categoría
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
                    <th>Categoría</th>
                    <th>Subcategorías</th>
                    <th>Productos</th>
                    <th>Activos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach (
                    $categories as $category
                ): ?>
                    <tr>
                        <td>
                            <div class="category-table-cell">
                                <?php if (
                                    !empty(
                                        $category['imagen']
                                    )
                                ): ?>
                                    <img
                                        src="<?= e(
                                            asset_url(
                                                $category[
                                                    'imagen'
                                                ]
                                            )
                                        ) ?>"
                                        alt=""
                                    >
                                <?php endif; ?>

                                <div>
                                    <strong>
                                        <?= e(
                                            $category[
                                                'nombreCategoria'
                                            ]
                                        ) ?>
                                    </strong>

                                    <span>
                                        <?= e(
                                            $category[
                                                'descripcion'
                                            ]
                                            ?? 'Sin descripción'
                                        ) ?>
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td>
                            <?= e(
                                $category[
                                    'totalSubcategorias'
                                ]
                            ) ?>
                        </td>

                        <td>
                            <?= e(
                                $category[
                                    'totalProductos'
                                ]
                            ) ?>
                        </td>

                        <td>
                            <?= e(
                                $category[
                                    'totalActivos'
                                ]
                            ) ?>
                        </td>

                        <td>
                            <?php if (
                                (bool) $category['activo']
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
                                            'inventario/categorias/editar?id='
                                            . $category[
                                                'idCategoria'
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
                                            'inventario/categorias/estado'
                                        )
                                    ) ?>"
                                >
                                    <?= csrf_field() ?>

                                    <input
                                        type="hidden"
                                        name="idCategoria"
                                        value="<?= e(
                                            $category[
                                                'idCategoria'
                                            ]
                                        ) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="activo"
                                        value="<?= (bool) $category['activo']
                                            ? '0'
                                            : '1' ?>"
                                    >

                                    <button
                                        class="button button--small
                                            <?= (bool) $category['activo']
                                                ? 'button--danger'
                                                : '' ?>"
                                        type="submit"
                                    >
                                        <?= (bool) $category['activo']
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