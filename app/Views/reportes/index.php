<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">Administración</span>
        <h1>Reportes y estadísticas</h1>
        <p>
            Consulta el estado del inventario, las necesidades,
            la trazabilidad y los eventos de seguridad del sistema.
        </p>
    </div>

    <a class="button button--secondary" href="<?= e(base_url('panel')) ?>">
        Volver al panel
    </a>
</section>

<?php require BASE_PATH . '/app/Views/reportes/_navigation.php'; ?>

<section class="report-kpi-grid">
    <?php
    $kpis = [
        ['Activos', $summary['totalActivos'] ?? 0, 'Copias activas registradas'],
        ['Disponibles', $summary['disponibles'] ?? 0, 'Listas para asignación'],
        ['Asignados', $summary['asignados'] ?? 0, 'Bajo custodia'],
        ['Servicio técnico', $summary['servicioTecnico'] ?? 0, 'En revisión o reparación'],
        ['Bajas', $summary['bajas'] ?? 0, 'Descarte o donación'],
        ['Usuarios', $summary['usuariosActivos'] ?? 0, 'Cuentas activas'],
        ['Colaboradores', $summary['colaboradoresActivos'] ?? 0, 'Perfiles activos'],
        ['Solicitudes pendientes', $summary['solicitudesPendientes'] ?? 0, 'En espera o trámite'],
        ['Reparaciones abiertas', $summary['reparacionesAbiertas'] ?? 0, 'Pendientes o en proceso'],
    ];
    ?>

    <?php foreach ($kpis as [$label, $value, $description]): ?>
        <article class="report-kpi-card">
            <span><?= e($label) ?></span>
            <strong><?= e($value) ?></strong>
            <small><?= e($description) ?></small>
        </article>
    <?php endforeach; ?>

    <article class="report-kpi-card report-kpi-card--money">
        <span>Valor del inventario</span>
        <strong>
            B/ <?= e(number_format((float) ($summary['valorInventario'] ?? 0), 2)) ?>
        </strong>
        <small>Suma del costo de los activos activos</small>
    </article>
</section>

<section class="report-shortcut-grid">
    <a class="report-shortcut-card" href="<?= e(base_url('reportes/inventario')) ?>">
        <strong>Inventario dinámico</strong>
        <span>Filtra por categoría, estado o búsqueda y exporta a Excel.</span>
    </a>

    <a class="report-shortcut-card" href="<?= e(base_url('reportes/depreciacion')) ?>">
        <strong>Equipos próximos a depreciación</strong>
        <span>Localiza activos vencidos o cercanos al fin de su vida útil.</span>
    </a>

    <a class="report-shortcut-card" href="<?= e(base_url('reportes/necesidades')) ?>">
        <strong>Presupuesto de necesidades</strong>
        <span>Resume solicitudes inmediatas, anuales y quinquenales.</span>
    </a>

    <a class="report-shortcut-card" href="<?= e(base_url('reportes/auditoria')) ?>">
        <strong>Bitácora firmada</strong>
        <span>Verifica la cadena de hashes y las firmas RSA.</span>
    </a>
</section>

<section class="dashboard-section">
    <div class="section-heading">
        <span class="section-heading__eyebrow">Resumen por categoría</span>
        <h2>Distribución del inventario</h2>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="data-table report-table">
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th>Total</th>
                        <th>Disponibles</th>
                        <th>Asignados</th>
                        <th>Revisión</th>
                        <th>Reparación</th>
                        <th>Descarte</th>
                        <th>Donados</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><strong><?= e($category['nombreCategoria']) ?></strong></td>
                            <td><?= e($category['totalActivos']) ?></td>
                            <td><?= e($category['enInventario']) ?></td>
                            <td><?= e($category['asignados']) ?></td>
                            <td><?= e($category['enRevision']) ?></td>
                            <td><?= e($category['enReparacion']) ?></td>
                            <td><?= e($category['enDescarte']) ?></td>
                            <td><?= e($category['donados']) ?></td>
                            <td>B/ <?= e(number_format((float) $category['valorCategoria'], 2)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
