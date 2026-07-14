<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            <?= e($category['nombreCategoria']) ?>
        </span>

        <h1>Productos registrados</h1>

        <p>
            <?= e(
                $category['descripcion']
                ?? 'Productos asociados a la categoría.'
            ) ?>
        </p>
    </div>

    <a
        class="button button--secondary"
        href="<?= e(base_url('inventario')) ?>"
    >
        Volver a categorías
    </a>
</section>

<div class="inventory-product-grid">
    <?php if ($products === []): ?>
        <div class="empty-state">
            <h2>No hay productos registrados</h2>

            <p>
                Esta categoría todavía no contiene productos.
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
                        alt="<?= e($product['nombreProducto']) ?>"
                    >
                <?php else: ?>
                    <span>Sin imagen</span>
                <?php endif; ?>
            </div>

            <div class="inventory-product-card__body">
                <span class="badge badge--role">
                    <?= e($product['nombreSubcategoria']) ?>
                </span>

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