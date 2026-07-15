<section class="form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Administración
            </span>

            <h1>Editar usuario</h1>

            <p>
                Modifica la información, el rol o la contraseña
                de la cuenta seleccionada.
            </p>
        </div>

        <?php
        $action = base_url('usuarios/actualizar');
        $submitLabel = 'Guardar cambios';
        $isEdit = true;

        require BASE_PATH
            . '/app/Views/usuarios/_form.php';
        ?>
    </div>
</section>