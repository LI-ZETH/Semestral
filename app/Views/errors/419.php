<section class="error-page">
    <span class="error-page__code">419</span>

    <h1>Formulario no válido o expirado</h1>

    <p>
        La solicitud no contiene un token de seguridad válido.
        Esto puede ocurrir cuando la sesión ha expirado o la página
        permaneció abierta durante demasiado tiempo.
    </p>

    <p>
        Regresa a la página anterior, actualízala e intenta nuevamente.
    </p>

    <a
        class="button"
        href="<?= e(base_url()) ?>"
    >
        Volver al inicio
    </a>
</section>