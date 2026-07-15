<section class="license-revealed-card">
    <span class="section-heading__eyebrow">Clave descifrada</span>
    <h1><?= e($license['nombreProducto']) ?></h1>
    <p>
        Esta información se muestra únicamente en esta página. No la compartas
        por canales inseguros.
    </p>

    <div class="license-revealed-key">
        <code><?= e($licenseKey) ?></code>
    </div>

    <div class="management-header__actions">
        <button
            class="button"
            type="button"
            onclick="navigator.clipboard.writeText(<?= e(json_encode($licenseKey)) ?>)"
        >
            Copiar clave
        </button>
        <a
            class="button button--secondary"
            href="<?= e(base_url('licencias/ver?id=' . $license['idLicencia'])) ?>"
        >
            Regresar al detalle
        </a>
    </div>
</section>
