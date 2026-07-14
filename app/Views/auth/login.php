<section class="auth-section">
    <div class="auth-card">
        <div class="auth-card__brand auth-card__brand--logo">
            <img
                class="auth-card__logo"
                src="<?= e(asset_url('assets/img/Logo-app.png')) ?>"
                alt="Logo de TrackiT"
            >
        </div>

        <div class="auth-card__header">
            <span class="section-heading__eyebrow">
                Acceso seguro
            </span>

            <h1>Bienvenido a TrackiT</h1>

            <p>
                Accede al sistema para administrar activos,
                inventario y trazabilidad tecnológica.
            </p>
        </div>

        <?php if (!empty($warning)): ?>
            <div class="alert alert--warning">
                <?= e($warning) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert--success">
                <?= e($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert--error">
                <?= e($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form
            method="POST"
            action="<?= e(base_url('login')) ?>"
        >
            <?= csrf_field() ?>

            <div class="field">
                <label for="identifier">
                    Usuario o correo
                </label>

                <input
                    id="identifier"
                    name="identifier"
                    type="text"
                    maxlength="120"
                    autocomplete="username"
                    value="<?= e(
                        $old['identifier'] ?? ''
                    ) ?>"
                    autofocus
                    required
                >

                <?php if (
                    !empty($errors['identifier'])
                ): ?>
                    <small class="field__error">
                        <?= e($errors['identifier']) ?>
                    </small>
                <?php endif; ?>
            </div>

            <div class="field auth-card__password">
                <label for="password">
                    Contraseña
                </label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    maxlength="255"
                    autocomplete="current-password"
                    required
                >

                <?php if (!empty($errors['password'])): ?>
                    <small class="field__error">
                        <?= e($errors['password']) ?>
                    </small>
                <?php endif; ?>
            </div>

            <button
                class="button auth-card__submit"
                type="submit"
            >
                Iniciar sesión
            </button>
        </form>

        <p class="auth-card__help">
            Si tu cuenta está bloqueada, contacta a un
            administrador del sistema.
        </p>
    </div>
</section>