<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">Integridad criptográfica</span>
        <h1>Bitácora de auditoría</h1>
        <p>Cada operación POST autenticada se encadena mediante SHA-256 y se firma con la llave RSA del sistema.</p>
    </div>
    <a class="button button--secondary" href="<?= e(base_url('reportes')) ?>">Volver a reportes</a>
</section>

<?php require BASE_PATH . '/app/Views/reportes/_navigation.php'; ?>

<div class="audit-integrity-card <?= $integrity['valid'] ? 'audit-integrity-card--valid' : 'audit-integrity-card--invalid' ?>">
    <div>
        <span>Estado de integridad</span>
        <strong><?= $integrity['valid'] ? 'Cadena válida' : 'Cadena comprometida' ?></strong>
        <small><?= e($integrity['verified']) ?> de <?= e($integrity['total']) ?> registro(s) verificados.</small>
    </div>
    <?php if (!$integrity['valid']): ?>
        <p>Primer registro inválido: #<?= e($integrity['firstInvalidId'] ?? 'desconocido') ?></p>
    <?php else: ?>
        <p>Los hashes y las firmas almacenadas coinciden con el contenido de la bitácora.</p>
    <?php endif; ?>
</div>

<form class="filters-card" method="GET" action="<?= e(base_url('reportes/auditoria')) ?>">
    <div class="filters-grid report-filters-grid">
        <div class="field"><label for="buscar">Buscar</label><input id="buscar" name="buscar" type="search" value="<?= e($filters['search']) ?>" placeholder="Usuario, acción, tabla, registro o descripción"></div>
        <div class="field"><label for="modulo">Módulo</label><select id="modulo" name="modulo"><option value="">Todos</option><?php foreach (['USUARIOS','INVENTARIO','ASIGNACIONES','UBICACIONES','SOLICITUDES','REPARACIONES','PERFIL','REPORTES'] as $module): ?><option value="<?= e($module) ?>" <?= $filters['module'] === $module ? 'selected' : '' ?>><?= e($module) ?></option><?php endforeach; ?></select></div>
        <div class="filters-actions report-filter-actions"><a class="button button--secondary" href="<?= e(base_url('reportes/auditoria')) ?>">Limpiar</a><button class="button" type="submit">Filtrar</button></div>
    </div>
</form>

<div class="report-toolbar">
    <span><strong><?= e(count($rows)) ?></strong> evento(s)</span>
    <a class="button" href="<?= e(base_url('reportes/exportar?' . http_build_query(['tipo' => 'auditoria', 'buscar' => $filters['search'], 'modulo' => $filters['module']]))) ?>">Exportar a Excel</a>
</div>

<div class="table-card"><div class="table-responsive"><table class="data-table report-table report-table--audit"><thead><tr><th>ID / fecha</th><th>Usuario</th><th>Módulo</th><th>Acción</th><th>Registro</th><th>IP</th><th>Hash</th><th>Firma</th></tr></thead><tbody>
<?php if ($rows === []): ?><tr><td class="table-empty" colspan="8">La bitácora todavía no contiene eventos.</td></tr><?php endif; ?>
<?php foreach ($rows as $row): ?><tr><td><strong>#<?= e($row['idAuditoria']) ?></strong><br><small><?= e($row['fecha']) ?></small></td><td><?= e($row['nombreUsuario'] ?? 'Sistema') ?><br><small><?= e($row['usuario'] ?? '') ?></small></td><td><?= e($row['modulo']) ?></td><td><span class="badge badge--normal"><?= e($row['accion']) ?></span><br><small><?= e($row['descripcion'] ?? '') ?></small></td><td><?= e($row['tablaAfectada'] ?? '—') ?><br><small><?= e($row['idRegistro'] ?? 'Sin ID') ?></small></td><td><?= e($row['direccionIP'] ?? '—') ?></td><td><code class="report-hash"><?= e($row['hashRegistro'] ?? 'Sin hash') ?></code></td><td><span class="badge <?= !empty($row['firmaDigital']) ? 'badge--active' : 'badge--blocked' ?>"><?= !empty($row['firmaDigital']) ? 'Firmado' : 'Sin firma' ?></span><br><small><?= e($row['algoritmoFirma'] ?? '') ?></small></td></tr><?php endforeach; ?>
</tbody></table></div></div>
