<section class="form-section">
    <div class="form-card form-card--wide">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">Asignación de licencia</span>
            <h1>Asignar <?= e($license['nombreProducto']) ?></h1>
            <p>
                <?= e($availableSeats) ?> puesto(s) disponible(s) de
                <?= e($license['cantidadPuestos']) ?>.
            </p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert--error"><?= e($errors['general']) ?></div>
        <?php endif; ?>

        <?php if ($availableSeats <= 0): ?>
            <div class="alert alert--warning">
                La licencia no tiene puestos disponibles.
            </div>
        <?php else: ?>
            <form method="POST" action="<?= e(base_url('licencias/asignar')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="idLicencia" value="<?= e($license['idLicencia']) ?>">

                <div class="form-grid">
                    <div class="field field--full">
                        <label for="idColaborador">Colaborador</label>
                        <select id="idColaborador" name="idColaborador" required>
                            <option value="">Selecciona un colaborador</option>
                            <?php foreach ($collaborators as $collaborator): ?>
                                <option
                                    value="<?= e($collaborator['idColaborador']) ?>"
                                    <?= (int) ($old['idColaborador'] ?? 0) === (int) $collaborator['idColaborador'] ? 'selected' : '' ?>
                                >
                                    <?= e(
                                        $collaborator['apellido'] . ', '
                                        . $collaborator['nombre'] . ' — '
                                        . $collaborator['correo']
                                    ) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['idColaborador'])): ?>
                            <small class="field__error"><?= e($errors['idColaborador']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="field field--full">
                        <label for="correoAsignado">Correo asociado a la licencia</label>
                        <input
                            id="correoAsignado"
                            name="correoAsignado"
                            type="email"
                            maxlength="120"
                            value="<?= e($old['correoAsignado'] ?? '') ?>"
                            placeholder="Puede ser distinto al correo institucional"
                        >
                        <?php if (!empty($errors['correoAsignado'])): ?>
                            <small class="field__error"><?= e($errors['correoAsignado']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="field field--full">
                        <label for="observaciones">Observaciones de asignación</label>
                        <textarea id="observaciones" name="observaciones" maxlength="2000"><?= e($old['observaciones'] ?? '') ?></textarea>
                        <?php if (!empty($errors['observaciones'])): ?>
                            <small class="field__error"><?= e($errors['observaciones']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <a
                        class="button button--secondary"
                        href="<?= e(base_url('licencias/ver?id=' . $license['idLicencia'])) ?>"
                    >
                        Cancelar
                    </a>
                    <button class="button" type="submit">Confirmar asignación</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>
