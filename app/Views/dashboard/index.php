<section class="dashboard-welcome">
    <?php if (!empty($success)): ?>
        <div class="alert alert--success">
            <?= e($success) ?>
        </div>
    <?php endif; ?>

    <span class="section-heading__eyebrow">
        Panel de <?= e($role ?? 'usuario') ?>
    </span>

    <h1>
        Bienvenido,
        <?= e($user['nombre'] ?? 'usuario') ?>
    </h1>

    <p>
        Has iniciado sesión como

        <strong>
            <?= e($role ?? '') ?>
        </strong>.

        Selecciona uno de los módulos disponibles para
        comenzar a trabajar.
    </p>
</section>

<section class="module-grid">
    <?php foreach ($modules as $module): ?>
        <a
            class="module-card-link"
            href="<?= e($module['url']) ?>"
            aria-label="Abrir <?= e($module['title']) ?>"
        >
            <article class="module-card">
                <span class="module-card__number">
                    <?= e($module['number']) ?>
                </span>

                <h2>
                    <?= e($module['title']) ?>
                </h2>

                <p>
                    <?= e($module['description']) ?>
                </p>

                <span class="module-card__status">
                    <?= e($module['status']) ?>
                    <span aria-hidden="true">→</span>
                </span>
            </article>
        </a>
    <?php endforeach; ?>
</section>