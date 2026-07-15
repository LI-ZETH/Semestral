<?php

declare(strict_types=1);

use App\Core\Auth;
?>
<header class="site-header">
    <div class="site-header__container">
        <a
            class="brand"
            href="<?= e(base_url()) ?>"
            aria-label="Ir al inicio"
        >
            <img
                class="brand__logo"
                src="<?= e(asset_url('assets/img/Logo-app.png')) ?>"
                alt="Logo de TrackiT"
            >
        </a>

        <nav
            class="main-navigation"
            aria-label="Navegación principal"
        >
            <a href="<?= e(base_url()) ?>">
                Inicio
            </a>

            <?php if (Auth::check()): ?>
            <a href="<?= e(base_url('panel')) ?>">
                Panel
            </a>

            <a href="<?= e(base_url('perfil')) ?>">
                Mi perfil
            </a>

            <?php if (
                Auth::can(
                    \App\Core\Permissions::USUARIOS_VER
                )
            ): ?>
                <a href="<?= e(base_url('usuarios')) ?>">
                    Usuarios
                </a>
            <?php endif; ?>

            <?php if (
                Auth::hasRole(
                    \App\Core\Roles::ADMINISTRADOR
                )
            ): ?>
                <a href="<?= e(base_url('reportes')) ?>">
                    Reportes
                </a>
            <?php endif; ?>

            <div class="navigation-user">
                <strong>
                    <?= e(
                        Auth::user()['nombre']
                        ?? 'Usuario'
                    ) ?>
                </strong>

                <small>
                    <?= e(Auth::role() ?? '') ?>
                </small>
            </div>

            <form
                class="logout-form"
                method="POST"
                action="<?= e(base_url('logout')) ?>"
            >
                <?= csrf_field() ?>

                <button
                    class="navigation-button"
                    type="submit"
                >
                    Cerrar sesión
                </button>
            </form>
        <?php else: ?>
            <a href="<?= e(base_url('login')) ?>">
                Iniciar sesión
            </a>
        <?php endif; ?>
        </nav>
    </div>
</header>