<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            <?= e($subcategory['nombreCategoria']) ?>
        </span>

        <h1>
            <?= e(
                $subcategory['nombreSubcategoria']
            ) ?>
        </h1>

        <p>
            <?= e(
                $subcategory['descripcion']
                ?? 'Productos de esta subcategoría.'
            ) ?>
        </p>
    </div>

    <div class="management-header__actions">
        <a
            class="button button--secondary"
            href="<?= e(
                base_url(
                    'inventario/categoria?id='
                    . $subcategory['idCategoria']
                )
            ) ?>"
        >
            Volver a subcategorías
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
                        'inventario/productos?subcategoria='
                        . $subcategory['idSubcategoria']
                    )
                ) ?>"
            >
                Administrar productos
            </a>
        <?php endif; ?>
    </div>
</section>

<div class="inventory-product-grid">
    <?php if ($products === []): ?>
        <div class="empty-state">
            <h2>No hay productos registrados</h2>

            <p>
                Esta subcategoría todavía no contiene productos.
            </p>
        </div>
    <?php endif; ?>

    <?php foreach ($products as $product): ?>
        <a
            class="inventory-product-card"
            href="<?= e(
                base_url(
                    'inventario/producto?id='
                    . $product['idProducto']
                )
            ) ?>"
        >
            <div class="inventory-product-card__image">
                <?php if (!empty($product['imagen'])): ?>
                    <img
                        src="<?= e(
                            asset_url($product['imagen'])
                        ) ?>"
                        alt="<?= e(
                            $product['nombreProducto']
                        ) ?>"
                    >
                <?php else: ?>
                    <span>Sin imagen</span>
                <?php endif; ?>
            </div>

            <div class="inventory-product-card__body">
                <h2>
                    <?= e($product['nombreProducto']) ?>
                </h2>

                <p class="inventory-product-card__model">
                    <?= e(
                        trim(
                            ($product['marca'] ?? '')
                            . ' '
                            . ($product['modelo'] ?? '')
                        )
                    ) ?>
                </p>

                <div class="inventory-product-card__stats">
                    <span>
                        <strong>
                            <?= e($product['totalActivos']) ?>
                        </strong>
                        Copias
                    </span>

                    <span>
                        <strong>
                            <?= e($product['disponibles']) ?>
                        </strong>
                        Disponibles
                    </span>

                    <span>
                        <strong>
                            <?= e($product['asignados']) ?>
                        </strong>
                        Asignadas
                    </span>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>