<section class="form-section form-section--wide">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Reparación técnica
            </span>

            <h1><?= e($task['codigoActivo']) ?></h1>

            <p>
                <?= e($task['nombreProducto']) ?>
                <?= e(trim(
                    ($task['marca'] ?? '')
                    . ' '
                    . ($task['modelo'] ?? '')
                )) ?>
            </p>
        </div>

        <div class="request-detail-card repair-detail-card">
            <div>
                <span>Solicitante</span>
                <strong>
                    <?= e($task['colaboradorNombre']) ?>
                    <?= e($task['colaboradorApellido']) ?>
                </strong>
            </div>

            <div>
                <span>Contacto</span>
                <strong>
                    <?= e($task['colaboradorCorreo']) ?>
                    <?= !empty($task['colaboradorTelefono'])
                        ? ' · ' . e($task['colaboradorTelefono'])
                        : '' ?>
                </strong>
            </div>

            <div>
                <span>Ubicación</span>
                <strong>
                    <?= e($task['nombreUbicacion'] ?? 'Sin ubicación') ?>
                    <?= !empty($task['edificio'])
                        ? ' · ' . e($task['edificio'])
                        : '' ?>
                    <?= !empty($task['piso'])
                        ? ' · ' . e($task['piso'])
                        : '' ?>
                    <?= !empty($task['oficina'])
                        ? ' · ' . e($task['oficina'])
                        : '' ?>
                </strong>
            </div>

            <div>
                <span>Prioridad</span>
                <strong><?= e($task['prioridad']) ?></strong>
            </div>

            <div class="request-detail-card__wide">
                <span>Falla reportada</span>
                <p><?= nl2br(e($task['descripcionFalla'])) ?></p>
            </div>

            <?php if (!empty($task['observacionRevision'])): ?>
                <div class="request-detail-card__wide">
                    <span>Indicaciones del administrador</span>
                    <p><?= nl2br(e($task['observacionRevision'])) ?></p>
                </div>
            <?php endif; ?>
        </div>

        <form
            method="POST"
            action="<?= e(base_url('reparaciones/actualizar')) ?>"
        >
            <?= csrf_field() ?>

            <input
                type="hidden"
                name="idSolicitudReparacion"
                value="<?= e($task['idSolicitudReparacion']) ?>"
            >

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert--error">
                    <?= e($errors['general']) ?>
                </div>
            <?php endif; ?>

            <div class="form-grid">
                <div class="field">
                    <label for="idEstadoReparacion">
                        Estado de la reparación
                    </label>

                    <select
                        id="idEstadoReparacion"
                        name="idEstadoReparacion"
                        required
                    >
                        <?php foreach ($states as $state): ?>
                            <option
                                value="<?= e($state['idEstadoReparacion']) ?>"
                                <?= ((int) ($task['idEstadoReparacion'] ?? 0)
                                    === (int) $state['idEstadoReparacion'])
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= e($state['nombreEstado']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (!empty($errors['idEstadoReparacion'])): ?>
                        <small class="field__error">
                            <?= e($errors['idEstadoReparacion']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="costoReparacion">
                        Costo de reparación
                    </label>

                    <input
                        id="costoReparacion"
                        name="costoReparacion"
                        type="number"
                        min="0"
                        step="0.01"
                        value="<?= e($task['costoReparacion'] ?? '0.00') ?>"
                        required
                    >

                    <?php if (!empty($errors['costoReparacion'])): ?>
                        <small class="field__error">
                            <?= e($errors['costoReparacion']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field field--full">
                    <label for="diagnostico">Diagnóstico</label>

                    <textarea
                        id="diagnostico"
                        name="diagnostico"
                        maxlength="4000"
                        placeholder="Describe el resultado de la revisión técnica."
                    ><?= e($task['diagnostico'] ?? '') ?></textarea>

                    <?php if (!empty($errors['diagnostico'])): ?>
                        <small class="field__error">
                            <?= e($errors['diagnostico']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field field--full">
                    <label for="trabajoRealizado">
                        Trabajo realizado
                    </label>

                    <textarea
                        id="trabajoRealizado"
                        name="trabajoRealizado"
                        maxlength="4000"
                        placeholder="Detalla los cambios, reparaciones o pruebas realizadas."
                    ><?= e($task['trabajoRealizado'] ?? '') ?></textarea>

                    <?php if (!empty($errors['trabajoRealizado'])): ?>
                        <small class="field__error">
                            <?= e($errors['trabajoRealizado']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field field--full">
                    <label for="observaciones">Observaciones</label>

                    <textarea
                        id="observaciones"
                        name="observaciones"
                        maxlength="4000"
                    ><?= e($task['observaciones'] ?? '') ?></textarea>

                    <?php if (!empty($errors['observaciones'])): ?>
                        <small class="field__error">
                            <?= e($errors['observaciones']) ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="alert alert--warning repair-state-help">
                <strong>Importante:</strong>
                “En proceso” coloca el activo en reparación.
                “Finalizada” lo devuelve a Asignado si mantiene custodio,
                o a En inventario si ya fue devuelto.
                “No reparable” lo deja en revisión técnica para decisión
                administrativa.
            </div>

            <div class="form-actions">
                <a
                    class="button button--secondary"
                    href="<?= e(base_url('reparaciones')) ?>"
                >
                    Cancelar
                </a>

                <button class="button" type="submit">
                    Guardar seguimiento
                </button>
            </div>
        </form>
    </div>
</section>
