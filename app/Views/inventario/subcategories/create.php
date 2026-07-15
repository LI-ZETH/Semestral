<section class="form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Inventario
            </span>

            <h1>Registrar subcategoría</h1>

            <p>
                Crea una división específica dentro de una
                categoría principal.
            </p>
        </div>

        <?php
        $action = base_url(
            'inventario/subcategorias/guardar'
        );

        $submitLabel = 'Registrar subcategoría';
        $subcategory = $old;
        $isEdit = false;

        require BASE_PATH
            . '/app/Views/inventario/subcategories/_form.php';
        ?>
    </div>
</section>