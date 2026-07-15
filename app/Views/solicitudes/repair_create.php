<section class="form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Soporte técnico
            </span>

            <h1>Reportar reparación</h1>

            <p>
                Reporta una falla de uno de los equipos que se
                encuentran actualmente bajo tu custodia.
            </p>
        </div>

        <form
            method="POST"
            action="<?= e(base_url('solicitudes/reparacion/guardar')) ?>"
        >
            <?= csrf_field() ?>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert--error">
                    <?= e($errors['general']) ?>
                </div>
            <?php endif; ?>

            <div class="form-grid">
                <div class="field field--full">
                    <label for="idActivo">Equipo con la falla</label>

                    <select id="idActivo" name="idActivo" required>
                        <option value="">Selecciona un equipo</option>

                        <?php foreach ($assets as $asset): ?>
                            <option
                                value="<?= e($asset['idActivo']) ?>"
                                <?= ((int) ($old['idActivo'] ?? 0)
                                    === (int) $asset['idActivo'])
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= e($asset['codigoActivo']) ?>
                                —
                                <?= e($asset['nombreProducto']) ?>
                                <?= e(trim(
                                    ($asset['marca'] ?? '')
                                    . ' '
                                    . ($asset['modelo'] ?? '')
                                )) ?>
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
                    <label for="titulo">Resumen de la falla</label>

                    <input
                        id="titulo"
                        name="titulo"
                        type="text"
                        maxlength="150"
                        value="<?= e($old['titulo'] ?? '') ?>"
                        placeholder="Ejemplo: La laptop no enciende"
                        required
                    >

                    <?php if (!empty($errors['titulo'])): ?>
                        <small class="field__error">
                            <?= e($errors['titulo']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="prioridad">Prioridad</label>

                    <select id="prioridad" name="prioridad" required>
                        <?php foreach ([
                            'BAJA' => 'Baja',
                            'MEDIA' => 'Media',
                            'ALTA' => 'Alta',
                            'URGENTE' => 'Urgente',
                        ] as $value => $label): ?>
                            <option
                                value="<?= e($value) ?>"
                                <?= (($old['prioridad'] ?? 'MEDIA') === $value)
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field field--full">
                    <label for="descripcionFalla">
                        Descripción detallada
                    </label>

                    <textarea
                        id="descripcionFalla"
                        name="descripcionFalla"
                        maxlength="3000"
                        placeholder="Explica qué ocurre, cuándo comenzó y qué estabas haciendo cuando apareció la falla."
                        required
                    ><?= e($old['descripcionFalla'] ?? '') ?></textarea>

                    <?php if (!empty($errors['descripcionFalla'])): ?>
                        <small class="field__error">
                            <?= e($errors['descripcionFalla']) ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($assets === []): ?>
                <div class="alert alert--warning">
                    No tienes equipos activos bajo tu custodia.
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <a
                    class="button button--secondary"
                    href="<?= e(base_url('solicitudes')) ?>"
                >
                    Cancelar
                </a>

                <button
                    class="button"
                    type="submit"
                    <?= $assets === [] ? 'disabled' : '' ?>
                >
                    Enviar reporte
                </button>
            </div>
        </form>
    </div>
</section>
