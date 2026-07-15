<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">Trazabilidad</span>
        <h1>Movimientos de activos</h1>
        <p>Consulta registros de altas, asignaciones, devoluciones, cambios de estado, ubicación y reparaciones.</p>
    </div>
    <a class="button button--secondary" href="<?= e(base_url('reportes')) ?>">Volver a reportes</a>
</section>

<?php require BASE_PATH . '/app/Views/reportes/_navigation.php'; ?>

<form class="filters-card" method="GET" action="<?= e(base_url('reportes/movimientos')) ?>">
    <div class="filters-grid report-filters-grid">
        <div class="field">
            <label for="buscar">Buscar</label>
            <input id="buscar" name="buscar" type="search" value="<?= e($filters['search']) ?>" placeholder="Código, producto, usuario o descripción">
        </div>
        <div class="field">
            <label for="tipo">Tipo de movimiento</label>
            <select id="tipo" name="tipo">
                <option value="">Todos</option>
                <?php foreach (['REGISTRO','ACTUALIZACION','ASIGNACION','DEVOLUCION','CAMBIO_ESTADO','CAMBIO_UBICACION','REPARACION','DESCARTE','DONACION'] as $type): ?>
                    <option value="<?= e($type) ?>" <?= $filters['type'] === $type ? 'selected' : '' ?>><?= e(str_replace('_', ' ', $type)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filters-actions report-filter-actions">
            <a class="button button--secondary" href="<?= e(base_url('reportes/movimientos')) ?>">Limpiar</a>
            <button class="button" type="submit">Filtrar</button>
        </div>
    </div>
</form>

<div class="report-toolbar">
    <span><strong><?= e(count($rows)) ?></strong> movimiento(s)</span>
    <a class="button" href="<?= e(base_url('reportes/exportar?' . http_build_query([
        'tipo' => 'movimientos',
        'buscar' => $filters['search'],
        'tipoMovimiento' => $filters['type'],
    ]))) ?>">Exportar a Excel</a>
</div>

<div class="table-card"><div class="table-responsive"><table class="data-table report-table report-table--wide"><thead><tr><th>Fecha</th><th>Tipo</th><th>Activo</th><th>Usuario</th><th>Estado</th><th>Ubicación</th><th>Descripción</th></tr></thead><tbody>
<?php if ($rows === []): ?><tr><td class="table-empty" colspan="7">No se encontraron movimientos.</td></tr><?php endif; ?>
<?php foreach ($rows as $row): ?><tr><td><?= e($row['fechaMovimiento']) ?></td><td><span class="badge badge--normal"><?= e(str_replace('_', ' ', $row['tipoMovimiento'])) ?></span></td><td><strong><?= e($row['codigoActivo']) ?></strong><br><small><?= e($row['nombreProducto']) ?></small></td><td><?= e($row['nombreUsuario']) ?></td><td><?= e(($row['estadoAnterior'] ?? '—') . ' → ' . ($row['estadoNuevo'] ?? '—')) ?></td><td><?= e(($row['ubicacionAnterior'] ?? '—') . ' → ' . ($row['ubicacionNueva'] ?? '—')) ?></td><td><?= e($row['descripcion'] ?? 'Sin descripción') ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
