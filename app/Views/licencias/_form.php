<form method="POST" action="<?= e($action) ?>">
    <?= csrf_field() ?>

    <?php if ($isEdit): ?>
        <input
            type="hidden"
            name="idLicencia"
            value="<?= e($license['idLicencia']) ?>"
        >
    <?php endif; ?>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert--error">
            <?= e($errors['general']) ?>
        </div>
    <?php endif; ?>

    <div class="form-grid">
        <?php if (!$isEdit): ?>
            <div class="field field--full">
                <label for="idActivo">Copia de licencia</label>

                <select id="idActivo" name="idActivo" required>
                    <option value="">Selecciona una copia</option>

                    <?php foreach ($assets as $asset): ?>
                        <option
                            value="<?= e($asset['idActivo']) ?>"
                            <?= (
                                (int) ($license['idActivo'] ?? 0)
                                === (int) $asset['idActivo']
                            ) ? 'selected' : '' ?>
                        >
                            <?= e(
                                $asset['codigoActivo']
                                . ' — '
                                . $asset['nombreProducto']
                                . ' '
                                . trim(
                                    ($asset['marca'] ?? '')
                                    . ' '
                                    . ($asset['modelo'] ?? '')
                                )
                            ) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if ($assets === []): ?>
                    <small class="field__help">
                        Primero registra un producto de tipo Licencia y
                        una copia individual para ese producto.
                    </small>
                <?php endif; ?>

                <?php if (!empty($errors['idActivo'])): ?>
                    <small class="field__error">
                        <?= e($errors['idActivo']) ?>
                    </small>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="license-selected-asset field--full">
                <span class="section-heading__eyebrow">
                    Copia vinculada
                </span>
                <strong>
                    <?= e(
                        $license['codigoActivo']
                        . ' — '
                        . $license['nombreProducto']
                    ) ?>
                </strong>
                <small>
                    La copia vinculada no puede cambiarse después del
                    registro.
                </small>
            </div>
        <?php endif; ?>

        <div class="field">
            <label for="proveedor">Proveedor</label>
            <input
                id="proveedor"
                name="proveedor"
                type="text"
                maxlength="120"
                value="<?= e($license['proveedor'] ?? '') ?>"
                placeholder="Microsoft, Adobe, Autodesk..."
            >
            <?php if (!empty($errors['proveedor'])): ?>
                <small class="field__error">
                    <?= e($errors['proveedor']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="tipoLicencia">Tipo de licencia</label>
            <input
                id="tipoLicencia"
                name="tipoLicencia"
                type="text"
                maxlength="80"
                value="<?= e($license['tipoLicencia'] ?? '') ?>"
                placeholder="Suscripción, perpetua, volumen..."
                required
            >
            <?php if (!empty($errors['tipoLicencia'])): ?>
                <small class="field__error">
                    <?= e($errors['tipoLicencia']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field field--full">
            <label for="urlAcceso">URL de acceso</label>
            <input
                id="urlAcceso"
                name="urlAcceso"
                type="url"
                maxlength="500"
                value="<?= e($license['urlAcceso'] ?? '') ?>"
                placeholder="https://portal.ejemplo.com"
            >
            <?php if (!empty($errors['urlAcceso'])): ?>
                <small class="field__error">
                    <?= e($errors['urlAcceso']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="cantidadPuestos">Cantidad de puestos</label>
            <input
                id="cantidadPuestos"
                name="cantidadPuestos"
                type="number"
                min="1"
                max="100000"
                value="<?= e($license['cantidadPuestos'] ?? 1) ?>"
                required
            >
            <?php if (!empty($errors['cantidadPuestos'])): ?>
                <small class="field__error">
                    <?= e($errors['cantidadPuestos']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="claveLicencia">
                <?= $isEdit
                    ? 'Reemplazar clave de licencia'
                    : 'Clave de licencia' ?>
            </label>
            <input
                id="claveLicencia"
                name="claveLicencia"
                type="password"
                maxlength="180"
                autocomplete="new-password"
                placeholder="Se almacenará cifrada con RSA"
            >
            <small class="field__help">
                Por seguridad, la clave guardada nunca se muestra en este
                formulario.
            </small>
            <?php if (!empty($errors['claveLicencia'])): ?>
                <small class="field__error">
                    <?= e($errors['claveLicencia']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="fechaInicio">Fecha de inicio</label>
            <input
                id="fechaInicio"
                name="fechaInicio"
                type="date"
                value="<?= e($license['fechaInicio'] ?? '') ?>"
            >
            <?php if (!empty($errors['fechaInicio'])): ?>
                <small class="field__error">
                    <?= e($errors['fechaInicio']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="fechaExpiracion">Fecha de expiración</label>
            <input
                id="fechaExpiracion"
                name="fechaExpiracion"
                type="date"
                value="<?= e($license['fechaExpiracion'] ?? '') ?>"
            >
            <?php if (!empty($errors['fechaExpiracion'])): ?>
                <small class="field__error">
                    <?= e($errors['fechaExpiracion']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field field--full">
            <label class="checkbox-field">
                <input
                    type="checkbox"
                    name="renovacionAutomatica"
                    value="1"
                    <?= !empty($license['renovacionAutomatica'])
                        ? 'checked'
                        : '' ?>
                >
                Renovación automática
            </label>
        </div>

        <?php if ($isEdit && !empty($license['claveCifrada'])): ?>
            <div class="field field--full">
                <label class="checkbox-field checkbox-field--danger">
                    <input
                        type="checkbox"
                        name="eliminarClave"
                        value="1"
                    >
                    Eliminar la clave cifrada actualmente almacenada
                </label>
            </div>
        <?php endif; ?>

        <div class="field field--full">
            <label for="observaciones">Observaciones</label>
            <textarea
                id="observaciones"
                name="observaciones"
                maxlength="2000"
                placeholder="Condiciones de compra, contrato, restricciones..."
            ><?= e($license['observaciones'] ?? '') ?></textarea>
            <?php if (!empty($errors['observaciones'])): ?>
                <small class="field__error">
                    <?= e($errors['observaciones']) ?>
                </small>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <a class="button button--secondary" href="<?= e($cancelUrl) ?>">
            Cancelar
        </a>
        <button class="button" type="submit">
            <?= e($submitLabel) ?>
        </button>
    </div>
</form>
