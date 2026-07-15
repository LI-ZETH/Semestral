<section class="form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Ubicaciones
            </span>

            <h1>Editar ubicación</h1>

            <p>
                Actualiza los datos físicos de esta ubicación.
            </p>
        </div>

        <?php
        $action = base_url('ubicaciones/actualizar');
        $submitLabel = 'Guardar cambios';

        require BASE_PATH
            . '/app/Views/ubicaciones/_form.php';
        ?>
    </div>
</section>
