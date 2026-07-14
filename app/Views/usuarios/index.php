<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            Administración
        </span>

        <h1>Usuarios del sistema</h1>

        <p>
            Consulta cuentas, roles, estados y bloqueos.
            Los usuarios se desactivan, pero no se eliminan.
        </p>
    </div>

    <a
        class="button"
        href="<?= e(base_url('usuarios/crear')) ?>"
    >
        Registrar usuario
    </a>
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

<form
    class="filters-card"
    method="GET"
    action="<?= e(base_url('usuarios')) ?>"
>
    <div class="filters-grid">
        <div class="field">
            <label for="search">Buscar</label>

            <input
                id="search"
                name="search"
                type="search"
                value="<?= e($filters['search']) ?>"
                placeholder="Nombre, cédula, usuario o correo"
            >
        </div>

        <div class="field">
            <label for="role">Rol</label>

            <select id="role" name="role">
                <option value="">Todos los roles</option>

                <?php foreach ($roles as $role): ?>
                    <option
                        value="<?= e($role['nombreRol']) ?>"
                        <?= (
                            $filters['role']
                            === $role['nombreRol']
                        ) ? 'selected' : '' ?>
                    >
                        <?= e($role['nombreRol']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label for="status">Estado</label>

            <select id="status" name="status">
                <option value="">Todos</option>

                <option
                    value="1"
                    <?= $filters['status'] === '1'
                        ? 'selected'
                        : '' ?>
                >
                    Activos
                </option>

                <option
                    value="0"
                    <?= $filters['status'] === '0'
                        ? 'selected'
                        : '' ?>
                >
                    Inactivos
                </option>
            </select>
        </div>

        <div class="filters-actions">
            <button class="button" type="submit">
                Filtrar
            </button>

            <a
                class="button button--secondary"
                href="<?= e(base_url('usuarios')) ?>"
            >
                Limpiar
            </a>
        </div>
    </div>
</form>

<div class="table-card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Seguridad</th>
                    <th>Último acceso</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($users === []): ?>
                    <tr>
                        <td
                            class="table-empty"
                            colspan="6"
                        >
                            No se encontraron usuarios.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <div class="user-cell">
                                <strong>
                                    <?= e(
                                        $user['nombre']
                                        . ' '
                                        . $user['apellido']
                                    ) ?>
                                </strong>

                                <span>
                                    <?= e($user['usuario']) ?>
                                    ·
                                    <?= e($user['correo']) ?>
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="badge badge--role">
                                <?= e($user['nombreRol']) ?>
                            </span>
                        </td>

                        <td>
                            <?php if ((bool) $user['activo']): ?>
                                <span class="badge badge--active">
                                    Activo
                                </span>
                            <?php else: ?>
                                <span class="badge badge--inactive">
                                    Inactivo
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ((bool) $user['bloqueado']): ?>
                                <span class="badge badge--blocked">
                                    Bloqueado
                                </span>
                            <?php else: ?>
                                <span class="badge badge--normal">
                                    Disponible
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= !empty($user['ultimoAcceso'])
                                ? e($user['ultimoAcceso'])
                                : 'Sin acceso' ?>
                        </td>

                        <td>
                            <div class="table-actions">
                                <a
                                    class="button button--small
                                        button--secondary"
                                    href="<?= e(
                                        base_url(
                                            'usuarios/editar?id='
                                            . $user['idUsuario']
                                        )
                                    ) ?>"
                                >
                                    Editar
                                </a>

                                <form
                                    class="inline-form"
                                    method="POST"
                                    action="<?= e(
                                        base_url('usuarios/estado')
                                    ) ?>"
                                >
                                    <?= csrf_field() ?>

                                    <input
                                        type="hidden"
                                        name="idUsuario"
                                        value="<?= e(
                                            $user['idUsuario']
                                        ) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="activo"
                                        value="<?= (bool) $user['activo']
                                            ? '0'
                                            : '1' ?>"
                                    >

                                    <button
                                        class="button button--small
                                            <?= (bool) $user['activo']
                                                ? 'button--danger'
                                                : '' ?>"
                                        type="submit"
                                    >
                                        <?= (bool) $user['activo']
                                            ? 'Desactivar'
                                            : 'Activar' ?>
                                    </button>
                                </form>

                                <?php if (
                                    (bool) $user['bloqueado']
                                ): ?>
                                    <form
                                        class="inline-form"
                                        method="POST"
                                        action="<?= e(
                                            base_url(
                                                'usuarios/desbloquear'
                                            )
                                        ) ?>"
                                    >
                                        <?= csrf_field() ?>

                                        <input
                                            type="hidden"
                                            name="idUsuario"
                                            value="<?= e(
                                                $user['idUsuario']
                                            ) ?>"
                                        >

                                        <button
                                            class="button button--small
                                                button--warning"
                                            type="submit"
                                        >
                                            Desbloquear
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>