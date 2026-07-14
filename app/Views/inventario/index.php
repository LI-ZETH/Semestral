<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            Inventario tecnológico
        </span>

        <h1>Categorías</h1>

        <p>
            Selecciona una categoría para consultar sus
            productos y copias individuales.
        </p>
    </div>
    <?php if (
        \App\Core\Auth::can(
            \App\Core\Permissions::INVENTARIO_GESTIONAR
        )
    ): ?>
        <a
            class="button"
            href="<?= e(
                base_url(
                    'inventario/categorias'
                )
            ) ?>"
        >
            Administrar categorías
        </a>
    <?php endif; ?>
</section>

<div class="inventory-category-grid">
    <?php foreach ($categories as $category): ?>
        <a
            class="inventory-category-card"
            href="<?= e(
                base_url(
                    'inventario/categoria?id='
                    . $category['idCategoria']
                )
            ) ?>"
        >
            <div class="inventory-category-card__media">
                <?php if (!empty($category['imagen'])): ?>
                    <img
                        src="<?= e(
                            asset_url($category['imagen'])
                        ) ?>"
                        alt="<?= e(
                            $category['nombreCategoria']
                        ) ?>"
                    >
                <?php else: ?>
                    <span>
                        <?= e(
                            mb_substr(
                                $category['nombreCategoria'],
                                0,
                                1
                            )
                        ) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="inventory-category-card__content">
                <h2>
                    <?= e($category['nombreCategoria']) ?>
                </h2>

                <p>
                    <?= e(
                        $category['descripcion']
                        ?? 'Categoría del inventario.'
                    ) ?>
                </p>

                <div class="inventory-category-card__stats">
                    <span>
                        <strong>
                            <?= e($category['totalActivos']) ?>
                        </strong>
                        Total
                    </span>

                    <span>
                        <strong>
                            <?= e($category['enInventario']) ?>
                        </strong>
                        Disponibles
                    </span>

                    <span>
                        <strong>
                            <?= e($category['asignados']) ?>
                        </strong>
                        Asignados
                    </span>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>