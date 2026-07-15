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

        <div class="management-header__actions">
            <a
                class="button button--secondary"
                href="<?= e(
                    base_url(
                        'inventario/subcategoria?id='
                        . $product['idSubcategoria']
                    )
                ) ?>"
            >
                Volver a productos
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
                            'inventario/activos?producto='
                            . $product['idProducto']
                        )
                    ) ?>"
                >
                    Administrar copias
                </a>
            <?php endif; ?>
        </div>
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
                        <th>Ficha</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($assets === []): ?>
                        <tr>
                            <td
                                class="table-empty"
                                colspan="7"
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

                            <td>
                                <a
                                    class="button button--small button--secondary"
                                    href="<?= e(
                                        base_url(
                                            'inventario/activos/ver?id='
                                            . $asset['idActivo']
                                        )
                                    ) ?>"
                                >
                                    Ver ficha
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>