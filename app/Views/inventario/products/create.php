<section class="form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Inventario
            </span>

            <h1>Registrar producto</h1>

            <p>
                Registra el modelo general que posteriormente
                tendrá una o varias copias individuales.
            </p>
        </div>

        <?php
        $action = base_url('inventario/productos/guardar');
        $submitLabel = 'Registrar producto';
        $product = $old;
        $isEdit = false;

        require BASE_PATH
            . '/app/Views/inventario/products/_form.php';
        ?>
    </div>
</section>
