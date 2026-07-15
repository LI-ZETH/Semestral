<section class="form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Inventario
            </span>

            <h1>Editar categoría</h1>

            <p>
                Actualiza su nombre, descripción o imagen.
            </p>
        </div>

        <?php
        $action = base_url(
            'inventario/categorias/actualizar'
        );

        $submitLabel = 'Guardar cambios';
        $isEdit = true;

        require BASE_PATH
            . '/app/Views/inventario/categories/_form.php';
        ?>
    </div>
</section>