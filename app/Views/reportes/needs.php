<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">Planificación</span>
        <h1>Presupuesto de necesidades</h1>
        <p>Analiza solicitudes de equipo, software y licencias por año y horizonte presupuestario.</p>
    </div>
    <a class="button button--secondary" href="<?= e(base_url('reportes')) ?>">Volver a reportes</a>
</section>

<?php require BASE_PATH . '/app/Views/reportes/_navigation.php'; ?>

<form class="filters-card" method="GET" action="<?= e(base_url('reportes/necesidades')) ?>">
    <div class="filters-grid report-filters-grid">
        <div class="field">
            <label for="anio">Año</label>
            <select id="anio" name="anio"><option value="">Todos</option><?php foreach ($years as $year): ?><option value="<?= e($year) ?>" <?= (int) $filters['year'] === (int) $year ? 'selected' : '' ?>><?= e($year) ?></option><?php endforeach; ?></select>
        </div>
        <div class="field">
            <label for="periodo">Período</label>
            <select id="periodo" name="periodo"><option value="">Todos</option><?php foreach (['INMEDIATA' => 'Inmediata', 'ANUAL' => 'Anual', 'QUINQUENAL' => 'Quinquenal'] as $value => $label): ?><option value="<?= e($value) ?>" <?= $filters['period'] === $value ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select>
        </div>
        <div class="field">
            <label for="estado">Estado</label>
            <select id="estado" name="estado"><option value="">Todos</option><?php foreach (['En espera', 'En trámite', 'Aprobada', 'Rechazada', 'Atendida', 'Cancelada'] as $status): ?><option value="<?= e($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option><?php endforeach; ?></select>
        </div>
        <div class="filters-actions report-filter-actions"><a class="button button--secondary" href="<?= e(base_url('reportes/necesidades')) ?>">Limpiar</a><button class="button" type="submit">Filtrar</button></div>
    </div>
</form>

<div class="report-summary-strip">
    <span>Solicitudes: <strong><?= e(count($rows)) ?></strong></span>
    <span>Unidades requeridas: <strong><?= e($requestedUnits) ?></strong></span>
    <span>Estimación: <strong>B/ <?= e(number_format((float) $estimatedTotal, 2)) ?></strong></span>
    <a class="button" href="<?= e(base_url('reportes/exportar?' . http_build_query(['tipo' => 'necesidades', 'anio' => $filters['year'], 'periodo' => $filters['period'], 'estado' => $filters['status']]))) ?>">Exportar a Excel</a>
</div>

<div class="table-card"><div class="table-responsive"><table class="data-table report-table report-table--wide"><thead><tr><th>Solicitud</th><th>Colaborador</th><th>Necesidad</th><th>Cantidad</th><th>Prioridad</th><th>Período</th><th>Año</th><th>Estado</th><th>Costo estimado</th></tr></thead><tbody>
<?php if ($rows === []): ?><tr><td class="table-empty" colspan="9">No hay solicitudes para los filtros seleccionados.</td></tr><?php endif; ?>
<?php foreach ($rows as $row): ?><tr><td>#<?= e($row['idSolicitud']) ?><br><small><?= e($row['fechaSolicitud']) ?></small></td><td><?= e($row['nombreColaborador']) ?><br><small><?= e($row['departamento'] ?? 'Sin departamento') ?></small></td><td><strong><?= e($row['titulo']) ?></strong><br><small><?= e($row['nombreProducto'] ?? $row['nombreSubcategoria'] ?? $row['tipoSolicitud']) ?></small></td><td><?= e($row['cantidad']) ?></td><td><?= e($row['prioridad']) ?></td><td><?= e($row['periodoNecesidad']) ?></td><td><?= e($row['anioPresupuestado'] ?? 'No definido') ?></td><td><?= e($row['nombreEstado']) ?></td><td><?= $row['costoEstimado'] !== null ? 'B/ ' . e(number_format((float) $row['costoEstimado'], 2)) : 'Pendiente' ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
