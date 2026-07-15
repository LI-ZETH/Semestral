<section class="form-section profile-password-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Seguridad de la cuenta
            </span>

            <h1>Cambiar contraseña</h1>

            <p>
                Confirma tu contraseña actual y establece una nueva clave
                segura para proteger el acceso a TrackiT.
            </p>
        </div>

        <form
            method="POST"
            action="<?= e(base_url('perfil/contrasena')) ?>"
        >
            <?= csrf_field() ?>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert--error">
                    <?= e($errors['general']) ?>
                </div>
            <?php endif; ?>

            <div class="form-grid">
                <div class="field field--full">
                    <label for="contrasenaActual">
                        Contraseña actual
                    </label>

                    <input
                        id="contrasenaActual"
                        name="contrasenaActual"
                        type="password"
                        autocomplete="current-password"
                        required
                    >

                    <?php if (
                        !empty($errors['contrasenaActual'])
                    ): ?>
                        <small class="field__error">
                            <?= e($errors['contrasenaActual']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="contrasenaNueva">
                        Nueva contraseña
                    </label>

                    <input
                        id="contrasenaNueva"
                        name="contrasenaNueva"
                        type="password"
                        autocomplete="new-password"
                        required
                    >

                    <?php if (
                        !empty($errors['contrasenaNueva'])
                    ): ?>
                        <div class="field__error password-error-list">
                            <?php foreach (
                                (array) $errors['contrasenaNueva']
                                as $passwordError
                            ): ?>
                                <span><?= e($passwordError) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="confirmarContrasena">
                        Confirmar nueva contraseña
                    </label>

                    <input
                        id="confirmarContrasena"
                        name="confirmarContrasena"
                        type="password"
                        autocomplete="new-password"
                        required
                    >

                    <?php if (
                        !empty($errors['confirmarContrasena'])
                    ): ?>
                        <small class="field__error">
                            <?= e(
                                $errors['confirmarContrasena']
                            ) ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="password-requirements">
                <strong>La nueva contraseña debe contener:</strong>

                <span>10 caracteres o más</span>
                <span>Una mayúscula y una minúscula</span>
                <span>Un número</span>
                <span>Un carácter especial</span>
            </div>

            <div class="form-actions">
                <a
                    class="button button--secondary"
                    href="<?= e(base_url('perfil')) ?>"
                >
                    Cancelar
                </a>

                <button class="button" type="submit">
                    Actualizar contraseña
                </button>
            </div>
        </form>
    </div>
</section>
