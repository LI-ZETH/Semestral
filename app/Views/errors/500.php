<section class="error-page">
    <span class="error-page__code">500</span>

    <h1>Ocurrió un error interno</h1>

    <p>
        No fue posible completar la operación solicitada.
        El incidente fue registrado para su revisión.
    </p>

    <p>
        Código de referencia:

        <strong>
            <?= e($errorId ?? 'NO-DISPONIBLE') ?>
        </strong>
    </p>

    <a
        class="button"
        href="<?= e(base_url()) ?>"
    >
        Volver al inicio
    </a>
</section>