<?php

declare(strict_types=1);

use App\Core\Auth;

$sessionUrl = Auth::check()
    ? base_url('panel')
    : base_url('login');

$sessionLabel = Auth::check()
    ? 'Ir al panel'
    : 'Iniciar sesión';
?>
<header class="public-header">
    <div class="nav-container">
        <a
            class="logo"
            href="<?= e(base_url()) ?>"
            aria-label="Ir al inicio de TrackiT"
        >
            <img
                src="<?= e(asset_url('assets/img/public/Logo-app.png')) ?>"
                alt="Logo de TrackiT"
            >

            <span class="teams-title">
                Semestral
            </span>
        </a>

        <nav
            class="nav__container"
            aria-label="Navegación del sitio público"
        >
            <ul class="nav-items__container">
                <li class="<?= ($activePage ?? '') === 'inicio' ? 'active' : '' ?>">
                    <a href="<?= e(base_url()) ?>">Inicio</a>
                </li>

                <li class="<?= ($activePage ?? '') === 'funcionalidades' ? 'active' : '' ?>">
                    <a href="<?= e(base_url('funcionalidades')) ?>">
                        Funcionalidades
                    </a>
                </li>

                <li class="<?= ($activePage ?? '') === 'noticias' ? 'active' : '' ?>">
                    <a href="<?= e(base_url('noticias')) ?>">
                        Noticias
                    </a>
                </li>

                <li class="<?= ($activePage ?? '') === 'nosotros' ? 'active' : '' ?>">
                    <a href="<?= e(base_url('nosotros')) ?>">
                        Nosotros
                    </a>
                </li>
            </ul>
        </nav>

        <div class="public-header__actions">
            <a
                class="manual-link <?= ($activePage ?? '') === 'ayuda' ? 'active' : '' ?>"
                href="<?= e(base_url('ayuda')) ?>"
            >
                <span aria-hidden="true">▤</span>
                Manual de usuario
            </a>

            <a
                class="sign-in-button"
                href="<?= e($sessionUrl) ?>"
            >
                <?= e($sessionLabel) ?>
            </a>
        </div>
    </div>
</header>
