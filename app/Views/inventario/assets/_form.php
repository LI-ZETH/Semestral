<form
    method="POST"
    action="<?= e($action) ?>"
    enctype="multipart/form-data"
>
    <?= csrf_field() ?>

    <?php if (!empty($asset['idActivo'])): ?>
        <input
            type="hidden"
            name="idActivo"
            value="<?= e($asset['idActivo']) ?>"
        >
    <?php endif; ?>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert--error">
            <?= e($errors['general']) ?>
        </div>
    <?php endif; ?>

    <div class="form-grid">
        <?php if ($isEdit): ?>
            <div class="field field--full">
                <span class="field__label">Producto general</span>

                <div class="readonly-product-card">
                    <strong>
                        <?= e($asset['nombreProducto']) ?>
                    </strong>

                    <span>
                        <?= e(
                            trim(
                                ($asset['marca'] ?? '')
                                . ' '
                                . ($asset['modelo'] ?? '')
                            ) ?: 'Sin marca o modelo'
                        ) ?>
                    </span>

                    <small>
                        <?= e($asset['nombreCategoria']) ?>
                        ·
                        <?= e($asset['nombreSubcategoria']) ?>
                    </small>
                </div>

                <input
                    type="hidden"
                    name="idProducto"
                    value="<?= e($asset['idProducto']) ?>"
                >
            </div>
        <?php else: ?>
            <div class="field field--full">
                <label for="idProducto">
                    Producto general
                </label>

                <select
                    id="idProducto"
                    name="idProducto"
                    required
                >
                    <option value="">
                        Selecciona un producto
                    </option>

                    <?php foreach ($products as $product): ?>
                        <option
                            value="<?= e($product['idProducto']) ?>"
                            <?= (
                                (int) ($asset['idProducto'] ?? 0)
                                === (int) $product['idProducto']
                            ) ? 'selected' : '' ?>
                        >
                            <?= e($product['nombreCategoria']) ?>
                            —
                            <?= e($product['nombreSubcategoria']) ?>
                            —
                            <?= e($product['nombreProducto']) ?>
                            <?= e(
                                trim(
                                    ($product['marca'] ?? '')
                                    . ' '
                                    . ($product['modelo'] ?? '')
                                ) !== ''
                                    ? ' ('
                                        . trim(
                                            ($product['marca'] ?? '')
                                            . ' '
                                            . ($product['modelo'] ?? '')
                                        )
                                        . ')'
                                    : ''
                            ) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if (!empty($errors['idProducto'])): ?>
                    <small class="field__error">
                        <?= e($errors['idProducto']) ?>
                    </small>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="field">
            <label for="codigoActivo">
                Código único del activo
            </label>

            <input
                id="codigoActivo"
                name="codigoActivo"
                type="text"
                maxlength="40"
                value="<?= e($asset['codigoActivo'] ?? '') ?>"
                placeholder="Ejemplo: TRK-LAP-000001"
                required
            >

            <small class="field__help">
                Se convertirá automáticamente a mayúsculas.
            </small>

            <?php if (!empty($errors['codigoActivo'])): ?>
                <small class="field__error">
                    <?= e($errors['codigoActivo']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="numeroSerie">
                Número de serie
                <span class="field__optional">Opcional</span>
            </label>

            <input
                id="numeroSerie"
                name="numeroSerie"
                type="text"
                maxlength="120"
                value="<?= e($asset['numeroSerie'] ?? '') ?>"
                placeholder="Serie suministrada por el fabricante"
            >

            <?php if (!empty($errors['numeroSerie'])): ?>
                <small class="field__error">
                    <?= e($errors['numeroSerie']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="direccionIP">
                Dirección IP
                <span class="field__optional">Opcional</span>
            </label>

            <input
                id="direccionIP"
                name="direccionIP"
                type="text"
                maxlength="45"
                value="<?= e($asset['direccionIP'] ?? '') ?>"
                placeholder="Ejemplo: 192.168.1.25"
            >

            <?php if (!empty($errors['direccionIP'])): ?>
                <small class="field__error">
                    <?= e($errors['direccionIP']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="idEstadoActivo">
                Estado del activo
            </label>

            <select
                id="idEstadoActivo"
                name="idEstadoActivo"
                required
            >
                <option value="">
                    Selecciona un estado
                </option>

                <?php foreach ($states as $state): ?>
                    <option
                        value="<?= e($state['idEstadoActivo']) ?>"
                        <?= (
                            (int) ($asset['idEstadoActivo'] ?? 0)
                            === (int) $state['idEstadoActivo']
                        ) ? 'selected' : '' ?>
                        <?= (
                            $state['codigoEstado'] === 'ASIGNADO'
                            && (int) ($asset['idEstadoActivo'] ?? 0)
                                !== (int) $state['idEstadoActivo']
                        ) ? 'disabled' : '' ?>
                    >
                        <?= e($state['nombreEstado']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <small class="field__help">
                “Asignado” se controla desde el módulo de asignaciones.
            </small>

            <?php if (!empty($errors['idEstadoActivo'])): ?>
                <small class="field__error">
                    <?= e($errors['idEstadoActivo']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="fechaAdquisicion">
                Fecha de adquisición
            </label>

            <input
                id="fechaAdquisicion"
                name="fechaAdquisicion"
                type="date"
                value="<?= e($asset['fechaAdquisicion'] ?? '') ?>"
                required
            >

            <?php if (!empty($errors['fechaAdquisicion'])): ?>
                <small class="field__error">
                    <?= e($errors['fechaAdquisicion']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="fechaIngreso">
                Fecha de ingreso al inventario
            </label>

            <input
                id="fechaIngreso"
                name="fechaIngreso"
                type="date"
                value="<?= e($asset['fechaIngreso'] ?? '') ?>"
                required
            >

            <?php if (!empty($errors['fechaIngreso'])): ?>
                <small class="field__error">
                    <?= e($errors['fechaIngreso']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="costo">
                Costo de adquisición
            </label>

            <input
                id="costo"
                name="costo"
                type="number"
                min="0"
                max="9999999999.99"
                step="0.01"
                value="<?= e($asset['costo'] ?? '0.00') ?>"
                required
            >

            <?php if (!empty($errors['costo'])): ?>
                <small class="field__error">
                    <?= e($errors['costo']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="valorResidual">
                Valor residual
            </label>

            <input
                id="valorResidual"
                name="valorResidual"
                type="number"
                min="0"
                max="9999999999.99"
                step="0.01"
                value="<?= e($asset['valorResidual'] ?? '0.00') ?>"
                required
            >

            <?php if (!empty($errors['valorResidual'])): ?>
                <small class="field__error">
                    <?= e($errors['valorResidual']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="vidaUtilMeses">
                Vida útil específica
                <span class="field__optional">Opcional</span>
            </label>

            <input
                id="vidaUtilMeses"
                name="vidaUtilMeses"
                type="number"
                min="1"
                max="600"
                value="<?= e($asset['vidaUtilMeses'] ?? '') ?>"
                placeholder="Ejemplo: 60"
            >

            <small class="field__help">
                Al dejarla vacía se usa la vida útil del producto.
            </small>

            <?php if (!empty($errors['vidaUtilMeses'])): ?>
                <small class="field__error">
                    <?= e($errors['vidaUtilMeses']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="idUbicacion">
                Ubicación actual
                <span class="field__optional">Opcional</span>
            </label>

            <select
                id="idUbicacion"
                name="idUbicacion"
            >
                <option value="">
                    Sin ubicación asignada
                </option>

                <?php foreach ($locations as $location): ?>
                    <option
                        value="<?= e($location['idUbicacion']) ?>"
                        <?= (
                            (int) ($asset['idUbicacion'] ?? 0)
                            === (int) $location['idUbicacion']
                        ) ? 'selected' : '' ?>
                    >
                        <?= e($location['nombreUbicacion']) ?>
                        <?= !empty($location['edificio'])
                            ? ' — ' . e($location['edificio'])
                            : '' ?>
                        <?= !empty($location['piso'])
                            ? ' / Piso ' . e($location['piso'])
                            : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if ($locations === []): ?>
                <small class="field__help">
                    Todavía no hay ubicaciones registradas. Puedes dejar
                    este campo vacío por ahora.
                </small>
            <?php endif; ?>

            <?php if (!empty($errors['idUbicacion'])): ?>
                <small class="field__error">
                    <?= e($errors['idUbicacion']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field field--full">
            <label for="observaciones">
                Observaciones
                <span class="field__optional">Opcional</span>
            </label>

            <textarea
                id="observaciones"
                name="observaciones"
                maxlength="2000"
                placeholder="Condición física, accesorios incluidos o información adicional."
            ><?= e($asset['observaciones'] ?? '') ?></textarea>

            <?php if (!empty($errors['observaciones'])): ?>
                <small class="field__error">
                    <?= e($errors['observaciones']) ?>
                </small>
            <?php endif; ?>
        </div>

        <?php if ($isEdit && !empty($asset['images'])): ?>
            <div class="field field--full">
                <span class="field__label">
                    Imágenes actuales
                </span>

                <p class="field__help asset-image-instructions">
                    Selecciona cuál será la imagen principal. Puedes
                    eliminar imágenes, pero deben quedar por lo menos dos.
                </p>

                <div class="asset-image-management-grid">
                    <?php foreach ($asset['images'] as $image): ?>
                        <article class="asset-image-management-card">
                            <img
                                src="<?= e(
                                    asset_url($image['rutaImagen'])
                                ) ?>"
                                alt="Imagen del activo"
                            >

                            <div class="asset-image-management-card__options">
                                <label>
                                    <input
                                        type="radio"
                                        name="imagenPrincipalId"
                                        value="<?= e(
                                            $image['idImagenActivo']
                                        ) ?>"
                                        <?= (
                                            (int) (
                                                $asset['imagenPrincipalId']
                                                ?? 0
                                            )
                                            === (int) $image['idImagenActivo']
                                            || (
                                                empty(
                                                    $asset['imagenPrincipalId']
                                                )
                                                && (bool) $image['esPrincipal']
                                            )
                                        ) ? 'checked' : '' ?>
                                    >
                                    Principal
                                </label>

                                <label class="asset-image-remove-option">
                                    <input
                                        type="checkbox"
                                        name="eliminarImagenes[]"
                                        value="<?= e(
                                            $image['idImagenActivo']
                                        ) ?>"
                                    >
                                    Eliminar
                                </label>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($errors['imagenPrincipalId'])): ?>
                    <small class="field__error">
                        <?= e($errors['imagenPrincipalId']) ?>
                    </small>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="field field--full">
            <label for="imagenes">
                <?= $isEdit
                    ? 'Agregar nuevas imágenes'
                    : 'Imágenes del activo' ?>
            </label>

            <input
                id="imagenes"
                name="imagenes[]"
                type="file"
                accept=".jpg,.jpeg,.png,.webp"
                multiple
                <?= $isEdit ? '' : 'required' ?>
            >

            <small class="field__help">
                <?= $isEdit
                    ? 'Puedes agregar imágenes adicionales. El máximo total es ocho.'
                    : 'Selecciona entre dos y ocho imágenes. La primera será la principal.' ?>
                Cada archivo puede pesar hasta 2 MB.
            </small>

            <?php if (!empty($errors['imagenes'])): ?>
                <small class="field__error">
                    <?= e($errors['imagenes']) ?>
                </small>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <a
            class="button button--secondary"
            href="<?= e(
                base_url(
                    'inventario/activos?producto='
                    . ($asset['idProducto'] ?? 0)
                )
            ) ?>"
        >
            Cancelar
        </a>

        <button class="button" type="submit">
            <?= e($submitLabel) ?>
        </button>
    </div>
</form>
