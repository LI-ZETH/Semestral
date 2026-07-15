<section class="form-section profile-form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Cuenta personal
            </span>

            <h1>Editar mi perfil</h1>

            <p>
                Actualiza tus datos personales. El nombre de usuario y el rol
                solamente pueden ser modificados por un administrador.
            </p>
        </div>

        <form
            method="POST"
            action="<?= e(base_url('perfil/actualizar')) ?>"
        >
            <?= csrf_field() ?>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert--error">
                    <?= e($errors['general']) ?>
                </div>
            <?php endif; ?>

            <div class="form-grid">
                <div class="field">
                    <label for="nombre">Nombre</label>

                    <input
                        id="nombre"
                        name="nombre"
                        type="text"
                        maxlength="60"
                        value="<?= e($profile['nombre']) ?>"
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
                        value="<?= e($profile['apellido']) ?>"
                        required
                    >

                    <?php if (!empty($errors['apellido'])): ?>
                        <small class="field__error">
                            <?= e($errors['apellido']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="cedula">Identificación</label>

                    <input
                        id="cedula"
                        name="cedula"
                        type="text"
                        maxlength="25"
                        value="<?= e($profile['cedula'] ?? '') ?>"
                        <?= $profile['nombreRol'] === 'Colaborador'
                            ? 'required'
                            : '' ?>
                    >

                    <?php if (!empty($errors['cedula'])): ?>
                        <small class="field__error">
                            <?= e($errors['cedula']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="correo">Correo electrónico</label>

                    <input
                        id="correo"
                        name="correo"
                        type="email"
                        maxlength="120"
                        value="<?= e($profile['correo']) ?>"
                        required
                    >

                    <?php if (!empty($errors['correo'])): ?>
                        <small class="field__error">
                            <?= e($errors['correo']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="usuario">Nombre de usuario</label>

                    <input
                        id="usuario"
                        type="text"
                        value="<?= e($profile['usuario']) ?>"
                        disabled
                    >

                    <small class="field__help">
                        Solicita el cambio al administrador.
                    </small>
                </div>

                <div class="field">
                    <label for="rol">Rol</label>

                    <input
                        id="rol"
                        type="text"
                        value="<?= e($profile['nombreRol']) ?>"
                        disabled
                    >
                </div>
            </div>

            <?php if (!empty($profile['idColaborador'])): ?>
                <div class="form-subsection">
                    <div class="form-subsection__header">
                        <h2>Información laboral</h2>

                        <p>
                            Estos datos ayudan a identificar tu área y facilitan
                            la atención de solicitudes técnicas.
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
                                value="<?= e($profile['telefono'] ?? '') ?>"
                            >

                            <?php if (!empty($errors['telefono'])): ?>
                                <small class="field__error">
                                    <?= e($errors['telefono']) ?>
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
                                value="<?= e($profile['cargo'] ?? '') ?>"
                            >

                            <?php if (!empty($errors['cargo'])): ?>
                                <small class="field__error">
                                    <?= e($errors['cargo']) ?>
                                </small>
                            <?php endif; ?>
                        </div>

                        <div class="field field--full">
                            <label for="departamento">Departamento</label>

                            <input
                                id="departamento"
                                name="departamento"
                                type="text"
                                maxlength="100"
                                value="<?= e(
                                    $profile['departamento'] ?? ''
                                ) ?>"
                            >

                            <?php if (
                                !empty($errors['departamento'])
                            ): ?>
                                <small class="field__error">
                                    <?= e($errors['departamento']) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="form-subsection">
                    <div class="form-subsection__header">
                        <h2>Ubicación actual</h2>

                        <p>
                            La ubicación seleccionada se utilizará en futuras
                            solicitudes y reportes de reparación. El cambio queda
                            registrado en el historial.
                        </p>
                    </div>

                    <div class="form-grid">
                        <div class="field field--full">
                            <label for="idUbicacion">
                                Ubicación
                            </label>

                            <select
                                id="idUbicacion"
                                name="idUbicacion"
                            >
                                <option value="">
                                    Sin ubicación registrada
                                </option>

                                <?php foreach ($locations as $location): ?>
                                    <option
                                        value="<?= e(
                                            $location['idUbicacion']
                                        ) ?>"
                                        <?= (
                                            (int) (
                                                $profile['idUbicacionActual']
                                                ?? 0
                                            )
                                            ===
                                            (int) $location['idUbicacion']
                                        ) ? 'selected' : '' ?>
                                    >
                                        <?= e(
                                            $location['nombreUbicacion']
                                            . (!empty($location['edificio'])
                                                ? ' — '
                                                    . $location['edificio']
                                                : '')
                                            . (!empty($location['piso'])
                                                ? ', '
                                                    . $location['piso']
                                                : '')
                                        ) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <?php if (!empty($errors['idUbicacion'])): ?>
                                <small class="field__error">
                                    <?= e($errors['idUbicacion']) ?>
                                </small>
                            <?php endif; ?>
                        </div>

                        <div class="field field--full">
                            <label for="observacionesUbicacion">
                                Observación del cambio
                            </label>

                            <textarea
                                id="observacionesUbicacion"
                                name="observacionesUbicacion"
                                maxlength="255"
                                placeholder="Ejemplo: traslado temporal al piso 4."
                            ><?= e(
                                $profile['observacionesUbicacion']
                                ?? ''
                            ) ?></textarea>

                            <?php if (
                                !empty(
                                    $errors['observacionesUbicacion']
                                )
                            ): ?>
                                <small class="field__error">
                                    <?= e(
                                        $errors[
                                            'observacionesUbicacion'
                                        ]
                                    ) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <a
                    class="button button--secondary"
                    href="<?= e(base_url('perfil')) ?>"
                >
                    Cancelar
                </a>

                <button class="button" type="submit">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</section>
