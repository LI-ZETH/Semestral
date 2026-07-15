<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">Reportes</span>
        <h1>Inventario dinámico</h1>
        <p>Consulta activos por categoría, estado, código, producto, ubicación o custodio.</p>
    </div>

    <a class="button button--secondary" href="<?= e(base_url('reportes')) ?>">
        Volver a reportes
    </a>
</section>

<?php require BASE_PATH . '/app/Views/reportes/_navigation.php'; ?>

<form class="filters-card" method="GET" action="<?= e(base_url('reportes/inventario')) ?>">
    <div class="filters-grid report-filters-grid">
        <div class="field">
            <label for="buscar">Buscar</label>
            <input id="buscar" name="buscar" type="search"
                value="<?= e($filters['search']) ?>"
                placeholder="Código, serie, producto, ubicación o custodio">
        </div>

        <div class="field">
            <label for="categoria">Categoría</label>
            <select id="categoria" name="categoria">
                <option value="">Todas</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= e($category['idCategoria']) ?>"
                        <?= (int) $filters['category'] === (int) $category['idCategoria'] ? 'selected' : '' ?>>
                        <?= e($category['nombreCategoria']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label for="estado">Estado</label>
            <select id="estado" name="estado">
                <option value="">Todos</option>
                <?php foreach ($states as $state): ?>
                    <option value="<?= e($state['idEstadoActivo']) ?>"
                        <?= (int) $filters['state'] === (int) $state['idEstadoActivo'] ? 'selected' : '' ?>>
                        <?= e($state['nombreEstado']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filters-actions report-filter-actions">
            <a class="button button--secondary" href="<?= e(base_url('reportes/inventario')) ?>">Limpiar</a>
            <button class="button" type="submit">Filtrar</button>
        </div>
    </div>
</form>

<div class="report-toolbar">
    <span><strong><?= e(count($rows)) ?></strong> registro(s) encontrados</span>
    <a class="button" href="<?= e(base_url('reportes/exportar?' . http_build_query([
        'tipo' => 'inventario',
        'buscar' => $filters['search'],
        'categoria' => $filters['category'],
        'estado' => $filters['state'],
    ]))) ?>">Exportar a Excel</a>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="data-table report-table report-table--wide">
            <thead>
                <tr>
                    <th>Código</th><th>Producto</th><th>Categoría</th><th>Estado</th>
                    <th>Ubicación</th><th>Custodio</th><th>Costo</th><th>Adquisición</th><th>Fin vida útil</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rows === []): ?>
                    <tr><td class="table-empty" colspan="9">No se encontraron activos.</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><strong><?= e($row['codigoActivo']) ?></strong><br><small><?= e($row['numeroSerie'] ?? 'Sin serie') ?></small></td>
                        <td><?= e($row['nombreProducto']) ?><br><small><?= e(trim(($row['marca'] ?? '') . ' ' . ($row['modelo'] ?? ''))) ?></small></td>
                        <td><?= e($row['nombreCategoria']) ?><br><small><?= e($row['nombreSubcategoria']) ?></small></td>
                        <td><span class="badge badge--normal"><?= e($row['nombreEstado']) ?></span></td>
                        <td><?= e($row['nombreUbicacion'] ?? 'Sin ubicación') ?></td>
                        <td><?= e($row['nombreColaborador'] ?? 'Sin asignar') ?></td>
                        <td>B/ <?= e(number_format((float) $row['costo'], 2)) ?></td>
                        <td><?= e($row['fechaAdquisicion']) ?></td>
                        <td><?= e($row['fechaFinVidaUtil'] ?? 'No definida') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
