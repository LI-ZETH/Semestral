<?php

declare(strict_types=1);
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
        </nav>
    </div>
</header>