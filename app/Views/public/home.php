<?php
use App\Core\Auth;

$primaryUrl = Auth::check()
    ? base_url('panel')
    : base_url('login');

$primaryLabel = Auth::check()
    ? 'Abrir mi panel'
    : 'Iniciar TrackiT';
?>
<section class="hero-section">
    <div class="hero-content">
        <span class="public-eyebrow">
            Sistema CMDB de gestión tecnológica
        </span>

        <h1>
            Gestiona, asigna y supervisa tus equipos con TrackiT
        </h1>

        <p>
            Centraliza el inventario de hardware, software y
            licencias; controla asignaciones, reparaciones,
            ubicaciones, depreciación y auditoría desde una sola
            plataforma.
        </p>

        <div class="hero-actions">
            <a
                href="<?= e($primaryUrl) ?>"
                class="download-button primary-button"
            >
                <?= e($primaryLabel) ?>
            </a>

            <a
                href="<?= e(base_url('funcionalidades')) ?>"
                class="secondary-public-button"
            >
                Ver funcionalidades
            </a>
        </div>

        <div class="compare-plans-link">
            <span aria-hidden="true">›</span>
            <a href="<?= e(base_url('ayuda')) ?>">
                Consulta el manual de uso de la aplicación
            </a>
        </div>
    </div>
</section>

<section class="public-statistics-section">
    <div class="public-section-heading">
        <span class="public-eyebrow">Sistema conectado</span>
        <h2>Un inventario que refleja la operación real</h2>
        <p>
            Estos datos se obtienen directamente de la base de datos
            actual de TrackiT.
        </p>
    </div>

    <div class="public-statistics-grid">
        <article class="public-statistic-card">
            <strong><?= e($statistics['categorias']) ?></strong>
            <span>Categorías activas</span>
        </article>

        <article class="public-statistic-card">
            <strong><?= e($statistics['subcategorias']) ?></strong>
            <span>Subcategorías</span>
        </article>

        <article class="public-statistic-card">
            <strong><?= e($statistics['productos']) ?></strong>
            <span>Productos generales</span>
        </article>

        <article class="public-statistic-card">
            <strong><?= e($statistics['activos']) ?></strong>
            <span>Copias individuales</span>
        </article>
    </div>
</section>

<section class="public-highlight-section">
    <div class="public-section-heading">
        <span class="public-eyebrow">TrackiT System</span>
        <h2>Control durante todo el ciclo de vida</h2>
    </div>

    <div class="public-highlight-grid">
        <article>
            <span>01</span>
            <h3>Inventario estructurado</h3>
            <p>
                Organiza categorías, subcategorías, productos y
                copias individuales con imágenes y códigos únicos.
            </p>
        </article>

        <article>
            <span>02</span>
            <h3>Asignación y soporte</h3>
            <p>
                Registra custodios, ubicaciones, devoluciones,
                solicitudes y reparaciones técnicas.
            </p>
        </article>

        <article>
            <span>03</span>
            <h3>Trazabilidad y seguridad</h3>
            <p>
                Consulta reportes, movimientos, auditoría firmada,
                accesos, QR y controles basados en roles.
            </p>
        </article>
    </div>
</section>
