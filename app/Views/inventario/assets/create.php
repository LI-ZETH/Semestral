<section class="form-section">
    <div class="form-card form-card--wide">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Inventario · Copias individuales
            </span>

            <h1>Registrar copia</h1>

            <p>
                Registra un activo físico o digital específico y adjunta
                al menos dos imágenes como evidencia.
            </p>
        </div>

        <?php
        $action = base_url('inventario/activos/guardar');
        $submitLabel = 'Registrar copia';
        $asset = $old;
        $isEdit = false;

        require BASE_PATH
            . '/app/Views/inventario/assets/_form.php';
        ?>
    </div>
</section>
