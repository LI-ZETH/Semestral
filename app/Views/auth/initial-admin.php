<section class="form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Configuración inicial
            </span>

            <h1>Registrar primer administrador</h1>

            <p>
                Esta pantalla solo estará disponible mientras no
                exista un administrador activo.
            </p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert--error">
                <?= e($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form
            method="POST"
            action="<?= e(
                base_url(
                    'configuracion/primer-administrador'
                )
            ) ?>"
            autocomplete="off"
        >
            <?= csrf_field() ?>

            <div class="form-grid">
                <div class="field">
                    <label for="cedula">Cédula</label>

                    <input
                        id="cedula"
                        name="cedula"
                        type="text"
                        maxlength="25"
                        value="<?= e($old['cedula'] ?? '') ?>"
                        required
                    >

                    <?php if (!empty($errors['cedula'])): ?>
                        <small class="field__error">
                            <?= e($errors['cedula']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="usuario">
                        Nombre de usuario
                    </label>

                    <input
                        id="usuario"
                        name="usuario"
                        type="text"
                        maxlength="40"
                        value="<?= e($old['usuario'] ?? '') ?>"
                        required
                    >

                    <?php if (!empty($errors['usuario'])): ?>
                        <small class="field__error">
                            <?= e($errors['usuario']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="nombre">Nombre</label>

                    <input
                        id="nombre"
                        name="nombre"
                        type="text"
                        maxlength="60"
                        value="<?= e($old['nombre'] ?? '') ?>"
                        required
                    >

                    <?php if (!empty($errors['nombre'])): ?>
                        <small class="field__error">
                            <?= e($errors['nombre']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="apellido">Apellido</label>

                    <input
                        id="apellido"
                        name="apellido"
                        type="text"
                        maxlength="60"
                        value="<?= e($old['apellido'] ?? '') ?>"
                        required
                    >

                    <?php if (!empty($errors['apellido'])): ?>
                        <small class="field__error">
                            <?= e($errors['apellido']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field field--full">
                    <label for="correo">
                        Correo electrónico
                    </label>

                    <input
                        id="correo"
                        name="correo"
                        type="email"
                        maxlength="120"
                        value="<?= e($old['correo'] ?? '') ?>"
                        required
                    >

                    <?php if (!empty($errors['correo'])): ?>
                        <small class="field__error">
                            <?= e($errors['correo']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="password">Contraseña</label>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        required
                    >

                    <?php if (!empty($errors['password'])): ?>
                        <small class="field__error">
                            <?= e($errors['password']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="password_confirmation">
                        Confirmar contraseña
                    </label>

                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                    >

                    <?php if (
                        !empty(
                            $errors['password_confirmation']
                        )
                    ): ?>
                        <small class="field__error">
                            <?= e(
                                $errors[
                                    'password_confirmation'
                                ]
                            ) ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-actions">
                <button class="button" type="submit">
                    Crear administrador
                </button>
            </div>
        </form>
    </div>
</section>