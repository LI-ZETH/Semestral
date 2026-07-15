<section class="form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Soporte técnico
            </span>

            <h1>Asignar reparación</h1>

            <p>
                Selecciona el técnico responsable del diagnóstico
                y seguimiento del equipo.
            </p>
        </div>

        <div class="request-detail-card">
            <div>
                <span>Solicitante</span>
                <strong>
                    <?= e($request['nombre']) ?>
                    <?= e($request['apellido']) ?>
                </strong>
            </div>

            <div>
                <span>Equipo</span>
                <strong>
                    <?= e($request['codigoActivo']) ?>
                    ·
                    <?= e($request['nombreProducto']) ?>
                </strong>
            </div>

            <div>
                <span>Ubicación</span>
                <strong>
                    <?= e($request['nombreUbicacion'] ?? 'Sin ubicación') ?>
                </strong>
            </div>

            <div>
                <span>Prioridad</span>
                <strong><?= e($request['prioridad']) ?></strong>
            </div>

            <div class="request-detail-card__wide">
                <span>Falla reportada</span>
                <p><?= nl2br(e($request['descripcionFalla'])) ?></p>
            </div>
        </div>

        <form
            method="POST"
            action="<?= e(base_url('solicitudes/reparacion/asignar')) ?>"
        >
            <?= csrf_field() ?>

            <input
                type="hidden"
                name="idSolicitudReparacion"
                value="<?= e($request['idSolicitudReparacion']) ?>"
            >

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert--error">
                    <?= e($errors['general']) ?>
                </div>
            <?php endif; ?>

            <div class="form-grid">
                <div class="field field--full">
                    <label for="idTecnico">Técnico responsable</label>

                    <select id="idTecnico" name="idTecnico" required>
                        <option value="">Selecciona un técnico</option>

                        <?php foreach ($technicians as $technician): ?>
                            <option
                                value="<?= e($technician['idUsuario']) ?>"
                                <?= ((int) ($old['idTecnico'] ?? 0)
                                    === (int) $technician['idUsuario'])
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= e($technician['nombre']) ?>
                                <?= e($technician['apellido']) ?>
                                —
                                <?= e($technician['correo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (!empty($errors['idTecnico'])): ?>
                        <small class="field__error">
                            <?= e($errors['idTecnico']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field field--full">
                    <label for="observacionRevision">
                        Indicaciones para el técnico
                    </label>

                    <textarea
                        id="observacionRevision"
                        name="observacionRevision"
                        maxlength="2000"
                    ><?= e($old['observacionRevision'] ?? '') ?></textarea>

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
                    Asignar reparación
                </button>
            </div>
        </form>
    </div>
</section>
