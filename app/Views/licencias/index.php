<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">Software</span>
        <h1>Licencias de software</h1>
        <p>
            Controla puestos, proveedores, fechas de vencimiento,
            renovaciones y asignaciones a colaboradores.
        </p>
    </div>

    <div class="management-header__actions">
        <a class="button button--secondary" href="<?= e(base_url('panel')) ?>">
            Volver al panel
        </a>
        <a class="button" href="<?= e(base_url('licencias/crear')) ?>">
            Registrar licencia
        </a>
    </div>
</section>

<?php if (!empty($success)): ?>
    <div class="alert alert--success"><?= e($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert--error"><?= e($error) ?></div>
<?php endif; ?>

<form class="filters-card" method="GET" action="<?= e(base_url('licencias')) ?>">
    <div class="filters-grid license-filters-grid">
        <div class="field">
            <label for="buscar">Buscar</label>
            <input
                id="buscar"
                name="buscar"
                type="search"
                value="<?= e($filters['search'] ?? '') ?>"
                placeholder="Producto, código, proveedor..."
            >
        </div>

        <div class="field">
            <label for="vencimiento">Vencimiento</label>
            <select id="vencimiento" name="vencimiento">
                <option value="">Todos</option>
                <option value="expired" <?= ($filters['expiration'] ?? '') === 'expired' ? 'selected' : '' ?>>Vencidas</option>
                <option value="30" <?= ($filters['expiration'] ?? '') === '30' ? 'selected' : '' ?>>Próximos 30 días</option>
                <option value="90" <?= ($filters['expiration'] ?? '') === '90' ? 'selected' : '' ?>>Próximos 90 días</option>
                <option value="none" <?= ($filters['expiration'] ?? '') === 'none' ? 'selected' : '' ?>>Sin expiración</option>
            </select>
        </div>

        <div class="filters-actions">
            <button class="button" type="submit">Filtrar</button>
            <a class="button button--secondary" href="<?= e(base_url('licencias')) ?>">
                Limpiar
            </a>
        </div>
    </div>
</form>

<div class="license-grid">
    <?php if ($licenses === []): ?>
        <div class="empty-state">
            <h2>No hay licencias registradas</h2>
            <p>
                Registra una copia de producto tipo Licencia y después
                completa sus datos comerciales.
            </p>
        </div>
    <?php endif; ?>

    <?php foreach ($licenses as $license): ?>
        <?php
        $days = $license['diasRestantes'] !== null
            ? (int) $license['diasRestantes']
            : null;
        $expirationClass = $days === null
            ? 'license-status--neutral'
            : ($days < 0
                ? 'license-status--expired'
                : ($days <= 30
                    ? 'license-status--warning'
                    : 'license-status--active'));
        ?>

        <article class="license-card">
            <div class="license-card__header">
                <div>
                    <span class="license-card__code">
                        <?= e($license['codigoActivo']) ?>
                    </span>
                    <h2><?= e($license['nombreProducto']) ?></h2>
                    <p>
                        <?= e(
                            trim(
                                ($license['marca'] ?? '')
                                . ' '
                                . ($license['modelo'] ?? '')
                            ) ?: 'Sin marca o modelo'
                        ) ?>
                    </p>
                </div>

                <span class="license-status <?= e($expirationClass) ?>">
                    <?php if ($days === null): ?>
                        Sin expiración
                    <?php elseif ($days < 0): ?>
                        Vencida
                    <?php elseif ($days === 0): ?>
                        Vence hoy
                    <?php else: ?>
                        <?= e($days) ?> días
                    <?php endif; ?>
                </span>
            </div>

            <dl class="license-card__details">
                <div>
                    <dt>Proveedor</dt>
                    <dd><?= e($license['proveedor'] ?? 'No registrado') ?></dd>
                </div>
                <div>
                    <dt>Tipo</dt>
                    <dd><?= e($license['tipoLicencia']) ?></dd>
                </div>
                <div>
                    <dt>Puestos</dt>
                    <dd>
                        <?= e($license['puestosAsignados']) ?> asignados /
                        <?= e($license['cantidadPuestos']) ?> totales
                    </dd>
                </div>
                <div>
                    <dt>Disponibles</dt>
                    <dd><?= e($license['puestosDisponibles']) ?></dd>
                </div>
                <div>
                    <dt>Expiración</dt>
                    <dd><?= e($license['fechaExpiracion'] ?? 'No aplica') ?></dd>
                </div>
                <div>
                    <dt>Renovación</dt>
                    <dd>
                        <?= (bool) $license['renovacionAutomatica']
                            ? 'Automática'
                            : 'Manual' ?>
                    </dd>
                </div>
            </dl>

            <div class="license-card__actions">
                <a
                    class="button button--small"
                    href="<?= e(base_url('licencias/ver?id=' . $license['idLicencia'])) ?>"
                >
                    Ver detalle
                </a>
                <a
                    class="button button--small button--secondary"
                    href="<?= e(base_url('licencias/editar?id=' . $license['idLicencia'])) ?>"
                >
                    Editar
                </a>
            </div>
        </article>
    <?php endforeach; ?>
</div>
