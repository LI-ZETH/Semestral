<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">Seguridad</span>
        <h1>Historial de accesos</h1>
        <p>Revisa intentos exitosos y fallidos, cuentas utilizadas, direcciones IP y mensajes del control de acceso.</p>
    </div>
    <a class="button button--secondary" href="<?= e(base_url('reportes')) ?>">Volver a reportes</a>
</section>

<?php require BASE_PATH . '/app/Views/reportes/_navigation.php'; ?>

<form class="filters-card" method="GET" action="<?= e(base_url('reportes/accesos')) ?>">
    <div class="filters-grid report-filters-grid">
        <div class="field"><label for="buscar">Buscar</label><input id="buscar" name="buscar" type="search" value="<?= e($filters['search']) ?>" placeholder="Usuario, identificador o IP"></div>
        <div class="field"><label for="resultado">Resultado</label><select id="resultado" name="resultado"><option value="">Todos</option><option value="1" <?= $filters['result'] === '1' ? 'selected' : '' ?>>Exitosos</option><option value="0" <?= $filters['result'] === '0' ? 'selected' : '' ?>>Fallidos</option></select></div>
        <div class="filters-actions report-filter-actions"><a class="button button--secondary" href="<?= e(base_url('reportes/accesos')) ?>">Limpiar</a><button class="button" type="submit">Filtrar</button></div>
    </div>
</form>

<div class="report-toolbar">
    <span><strong><?= e(count($rows)) ?></strong> intento(s)</span>
    <a class="button" href="<?= e(base_url('reportes/exportar?' . http_build_query(['tipo' => 'accesos', 'buscar' => $filters['search'], 'resultado' => $filters['result']]))) ?>">Exportar a Excel</a>
</div>

<div class="table-card"><div class="table-responsive"><table class="data-table report-table report-table--wide"><thead><tr><th>Fecha</th><th>Identificador</th><th>Usuario</th><th>IP</th><th>Resultado</th><th>Descripción</th><th>Navegador</th></tr></thead><tbody>
<?php if ($rows === []): ?><tr><td class="table-empty" colspan="7">No se encontraron intentos de acceso.</td></tr><?php endif; ?>
<?php foreach ($rows as $row): ?><tr><td><?= e($row['fechaIntento']) ?></td><td><strong><?= e($row['usuarioIngresado']) ?></strong></td><td><?= e($row['nombreUsuario'] ?? 'No identificado') ?></td><td><?= e($row['direccionIP']) ?></td><td><span class="badge <?= (bool) $row['exito'] ? 'badge--active' : 'badge--blocked' ?>"><?= (bool) $row['exito'] ? 'Exitoso' : 'Fallido' ?></span></td><td><?= e($row['descripcion'] ?? 'Sin descripción') ?></td><td><span class="report-user-agent"><?= e($row['userAgent'] ?? 'No registrado') ?></span></td></tr><?php endforeach; ?>
</tbody></table></div></div>
