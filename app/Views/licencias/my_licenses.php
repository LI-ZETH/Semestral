<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">Mi software</span>
        <h1>Mis licencias</h1>
        <p>
            Consulta las licencias de software que están actualmente asignadas
            a tu cuenta.
        </p>
    </div>

    <a class="button button--secondary" href="<?= e(base_url('panel')) ?>">
        Volver al panel
    </a>
</section>

<div class="my-license-grid">
    <?php if ($licenses === []): ?>
        <div class="empty-state">
            <h2>No tienes licencias asignadas</h2>
            <p>Actualmente no existen puestos de software vinculados a tu cuenta.</p>
        </div>
    <?php endif; ?>

    <?php foreach ($licenses as $license): ?>
        <?php
        $days = $license['diasRestantes'] !== null
            ? (int) $license['diasRestantes']
            : null;
        ?>
        <article class="my-license-card">
            <span class="badge badge--normal">
                <?= e($license['tipoLicencia']) ?>
            </span>
            <h2><?= e($license['nombreProducto']) ?></h2>
            <p class="my-equipment-card__model">
                <?= e(
                    trim(
                        ($license['marca'] ?? '') . ' '
                        . ($license['modelo'] ?? '')
                    ) ?: 'Software institucional'
                ) ?>
            </p>

            <dl class="equipment-detail-list">
                <div><dt>Proveedor</dt><dd><?= e($license['proveedor'] ?? 'No registrado') ?></dd></div>
                <div><dt>Correo asignado</dt><dd><?= e($license['correoAsignado'] ?? 'No registrado') ?></dd></div>
                <div><dt>Fecha de asignación</dt><dd><?= e($license['fechaAsignacion']) ?></dd></div>
                <div><dt>Expiración</dt><dd><?= e($license['fechaExpiracion'] ?? 'Sin expiración') ?></dd></div>
                <div>
                    <dt>Vigencia</dt>
                    <dd>
                        <?php if ($days === null): ?>
                            Sin límite registrado
                        <?php elseif ($days < 0): ?>
                            Vencida
                        <?php elseif ($days === 0): ?>
                            Vence hoy
                        <?php else: ?>
                            <?= e($days) ?> día(s) restante(s)
                        <?php endif; ?>
                    </dd>
                </div>
            </dl>

            <?php if (!empty($license['urlAcceso'])): ?>
                <a
                    class="button"
                    href="<?= e($license['urlAcceso']) ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Abrir software
                </a>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</div>
