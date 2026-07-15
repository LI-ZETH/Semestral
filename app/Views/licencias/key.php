<section class="form-section auth-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">Verificación de seguridad</span>
            <h1>Mostrar clave</h1>
            <p>
                Confirma tu contraseña para descifrar temporalmente la clave de
                <?= e($license['nombreProducto']) ?>.
            </p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert--error"><?= e($errors['general']) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= e(base_url('licencias/clave')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="idLicencia" value="<?= e($license['idLicencia']) ?>">

            <div class="field">
                <label for="contrasenaActual">Contraseña actual</label>
                <input
                    id="contrasenaActual"
                    name="contrasenaActual"
                    type="password"
                    autocomplete="current-password"
                    required
                >
                <?php if (!empty($errors['contrasenaActual'])): ?>
                    <small class="field__error"><?= e($errors['contrasenaActual']) ?></small>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <a
                    class="button button--secondary"
                    href="<?= e(base_url('licencias/ver?id=' . $license['idLicencia'])) ?>"
                >
                    Cancelar
                </a>
                <button class="button" type="submit">Descifrar clave</button>
            </div>
        </form>
    </div>
</section>
