<section class="form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Inventario
            </span>

            <h1>Registrar categoría</h1>

            <p>
                Crea una clasificación principal para
                organizar productos y activos.
            </p>
        </div>

        <?php
        $action = base_url(
            'inventario/categorias/guardar'
        );

        $submitLabel = 'Registrar categoría';
        $category = $old;
        $isEdit = false;

        require BASE_PATH
            . '/app/Views/inventario/categories/_form.php';
        ?>
    </div>
</section>