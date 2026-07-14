<section class="error-page">
    <span class="error-page__code">404</span>

    <h1>Página no encontrada</h1>

    <p>
        La dirección
        <code><?= e($path ?? '/') ?></code>
        no corresponde a una sección registrada.
    </p>

    <a
        class="button"
        href="<?= e(base_url()) ?>"
    >
        Volver al inicio
    </a>
</section>