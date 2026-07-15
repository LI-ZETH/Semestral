<form method="POST" action="<?= e($action) ?>">
    <?= csrf_field() ?>

    <?php if (!empty($user['idUsuario'])): ?>
        <input
            type="hidden"
            name="idUsuario"
            value="<?= e($user['idUsuario']) ?>"
        >
    <?php endif; ?>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert--error">
            <?= e($errors['general']) ?>
        </div>
    <?php endif; ?>

    <div class="form-grid">
        <div class="field">
            <label for="cedula">Cédula</label>

            <input
                id="cedula"
                name="cedula"
                type="text"
                maxlength="25"
                value="<?= e($user['cedula'] ?? '') ?>"
                required
            >

            <?php if (!empty($errors['cedula'])): ?>
                <small class="field__error">
                    <?= e($errors['cedula']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="nombreRol">Rol</label>

            <select
                id="nombreRol"
                name="nombreRol"
                required
            >
                <option value="">Selecciona un rol</option>

                <?php foreach ($roles as $role): ?>
                    <option
                        value="<?= e($role['nombreRol']) ?>"
                        <?= (
                            ($user['nombreRol'] ?? '')
                            === $role['nombreRol']
                        ) ? 'selected' : '' ?>
                    >
                        <?= e($role['nombreRol']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if (!empty($errors['nombreRol'])): ?>
                <small class="field__error">
                    <?= e($errors['nombreRol']) ?>
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
                value="<?= e($user['nombre'] ?? '') ?>"
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
                value="<?= e($user['apellido'] ?? '') ?>"
                required
            >

            <?php if (!empty($errors['apellido'])): ?>
                <small class="field__error">
                    <?= e($errors['apellido']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="usuario">Usuario</label>

            <input
                id="usuario"
                name="usuario"
                type="text"
                maxlength="40"
                value="<?= e($user['usuario'] ?? '') ?>"
                required
            >

            <?php if (!empty($errors['usuario'])): ?>
                <small class="field__error">
                    <?= e($errors['usuario']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="correo">Correo</label>

            <input
                id="correo"
                name="correo"
                type="email"
                maxlength="120"
                value="<?= e($user['correo'] ?? '') ?>"
                required
            >

            <?php if (!empty($errors['correo'])): ?>
                <small class="field__error">
                    <?= e($errors['correo']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="password">
                Contraseña
                <?php if ($isEdit): ?>
                    <span class="field__optional">
                        (dejar vacía para conservarla)
                    </span>
                <?php endif; ?>
            </label>

            <input
                id="password"
                name="password"
                type="password"
                autocomplete="new-password"
                <?= $isEdit ? '' : 'required' ?>
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
                <?= $isEdit ? '' : 'required' ?>
            >

            <?php if (
                !empty($errors['password_confirmation'])
            ): ?>
                <small class="field__error">
                    <?= e(
                        $errors['password_confirmation']
                    ) ?>
                </small>
            <?php endif; ?>
        </div>
    </div>

    <section class="form-subsection">
        <div class="form-subsection__header">
            <h2>Información del colaborador</h2>

            <p>
                Estos campos se guardarán cuando el rol
                seleccionado sea Colaborador.
            </p>
        </div>

        <div class="form-grid">
            <div class="field">
                <label for="telefono">Teléfono</label>

                <input
                    id="telefono"
                    name="telefono"
                    type="text"
                    maxlength="25"
                    value="<?= e($user['telefono'] ?? '') ?>"
                >

                <?php if (!empty($errors['telefono'])): ?>
                    <small class="field__error">
                        <?= e($errors['telefono']) ?>
                    </small>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="fechaIngreso">
                    Fecha de ingreso
                </label>

                <input
                    id="fechaIngreso"
                    name="fechaIngreso"
                    type="date"
                    value="<?= e(
                        $user['fechaIngreso'] ?? ''
                    ) ?>"
                >

                <?php if (
                    !empty($errors['fechaIngreso'])
                ): ?>
                    <small class="field__error">
                        <?= e($errors['fechaIngreso']) ?>
                    </small>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="cargo">Cargo</label>

                <input
                    id="cargo"
                    name="cargo"
                    type="text"
                    maxlength="100"
                    value="<?= e($user['cargo'] ?? '') ?>"
                >
            </div>

            <div class="field">
                <label for="departamento">
                    Departamento
                </label>

                <input
                    id="departamento"
                    name="departamento"
                    type="text"
                    maxlength="100"
                    value="<?= e(
                        $user['departamento'] ?? ''
                    ) ?>"
                >
            </div>
        </div>
    </section>

    <div class="form-actions">
        <a
            class="button button--secondary"
            href="<?= e(base_url('usuarios')) ?>"
        >
            Cancelar
        </a>

        <button class="button" type="submit">
            <?= e($submitLabel) ?>
        </button>
    </div>
</form>