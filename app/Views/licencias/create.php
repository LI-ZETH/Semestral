<section class="form-section">
    <div class="form-card form-card--wide">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">Software</span>
            <h1>Registrar licencia</h1>
            <p>
                Vincula los datos comerciales y de acceso a una copia de
                producto registrada como licencia.
            </p>
        </div>

        <?php
        $action = base_url('licencias/guardar');
        $cancelUrl = base_url('licencias');
        $submitLabel = 'Registrar licencia';
        $license = $old;
        $isEdit = false;

        require BASE_PATH . '/app/Views/licencias/_form.php';
        ?>
    </div>
</section>
