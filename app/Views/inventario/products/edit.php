<section class="form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Inventario
            </span>

            <h1>Editar producto</h1>

            <p>
                Actualiza su clasificación, información general,
                vida útil o imagen.
            </p>
        </div>

        <?php
        $action = base_url('inventario/productos/actualizar');
        $submitLabel = 'Guardar cambios';
        $isEdit = true;

        require BASE_PATH
            . '/app/Views/inventario/products/_form.php';
        ?>
    </div>
</section>
