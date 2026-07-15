<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            Cuenta personal
        </span>

        <h1>Mi perfil</h1>

        <p>
            Consulta tus datos de acceso, información personal
            y ubicación registrada en TrackiT.
        </p>
    </div>

    <div class="management-header__actions">
        <a
            class="button button--secondary"
            href="<?= e(base_url('panel')) ?>"
        >
            Volver al panel
        </a>

        <a
            class="button button--secondary"
            href="<?= e(base_url('perfil/contrasena')) ?>"
        >
            Cambiar contraseña
        </a>

        <a
            class="button"
            href="<?= e(base_url('perfil/editar')) ?>"
        >
            Editar perfil
        </a>
    </div>
</section>

<?php if (!empty($success)): ?>
    <div class="alert alert--success">
        <?= e($success) ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert--error">
        <?= e($error) ?>
    </div>
<?php endif; ?>

<section class="profile-layout">
    <article class="profile-summary-card">
        <div class="profile-avatar" aria-hidden="true">
            <?= e(
                mb_strtoupper(
                    mb_substr(
                        (string) $profile['nombre'],
                        0,
                        1
                    )
                    . mb_substr(
                        (string) $profile['apellido'],
                        0,
                        1
                    )
                )
            ) ?>
        </div>

        <div class="profile-summary-card__content">
            <span class="badge badge--normal">
                <?= e($profile['nombreRol']) ?>
            </span>

            <h2>
                <?= e(
                    $profile['nombre']
                    . ' '
                    . $profile['apellido']
                ) ?>
            </h2>

            <p>@<?= e($profile['usuario']) ?></p>
        </div>
    </article>

    <article class="profile-information-card">
        <div class="profile-section-heading">
            <div>
                <span class="section-heading__eyebrow">
                    Información de la cuenta
                </span>

                <h2>Datos personales</h2>
            </div>
        </div>

        <dl class="profile-detail-grid">
            <div>
                <dt>Identificación</dt>
                <dd>
                    <?= e(
                        $profile['cedula']
                        ?? 'No registrada'
                    ) ?>
                </dd>
            </div>

            <div>
                <dt>Correo electrónico</dt>
                <dd><?= e($profile['correo']) ?></dd>
            </div>

            <div>
                <dt>Nombre de usuario</dt>
                <dd><?= e($profile['usuario']) ?></dd>
            </div>

            <div>
                <dt>Rol</dt>
                <dd><?= e($profile['nombreRol']) ?></dd>
            </div>

            <div>
                <dt>Último acceso</dt>
                <dd>
                    <?= e(
                        $profile['ultimoAcceso']
                        ?? 'Sin acceso previo registrado'
                    ) ?>
                </dd>
            </div>

            <div>
                <dt>Fecha de registro</dt>
                <dd><?= e($profile['fechaRegistro']) ?></dd>
            </div>
        </dl>
    </article>

    <?php if (!empty($profile['idColaborador'])): ?>
        <article class="profile-information-card">
            <div class="profile-section-heading">
                <div>
                    <span class="section-heading__eyebrow">
                        Información laboral
                    </span>

                    <h2>Datos del colaborador</h2>
                </div>
            </div>

            <dl class="profile-detail-grid">
                <div>
                    <dt>Teléfono</dt>
                    <dd>
                        <?= e(
                            $profile['telefono']
                            ?? 'No registrado'
                        ) ?>
                    </dd>
                </div>

                <div>
                    <dt>Cargo</dt>
                    <dd>
                        <?= e(
                            $profile['cargo']
                            ?? 'No registrado'
                        ) ?>
                    </dd>
                </div>

                <div>
                    <dt>Departamento</dt>
                    <dd>
                        <?= e(
                            $profile['departamento']
                            ?? 'No registrado'
                        ) ?>
                    </dd>
                </div>

                <div>
                    <dt>Fecha de ingreso</dt>
                    <dd>
                        <?= e(
                            $profile['fechaIngreso']
                            ?? 'No registrada'
                        ) ?>
                    </dd>
                </div>
            </dl>
        </article>

        <article class="profile-information-card profile-location-card">
            <div class="profile-section-heading">
                <div>
                    <span class="section-heading__eyebrow">
                        Ubicación actual
                    </span>

                    <h2>
                        <?= e(
                            $profile['nombreUbicacion']
                            ?? 'Sin ubicación registrada'
                        ) ?>
                    </h2>
                </div>
            </div>

            <?php if (!empty($profile['idUbicacionActual'])): ?>
                <dl class="profile-detail-grid">
                    <div>
                        <dt>Tipo</dt>
                        <dd><?= e($profile['tipoUbicacion']) ?></dd>
                    </div>

                    <div>
                        <dt>Edificio</dt>
                        <dd>
                            <?= e(
                                $profile['edificio']
                                ?? 'No indicado'
                            ) ?>
                        </dd>
                    </div>

                    <div>
                        <dt>Piso</dt>
                        <dd>
                            <?= e(
                                $profile['piso']
                                ?? 'No indicado'
                            ) ?>
                        </dd>
                    </div>

                    <div>
                        <dt>Oficina</dt>
                        <dd>
                            <?= e(
                                $profile['oficina']
                                ?? 'No indicada'
                            ) ?>
                        </dd>
                    </div>

                    <div class="profile-detail-grid__full">
                        <dt>Dirección</dt>
                        <dd>
                            <?= e(
                                $profile['direccion']
                                ?? 'No indicada'
                            ) ?>
                        </dd>
                    </div>
                </dl>
            <?php else: ?>
                <div class="profile-warning">
                    No tienes una ubicación registrada. Actualiza tu perfil
                    para que los técnicos puedan localizarte cuando reportes
                    una reparación.
                </div>
            <?php endif; ?>
        </article>

        <article class="profile-information-card profile-history-card">
            <div class="profile-section-heading">
                <div>
                    <span class="section-heading__eyebrow">
                        Historial
                    </span>

                    <h2>Ubicaciones recientes</h2>
                </div>
            </div>

            <?php if ($profile['locationHistory'] === []): ?>
                <div class="empty-state profile-empty-state">
                    <p>No existe historial de ubicaciones.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table profile-history-table">
                        <thead>
                            <tr>
                                <th>Ubicación</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Estado</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach (
                                $profile['locationHistory'] as $history
                            ): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?= e(
                                                $history['nombreUbicacion']
                                            ) ?>
                                        </strong>
                                    </td>

                                    <td><?= e($history['fechaInicio']) ?></td>

                                    <td>
                                        <?= e(
                                            $history['fechaFin']
                                            ?? 'Actual'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?php if (
                                            (bool) $history['esActual']
                                        ): ?>
                                            <span class="badge badge--active">
                                                Actual
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge--inactive">
                                                Anterior
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </article>
    <?php endif; ?>
</section>
