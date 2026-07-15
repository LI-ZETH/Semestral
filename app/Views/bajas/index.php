<section class="management-header">
    <div>
        <span class="section-heading__eyebrow">
            Ciclo de vida del inventario
        </span>

        <h1>Bajas de activos</h1>

        <p>
            Consulta los descartes y donaciones registrados sin
            eliminar la trazabilidad histórica de las copias.
        </p>
    </div>

    <div class="management-header__actions">
        <a
            class="button button--secondary"
            href="<?= e(base_url('panel')) ?>"
        >
            Volver al panel
        </a>

        <a
            class="button"
            href="<?= e(base_url('bajas/crear')) ?>"
        >
            Registrar baja
        </a>
    </div>
</section>

<?php if (!empty($success)): ?>
    <div class="alert alert--success">
        <?= e($success) ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert--error">
        <?= e($error) ?>
    </div>
<?php endif; ?>

<form
    class="filters-card"
    method="GET"
    action="<?= e(base_url('bajas')) ?>"
>
    <div class="filters-grid disposal-filters-grid">
        <div class="field">
            <label for="buscar">Buscar</label>

            <input
                id="buscar"
                name="buscar"
                type="search"
                value="<?= e($filters['search'] ?? '') ?>"
                placeholder="Código, producto, entidad o documento"
            >
        </div>

        <div class="field">
            <label for="tipo">Tipo de baja</label>

            <select id="tipo" name="tipo">
                <option value="">Todos los tipos</option>

                <?php foreach ($types as $type): ?>
                    <option
                        value="<?= e($type['idTipoBaja']) ?>"
                        <?= (
                            (int) ($filters['type'] ?? 0)
                            === (int) $type['idTipoBaja']
                        ) ? 'selected' : '' ?>
                    >
                        <?= e($type['nombreTipo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filters-actions">
            <a
                class="button button--secondary"
                href="<?= e(base_url('bajas')) ?>"
            >
                Limpiar
            </a>

            <button class="button" type="submit">
                Filtrar
            </button>
        </div>
    </div>
</form>

<div class="table-card">
    <div class="table-responsive">
        <table class="data-table disposal-table">
            <thead>
                <tr>
                    <th>Activo</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Beneficiario</th>
                    <th>Registrado por</th>
                    <th>Documento</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($disposals === []): ?>
                    <tr>
                        <td class="table-empty" colspan="7">
                            No existen bajas registradas con estos filtros.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($disposals as $disposal): ?>
                    <tr>
                        <td>
                            <div class="disposal-asset-cell">
                                <?php if (!empty($disposal['imagenPrincipal'])): ?>
                                    <img
                                        src="<?= e(
                                            asset_url(
                                                $disposal['imagenPrincipal']
                                            )
                                        ) ?>"
                                        alt=""
                                    >
                                <?php else: ?>
                                    <span class="disposal-asset-placeholder">
                                        <?= e(
                                            mb_substr(
                                                $disposal['codigoActivo'],
                                                0,
                                                1
                                            )
                                        ) ?>
                                    </span>
                                <?php endif; ?>

                                <div>
                                    <strong>
                                        <?= e($disposal['codigoActivo']) ?>
                                    </strong>

                                    <span>
                                        <?= e($disposal['nombreProducto']) ?>
                                    </span>

                                    <small>
                                        <?= e(
                                            trim(
                                                ($disposal['marca'] ?? '')
                                                . ' '
                                                . ($disposal['modelo'] ?? '')
                                            ) ?: 'Sin marca o modelo'
                                        ) ?>
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge <?= $disposal['codigoTipo'] === 'DONACION'
                                ? 'badge--normal'
                                : 'badge--inactive' ?>">
                                <?= e($disposal['nombreTipo']) ?>
                            </span>
                        </td>

                        <td>
                            <?= e(
                                date(
                                    'd/m/Y',
                                    strtotime($disposal['fechaBaja'])
                                )
                            ) ?>
                        </td>

                        <td>
                            <?= e(
                                $disposal['entidadBeneficiaria']
                                ?? 'No aplica'
                            ) ?>
                        </td>

                        <td>
                            <?= e($disposal['registradoPor']) ?>
                        </td>

                        <td>
                            <?= e(
                                $disposal['documentoReferencia']
                                ?? 'Sin referencia'
                            ) ?>
                        </td>

                        <td>
                            <div class="table-actions">
                                <a
                                    class="button button--small"
                                    href="<?= e(
                                        base_url(
                                            'bajas/ver?id='
                                            . $disposal['idBaja']
                                        )
                                    ) ?>"
                                >
                                    Ver detalle
                                </a>

                                <a
                                    class="button button--small button--secondary"
                                    href="<?= e(
                                        base_url(
                                            'inventario/activos/ver?id='
                                            . $disposal['idActivo']
                                        )
                                    ) ?>"
                                >
                                    Ficha
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
