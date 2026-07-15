<?php

declare(strict_types=1);
?>
<footer class="public-footer">
    <div class="public-footer__container">
        <div>
            <img
                class="public-footer__logo"
                src="<?= e(asset_url('assets/img/public/Logo-app.png')) ?>"
                alt="TrackiT"
            >

            <p>
                Gestión, seguridad y trazabilidad para los
                recursos tecnológicos de la organización.
            </p>
        </div>

        <nav aria-label="Enlaces del pie de página">
            <a href="<?= e(base_url()) ?>">Inicio</a>
            <a href="<?= e(base_url('funcionalidades')) ?>">Funcionalidades</a>
            <a href="<?= e(base_url('noticias')) ?>">Noticias</a>
            <a href="<?= e(base_url('nosotros')) ?>">Nosotros</a>
            <a href="<?= e(base_url('ayuda')) ?>">Manual</a>
        </nav>
    </div>

    <p class="public-footer__copyright">
        &copy; <?= date('Y') ?> <?= e(APP_NAME) ?>.
        Todos los derechos reservados.
    </p>
</footer>
