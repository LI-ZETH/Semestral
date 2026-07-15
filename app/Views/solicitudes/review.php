<section class="form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Administración de solicitudes
            </span>

            <h1>Revisar solicitud</h1>

            <p>
                <?= e($request['nombre']) ?>
                <?= e($request['apellido']) ?>
                ·
                <?= e($request['correo']) ?>
            </p>
        </div>

        <div class="request-detail-card">
            <div>
                <span>Título</span>
                <strong><?= e($request['titulo']) ?></strong>
            </div>

            <div>
                <span>Tipo y prioridad</span>
                <strong>
                    <?= e($request['tipoSolicitud']) ?>
                    ·
                    <?= e($request['prioridad']) ?>
                </strong>
            </div>

            <div>
                <span>Producto solicitado</span>
                <strong>
                    <?= e(
                        $request['nombreProducto']
                        ?? $request['nombreSubcategoria']
                        ?? 'Sin producto específico'
                    ) ?>
                </strong>
            </div>

            <div>
                <span>Cantidad</span>
                <strong><?= e($request['cantidad']) ?></strong>
            </div>

            <div class="request-detail-card__wide">
                <span>Descripción</span>
                <p><?= nl2br(e($request['descripcionNecesidad'])) ?></p>
            </div>

            <div class="request-detail-card__wide">
                <span>Justificación</span>
                <p><?= nl2br(e($request['justificacion'])) ?></p>
            </div>
        </div>

        <form
            method="POST"
            action="<?= e(base_url('solicitudes/revisar')) ?>"
        >
            <?= csrf_field() ?>

            <input
                type="hidden"
                name="idSolicitud"
                value="<?= e($request['idSolicitud']) ?>"
            >

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert--error">
                    <?= e($errors['general']) ?>
                </div>
            <?php endif; ?>

            <div class="form-grid">
                <div class="field">
                    <label for="idEstadoSolicitud">Estado</label>

                    <select
                        id="idEstadoSolicitud"
                        name="idEstadoSolicitud"
                        required
                    >
                        <option value="">Selecciona un estado</option>

                        <?php foreach ($states as $state): ?>
                            <option
                                value="<?= e($state['idEstadoSolicitud']) ?>"
                                <?= ((int) ($request['idEstadoSolicitud'] ?? 0)
                                    === (int) $state['idEstadoSolicitud'])
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= e($state['nombreEstado']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (!empty($errors['idEstadoSolicitud'])): ?>
                        <small class="field__error">
                            <?= e($errors['idEstadoSolicitud']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="costoEstimado">
                        Costo estimado
                    </label>

                    <input
                        id="costoEstimado"
                        name="costoEstimado"
                        type="number"
                        min="0"
                        step="0.01"
                        value="<?= e($request['costoEstimado'] ?? '') ?>"
                        placeholder="Opcional"
                    >

                    <?php if (!empty($errors['costoEstimado'])): ?>
                        <small class="field__error">
                            <?= e($errors['costoEstimado']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field field--full">
                    <label for="observacionRevision">
                        Observación de revisión
                    </label>

                    <textarea
                        id="observacionRevision"
                        name="observacionRevision"
                        maxlength="2000"
                    ><?= e($request['observacionRevision'] ?? '') ?></textarea>

                    <?php if (!empty($errors['observacionRevision'])): ?>
                        <small class="field__error">
                            <?= e($errors['observacionRevision']) ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-actions">
                <a
                    class="button button--secondary"
                    href="<?= e(base_url('solicitudes/administrar')) ?>"
                >
                    Cancelar
                </a>

                <button class="button" type="submit">
                    Guardar revisión
                </button>
            </div>
        </form>
    </div>
</section>
