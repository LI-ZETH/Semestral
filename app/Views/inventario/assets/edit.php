<section class="form-section form-section--wide">
    <div class="form-card form-card--wide">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Inventario · Copias individuales
            </span>

            <h1>Editar copia</h1>

            <p>
                Actualiza la información, la ubicación, el estado y las
                imágenes de este activo.
            </p>
        </div>

        <?php
        $action = base_url('inventario/activos/actualizar');
        $submitLabel = 'Guardar cambios';
        $isEdit = true;

        require BASE_PATH
            . '/app/Views/inventario/assets/_form.php';
        ?>
    </div>
</section>
