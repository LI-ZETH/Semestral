<section class="hero">
    <div class="hero__content">
        <span class="hero__eyebrow">
            Sistema de gestión tecnológica
        </span>

        <h1>
            Control y trazabilidad de los recursos tecnológicos
        </h1>

        <p>
            Tránsito CMDB centraliza el inventario, las asignaciones,
            las reparaciones, las licencias y el ciclo de vida de los
            activos tecnológicos de la organización.
        </p>

        <div class="connection-status">
            <span class="connection-status__indicator"></span>

            Base de datos conectada correctamente
        </div>
    </div>
</section>

<section class="dashboard-section">
    <div class="section-heading">
        <div>
            <span class="section-heading__eyebrow">
                Resumen inicial
            </span>

            <h2>Estado actual del catálogo</h2>
        </div>
    </div>

    <div class="statistics-grid">
        <article class="statistic-card">
            <span class="statistic-card__label">
                Categorías
            </span>

            <strong class="statistic-card__value">
                <?= e($statistics['categorias']) ?>
            </strong>

            <p>
                Clasificaciones generales del inventario.
            </p>
        </article>

        <article class="statistic-card">
            <span class="statistic-card__label">
                Subcategorías
            </span>

            <strong class="statistic-card__value">
                <?= e($statistics['subcategorias']) ?>
            </strong>

            <p>
                Divisiones específicas para los productos.
            </p>
        </article>

        <article class="statistic-card">
            <span class="statistic-card__label">
                Productos
            </span>

            <strong class="statistic-card__value">
                <?= e($statistics['productos']) ?>
            </strong>

            <p>
                Modelos generales registrados.
            </p>
        </article>

        <article class="statistic-card">
            <span class="statistic-card__label">
                Activos
            </span>

            <strong class="statistic-card__value">
                <?= e($statistics['activos']) ?>
            </strong>

            <p>
                Copias individuales dentro del inventario.
            </p>
        </article>
    </div>
</section>