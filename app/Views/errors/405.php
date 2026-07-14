<section class="error-page">
    <span class="error-page__code">405</span>

    <h1>Método no permitido</h1>

    <p>
        La ruta
        <code><?= e($path ?? '/') ?></code>
        no acepta el método HTTP utilizado.
    </p>

    <a
        class="button"
        href="<?= e(base_url()) ?>"
    >
        Volver al inicio
    </a>
</section>