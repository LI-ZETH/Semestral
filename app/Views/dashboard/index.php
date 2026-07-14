<section class="dashboard-welcome">
    <?php if (!empty($success)): ?>
        <div class="alert alert--success">
            <?= e($success) ?>
        </div>
    <?php endif; ?>

    <span class="section-heading__eyebrow">
        Panel principal
    </span>

    <h1>
        Bienvenido,
        <?= e($user['nombre'] ?? 'usuario') ?>
    </h1>

    <p>
        Has iniciado sesión como
        <strong>
            <?= e($user['nombreRol'] ?? '') ?>
        </strong>.
        Desde este panel se administrarán los módulos de
        Tránsito CMDB.
    </p>
</section>

<section class="module-grid">
    <article class="module-card">
        <span class="module-card__number">01</span>

        <h2>Inventario</h2>

        <p>
            Categorías, productos, copias individuales,
            imágenes, estados y depreciación.
        </p>

        <span class="module-card__status">
            Próximamente
        </span>
    </article>

    <article class="module-card">
        <span class="module-card__number">02</span>

        <h2>Asignaciones</h2>

        <p>
            Entrega, devolución y trazabilidad de activos
            tecnológicos.
        </p>

        <span class="module-card__status">
            Próximamente
        </span>
    </article>

    <article class="module-card">
        <span class="module-card__number">03</span>

        <h2>Usuarios</h2>

        <p>
            Administración, roles, bloqueos y estado de
            las cuentas.
        </p>

        <span class="module-card__status">
            Próximamente
        </span>
    </article>
</section>