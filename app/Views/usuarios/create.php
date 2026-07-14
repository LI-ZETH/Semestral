<section class="form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Administración
            </span>

            <h1>Registrar usuario</h1>

            <p>
                Crea una cuenta y asigna uno de los tres
                roles disponibles en Tránsito CMDB.
            </p>
        </div>

        <?php
        $action = base_url('usuarios/guardar');
        $submitLabel = 'Registrar usuario';
        $user = $old;
        $isEdit = false;

        require BASE_PATH
            . '/app/Views/usuarios/_form.php';
        ?>
    </div>
</section>