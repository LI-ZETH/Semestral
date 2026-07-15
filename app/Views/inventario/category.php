<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            <?= e($category['nombreCategoria']) ?>
        </span>

        <h1>Subcategorías</h1>

        <p>
            <?= e(
                $category['descripcion']
                ?? 'Clasificaciones de esta categoría.'
            ) ?>
        </p>
    </div>

    <div class="management-header__actions">
        <a
            class="button button--secondary"
            href="<?= e(base_url('inventario')) ?>"
        >
            Volver a categorías
        </a>

        <?php if (
            \App\Core\Auth::can(
                \App\Core\Permissions::INVENTARIO_GESTIONAR
            )
        ): ?>
            <a
                class="button"
                href="<?= e(
                    base_url(
                        'inventario/subcategorias?categoria='
                        . $category['idCategoria']
                    )
                ) ?>"
            >
                Administrar subcategorías
            </a>
        <?php endif; ?>
    </div>
</section>

<div class="inventory-subcategory-grid">
    <?php if ($subcategories === []): ?>
        <div class="empty-state">
            <h2>No hay subcategorías activas</h2>

            <p>
                Esta categoría todavía no tiene
                subcategorías disponibles.
            </p>
        </div>
    <?php endif; ?>

    <?php foreach ($subcategories as $subcategory): ?>
        <a
            class="inventory-subcategory-card"
            href="<?= e(
                base_url(
                    'inventario/subcategoria?id='
                    . $subcategory['idSubcategoria']
                )
            ) ?>"
        >
            <div class="inventory-subcategory-card__media">
                <?php if (!empty($subcategory['imagen'])): ?>
                    <img
                        src="<?= e(
                            asset_url($subcategory['imagen'])
                        ) ?>"
                        alt="<?= e(
                            $subcategory[
                                'nombreSubcategoria'
                            ]
                        ) ?>"
                    >
                <?php else: ?>
                    <span>
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
            </div>

            <div class="inventory-subcategory-card__body">
                <h2>
                    <?= e(
                        $subcategory[
                            'nombreSubcategoria'
                        ]
                    ) ?>
                </h2>

                <p>
                    <?= e(
                        $subcategory['descripcion']
                        ?? 'Sin descripción.'
                    ) ?>
                </p>

                <div class="inventory-subcategory-card__stats">
                    <span>
                        <strong>
                            <?= e(
                                $subcategory[
                                    'totalProductos'
                                ]
                            ) ?>
                        </strong>
                        Productos
                    </span>

                    <span>
                        <strong>
                            <?= e(
                                $subcategory[
                                    'totalActivos'
                                ]
                            ) ?>
                        </strong>
                        Activos
                    </span>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>