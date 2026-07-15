<section class="form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Ubicaciones
            </span>

            <h1>Registrar ubicación</h1>

            <p>
                Registra edificios, oficinas, casas o bodegas
                donde se encuentran colaboradores y activos.
            </p>
        </div>

        <?php
        $action = base_url('ubicaciones/guardar');
        $submitLabel = 'Registrar ubicación';
        $location = $old;

        require BASE_PATH
            . '/app/Views/ubicaciones/_form.php';
        ?>
    </div>
</section>
