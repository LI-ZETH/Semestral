<section class="product-detail-header">
    <div class="product-detail-header__image">
        <?php if (!empty($product['imagen'])): ?>
            <img
                src="<?= e(asset_url($product['imagen'])) ?>"
                alt="<?= e($product['nombreProducto']) ?>"
            >
        <?php else: ?>
            <span>Sin imagen</span>
        <?php endif; ?>
    </div>

    <div class="product-detail-header__content">
        <span class="section-heading__eyebrow">
            <?= e($product['nombreCategoria']) ?>
            ·
            <?= e($product['nombreSubcategoria']) ?>
        </span>

        <h1>
            <?= e($product['nombreProducto']) ?>
        </h1>

        <p class="product-detail-header__model">
            <?= e(
                trim(
                    ($product['marca'] ?? '')
                    . ' '
                    . ($product['modelo'] ?? '')
                )
            ) ?>
        </p>

        <p>
            <?= e(
                $product['descripcion']
                ?? 'Este producto no tiene descripción.'
            ) ?>
        </p>

        <a
            class="button button--secondary"
            href="<?= e(
                base_url(
                    'inventario/categoria?id='
                    . $product['idCategoria']
                )
            ) ?>"
        >
            Volver a productos
        </a>
    </div>
</section>

<section class="dashboard-section">
    <div class="section-heading">
        <span class="section-heading__eyebrow">
            Copias individuales
        </span>

        <h2>Activos registrados</h2>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Serie</th>
                        <th>Estado</th>
                        <th>Ubicación</th>
                        <th>Custodio</th>
                        <th>Imágenes</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($assets === []): ?>
                        <tr>
                            <td
                                class="table-empty"
                                colspan="6"
                            >
                                No hay copias registradas.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($assets as $asset): ?>
                        <tr>
                            <td>
                                <strong>
                                    <?= e($asset['codigoActivo']) ?>
                                </strong>
                            </td>

                            <td>
                                <?= e(
                                    $asset['numeroSerie']
                                    ?? 'Sin serie'
                                ) ?>
                            </td>

                            <td>
                                <span class="badge badge--normal">
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
                                <?= e(
                                    $asset['nombreColaborador']
                                    ?? 'Sin asignar'
                                ) ?>
                            </td>

                            <td>
                                <?= e($asset['cantidadImagenes']) ?>
                                imagen(es)
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>