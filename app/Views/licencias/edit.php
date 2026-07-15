<section class="form-section">
    <div class="form-card form-card--wide">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">Software</span>
            <h1>Editar licencia</h1>
            <p>
                Actualiza proveedor, puestos, vigencia, acceso y datos de
                renovación.
            </p>
        </div>

        <?php
        $action = base_url('licencias/actualizar');
        $cancelUrl = base_url(
            'licencias/ver?id=' . $license['idLicencia']
        );
        $submitLabel = 'Guardar cambios';
        $isEdit = true;

        require BASE_PATH . '/app/Views/licencias/_form.php';
        ?>
    </div>
</section>
