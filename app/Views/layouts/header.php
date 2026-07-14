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
            <span class="brand__symbol">T</span>

            <span class="brand__text">
                Tránsito
                <strong>CMDB</strong>
            </span>
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

                <span class="navigation-user">
                    <?= e(
                        Auth::user()['nombre']
                        ?? 'Usuario'
                    ) ?>
                </span>

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