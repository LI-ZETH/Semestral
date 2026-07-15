<section class="form-section assignment-form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Asignaciones
            </span>

            <h1>Asignar una copia</h1>

            <p>
                Selecciona una copia disponible, el colaborador
                responsable y la ubicación donde quedará el equipo.
            </p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert--error">
                <?= e($errors['general']) ?>
            </div>
        <?php endif; ?>

        <?php if ($assets === []): ?>
            <div class="alert alert--warning">
                No existen copias disponibles para asignar. Debes tener
                al menos una copia activa en estado En inventario.
            </div>
        <?php endif; ?>

        <form
            method="POST"
            action="<?= e(base_url('asignaciones/guardar')) ?>"
        >
            <?= csrf_field() ?>

            <div class="form-grid">
                <div class="field field--full">
                    <label for="idActivo">Copia disponible</label>

                    <select
                        id="idActivo"
                        name="idActivo"
                        required
                    >
                        <option value="">
                            Selecciona una copia
                        </option>

                        <?php foreach ($assets as $asset): ?>
                            <option
                                value="<?= e($asset['idActivo']) ?>"
                                <?= (
                                    (int) ($old['idActivo'] ?? 0)
                                    === (int) $asset['idActivo']
                                ) ? 'selected' : '' ?>
                            >
                                <?= e(
                                    $asset['codigoActivo']
                                    . ' — '
                                    . $asset['nombreProducto']
                                    . (
                                        trim(
                                            ($asset['marca'] ?? '')
                                            . ' '
                                            . ($asset['modelo'] ?? '')
                                        ) !== ''
                                            ? ' ('
                                                . trim(
                                                    ($asset['marca'] ?? '')
                                                    . ' '
                                                    . ($asset['modelo'] ?? '')
                                                )
                                                . ')'
                                            : ''
                                    )
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (!empty($errors['idActivo'])): ?>
                        <small class="field__error">
                            <?= e($errors['idActivo']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field field--full">
                    <label for="idColaborador">Colaborador</label>

                    <select
                        id="idColaborador"
                        name="idColaborador"
                        required
                    >
                        <option value="">
                            Selecciona un colaborador
                        </option>

                        <?php foreach ($collaborators as $collaborator): ?>
                            <option
                                value="<?= e(
                                    $collaborator['idColaborador']
                                ) ?>"
                                <?= (
                                    (int) ($old['idColaborador'] ?? 0)
                                    === (int) $collaborator['idColaborador']
                                ) ? 'selected' : '' ?>
                            >
                                <?= e(
                                    $collaborator['apellido']
                                    . ', '
                                    . $collaborator['nombre']
                                    . ' — '
                                    . (
                                        $collaborator['departamento']
                                        ?? 'Sin departamento'
                                    )
                                    . (
                                        !empty($collaborator['ubicacionActual'])
                                            ? ' — Ubicación actual: '
                                                . $collaborator['ubicacionActual']
                                            : ''
                                    )
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (!empty($errors['idColaborador'])): ?>
                        <small class="field__error">
                            <?= e($errors['idColaborador']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field field--full">
                    <label for="idUbicacion">
                        Ubicación de entrega
                    </label>

                    <select
                        id="idUbicacion"
                        name="idUbicacion"
                        required
                    >
                        <option value="">
                            Selecciona una ubicación
                        </option>

                        <?php foreach ($locations as $location): ?>
                            <option
                                value="<?= e($location['idUbicacion']) ?>"
                                <?= (
                                    (int) ($old['idUbicacion'] ?? 0)
                                    === (int) $location['idUbicacion']
                                ) ? 'selected' : '' ?>
                            >
                                <?= e(
                                    $location['nombreUbicacion']
                                    . (
                                        trim(
                                            ($location['edificio'] ?? '')
                                            . ' '
                                            . ($location['piso'] ?? '')
                                            . ' '
                                            . ($location['oficina'] ?? '')
                                        ) !== ''
                                            ? ' — '
                                                . trim(
                                                    ($location['edificio'] ?? '')
                                                    . ' '
                                                    . ($location['piso'] ?? '')
                                                    . ' '
                                                    . ($location['oficina'] ?? '')
                                                )
                                            : ''
                                    )
                                ) ?>
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
                    <label class="checkbox-field assignment-checkbox">
                        <input
                            type="checkbox"
                            name="actualizarUbicacionColaborador"
                            value="1"
                            <?= ($old['actualizarUbicacionColaborador'] ?? '0')
                                === '1'
                                ? 'checked'
                                : '' ?>
                        >

                        Usar esta ubicación como ubicación actual del
                        colaborador.
                    </label>

                    <small class="field__help">
                        Esto permitirá que el técnico sepa en qué edificio,
                        piso u oficina localizar al colaborador.
                    </small>
                </div>

                <div class="field field--full">
                    <label for="observacionesEntrega">
                        Observaciones de entrega
                    </label>

                    <textarea
                        id="observacionesEntrega"
                        name="observacionesEntrega"
                        maxlength="2000"
                        placeholder="Accesorios entregados, condición inicial o indicaciones especiales."
                    ><?= e($old['observacionesEntrega'] ?? '') ?></textarea>

                    <?php if (!empty($errors['observacionesEntrega'])): ?>
                        <small class="field__error">
                            <?= e($errors['observacionesEntrega']) ?>
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

                <button
                    class="button"
                    type="submit"
                    <?= $assets === [] ? 'disabled' : '' ?>
                >
                    Confirmar asignación
                </button>
            </div>
        </form>
    </div>
</section>
