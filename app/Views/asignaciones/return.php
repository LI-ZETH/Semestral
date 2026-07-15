<section class="form-section assignment-form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Devoluciones
            </span>

            <h1>Registrar devolución</h1>

            <p>
                Recibe la copia, registra su condición y define
                el estado y la ubicación que tendrá después.
            </p>
        </div>

        <div class="assignment-summary">
            <div>
                <span>Copia</span>
                <strong><?= e($assignment['codigoActivo']) ?></strong>
            </div>

            <div>
                <span>Producto</span>
                <strong>
                    <?= e($assignment['nombreProducto']) ?>
                </strong>
            </div>

            <div>
                <span>Colaborador</span>
                <strong>
                    <?= e(
                        $assignment['nombreColaborador']
                        . ' '
                        . $assignment['apellidoColaborador']
                    ) ?>
                </strong>
            </div>

            <div>
                <span>Fecha de entrega</span>
                <strong><?= e($assignment['fechaEntrega']) ?></strong>
            </div>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert--error">
                <?= e($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form
            method="POST"
            action="<?= e(base_url('asignaciones/devolver')) ?>"
        >
            <?= csrf_field() ?>

            <input
                type="hidden"
                name="idAsignacion"
                value="<?= e($assignment['idAsignacion']) ?>"
            >

            <div class="form-grid">
                <div class="field">
                    <label for="idMotivoDevolucion">
                        Motivo de devolución
                    </label>

                    <select
                        id="idMotivoDevolucion"
                        name="idMotivoDevolucion"
                        required
                    >
                        <option value="">Selecciona un motivo</option>

                        <?php foreach ($reasons as $reason): ?>
                            <option
                                value="<?= e(
                                    $reason['idMotivoDevolucion']
                                ) ?>"
                                <?= (
                                    (int) ($old['idMotivoDevolucion'] ?? 0)
                                    === (int) $reason['idMotivoDevolucion']
                                ) ? 'selected' : '' ?>
                            >
                                <?= e($reason['nombreMotivo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (!empty($errors['idMotivoDevolucion'])): ?>
                        <small class="field__error">
                            <?= e($errors['idMotivoDevolucion']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="condicionRecepcion">
                        Condición de recepción
                    </label>

                    <select
                        id="condicionRecepcion"
                        name="condicionRecepcion"
                        required
                    >
                        <?php foreach ($conditions as $condition): ?>
                            <option
                                value="<?= e($condition) ?>"
                                <?= (
                                    ($old['condicionRecepcion'] ?? '')
                                    === $condition
                                ) ? 'selected' : '' ?>
                            >
                                <?= e(match ($condition) {
                                    'BUENO' => 'Bueno',
                                    'DANADO' => 'Dañado',
                                    'INCOMPLETO' => 'Incompleto',
                                    default => 'No verificado',
                                }) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (!empty($errors['condicionRecepcion'])): ?>
                        <small class="field__error">
                            <?= e($errors['condicionRecepcion']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="idEstadoActivo">
                        Estado posterior
                    </label>

                    <select
                        id="idEstadoActivo"
                        name="idEstadoActivo"
                        required
                    >
                        <option value="">Selecciona un estado</option>

                        <?php foreach ($states as $state): ?>
                            <option
                                value="<?= e($state['idEstadoActivo']) ?>"
                                <?= (
                                    (int) ($old['idEstadoActivo'] ?? 0)
                                    === (int) $state['idEstadoActivo']
                                ) ? 'selected' : '' ?>
                            >
                                <?= e($state['nombreEstado']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (!empty($errors['idEstadoActivo'])): ?>
                        <small class="field__error">
                            <?= e($errors['idEstadoActivo']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="idUbicacion">
                        Ubicación de recepción
                    </label>

                    <select
                        id="idUbicacion"
                        name="idUbicacion"
                        required
                    >
                        <option value="">Selecciona una ubicación</option>

                        <?php foreach ($locations as $location): ?>
                            <option
                                value="<?= e($location['idUbicacion']) ?>"
                                <?= (
                                    (int) ($old['idUbicacion'] ?? 0)
                                    === (int) $location['idUbicacion']
                                ) ? 'selected' : '' ?>
                            >
                                <?= e($location['nombreUbicacion']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (!empty($errors['idUbicacion'])): ?>
                        <small class="field__error">
                            <?= e($errors['idUbicacion']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field field--full">
                    <label for="observaciones">
                        Observaciones de recepción
                    </label>

                    <textarea
                        id="observaciones"
                        name="observaciones"
                        maxlength="2000"
                        placeholder="Daños, accesorios faltantes, estado físico o acciones recomendadas."
                    ><?= e($old['observaciones'] ?? '') ?></textarea>

                    <?php if (!empty($errors['observaciones'])): ?>
                        <small class="field__error">
                            <?= e($errors['observaciones']) ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-actions">
                <a
                    class="button button--secondary"
                    href="<?= e(base_url('asignaciones')) ?>"
                >
                    Cancelar
                </a>

                <button class="button" type="submit">
                    Registrar devolución
                </button>
            </div>
        </form>
    </div>
</section>
