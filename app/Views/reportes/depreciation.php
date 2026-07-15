<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">Ciclo de vida</span>
        <h1>Próximos a depreciación</h1>
        <p>Incluye equipos vencidos y los que alcanzarán el fin de su vida útil dentro del período elegido.</p>
    </div>
    <a class="button button--secondary" href="<?= e(base_url('reportes')) ?>">Volver a reportes</a>
</section>

<?php require BASE_PATH . '/app/Views/reportes/_navigation.php'; ?>

<form class="filters-card" method="GET" action="<?= e(base_url('reportes/depreciacion')) ?>">
    <div class="filters-grid depreciation-filter-grid">
        <div class="field">
            <label for="dias">Período máximo</label>
            <select id="dias" name="dias">
                <?php foreach ([0 => 'Solo vencidos', 30 => 'Próximos 30 días', 90 => 'Próximos 90 días', 180 => 'Próximos 180 días', 365 => 'Próximo año'] as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $days === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filters-actions">
            <button class="button" type="submit">Consultar</button>
        </div>
    </div>
</form>

<div class="report-toolbar">
    <span><strong><?= e(count($rows)) ?></strong> activo(s) encontrados</span>
    <a class="button" href="<?= e(base_url('reportes/exportar?' . http_build_query(['tipo' => 'depreciacion', 'dias' => $days]))) ?>">Exportar a Excel</a>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="data-table report-table">
            <thead>
                <tr><th>Código</th><th>Equipo</th><th>Categoría</th><th>Estado</th><th>Adquisición</th><th>Fin vida útil</th><th>Días restantes</th><th>Costo</th></tr>
            </thead>
            <tbody>
                <?php if ($rows === []): ?><tr><td class="table-empty" colspan="8">No hay activos en el período seleccionado.</td></tr><?php endif; ?>
                <?php foreach ($rows as $row): ?>
                    <?php $expired = (int) $row['diasRestantes'] < 0; ?>
                    <tr>
                        <td><strong><?= e($row['codigoActivo']) ?></strong></td>
                        <td><?= e($row['nombreProducto']) ?><br><small><?= e(trim(($row['marca'] ?? '') . ' ' . ($row['modelo'] ?? ''))) ?></small></td>
                        <td><?= e($row['nombreCategoria']) ?></td>
                        <td><?= e($row['nombreEstado']) ?></td>
                        <td><?= e($row['fechaAdquisicion']) ?></td>
                        <td><?= e($row['fechaFinVidaUtil']) ?></td>
                        <td><span class="badge <?= $expired ? 'badge--blocked' : 'badge--warning' ?>"><?= $expired ? 'Vencido hace ' . e(abs((int) $row['diasRestantes'])) . ' días' : e($row['diasRestantes']) . ' días' ?></span></td>
                        <td>B/ <?= e(number_format((float) $row['costo'], 2)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
