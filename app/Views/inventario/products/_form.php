<form
    method="POST"
    action="<?= e($action) ?>"
    enctype="multipart/form-data"
>
    <?= csrf_field() ?>

    <?php if (!empty($product['idProducto'])): ?>
        <input
            type="hidden"
            name="idProducto"
            value="<?= e($product['idProducto']) ?>"
        >
    <?php endif; ?>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert--error">
            <?= e($errors['general']) ?>
        </div>
    <?php endif; ?>

    <div class="form-grid">
        <div class="field field--full">
            <label for="idSubcategoria">
                Categoría y subcategoría
            </label>

            <select
                id="idSubcategoria"
                name="idSubcategoria"
                required
            >
                <option value="">
                    Selecciona una subcategoría
                </option>

                <?php foreach ($subcategories as $subcategory): ?>
                    <option
                        value="<?= e($subcategory['idSubcategoria']) ?>"
                        <?= (
                            (int) ($product['idSubcategoria'] ?? 0)
                            === (int) $subcategory['idSubcategoria']
                        ) ? 'selected' : '' ?>
                    >
                        <?= e($subcategory['nombreCategoria']) ?>
                        —
                        <?= e($subcategory['nombreSubcategoria']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if (!empty($errors['idSubcategoria'])): ?>
                <small class="field__error">
                    <?= e($errors['idSubcategoria']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field field--full">
            <label for="nombreProducto">
                Nombre general del producto
            </label>

            <input
                id="nombreProducto"
                name="nombreProducto"
                type="text"
                maxlength="120"
                value="<?= e($product['nombreProducto'] ?? '') ?>"
                placeholder="Ejemplo: Laptop HP ProBook 450"
                required
            >

            <?php if (!empty($errors['nombreProducto'])): ?>
                <small class="field__error">
                    <?= e($errors['nombreProducto']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="marca">Marca</label>

            <input
                id="marca"
                name="marca"
                type="text"
                maxlength="80"
                value="<?= e($product['marca'] ?? '') ?>"
                placeholder="Ejemplo: HP"
            >

            <?php if (!empty($errors['marca'])): ?>
                <small class="field__error">
                    <?= e($errors['marca']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="modelo">Modelo</label>

            <input
                id="modelo"
                name="modelo"
                type="text"
                maxlength="100"
                value="<?= e($product['modelo'] ?? '') ?>"
                placeholder="Ejemplo: ProBook 450 G10"
            >

            <?php if (!empty($errors['modelo'])): ?>
                <small class="field__error">
                    <?= e($errors['modelo']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="tipoProducto">
                Tipo de producto
            </label>

            <select
                id="tipoProducto"
                name="tipoProducto"
                required
            >
                <?php
                $selectedType = $product['tipoProducto'] ?? 'HARDWARE';
                ?>

                <option
                    value="HARDWARE"
                    <?= $selectedType === 'HARDWARE' ? 'selected' : '' ?>
                >
                    Hardware
                </option>

                <option
                    value="SOFTWARE"
                    <?= $selectedType === 'SOFTWARE' ? 'selected' : '' ?>
                >
                    Software
                </option>

                <option
                    value="LICENCIA"
                    <?= $selectedType === 'LICENCIA' ? 'selected' : '' ?>
                >
                    Licencia
                </option>
            </select>

            <?php if (!empty($errors['tipoProducto'])): ?>
                <small class="field__error">
                    <?= e($errors['tipoProducto']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="vidaUtilMeses">
                Vida útil en meses
                <span class="field__optional">Opcional</span>
            </label>

            <input
                id="vidaUtilMeses"
                name="vidaUtilMeses"
                type="number"
                min="1"
                max="600"
                value="<?= e($product['vidaUtilMeses'] ?? '') ?>"
                placeholder="Ejemplo: 60"
            >

            <small class="field__help">
                Se utilizará como valor sugerido al registrar copias.
            </small>

            <?php if (!empty($errors['vidaUtilMeses'])): ?>
                <small class="field__error">
                    <?= e($errors['vidaUtilMeses']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field field--full">
            <label for="descripcion">
                Descripción
                <span class="field__optional">Opcional</span>
            </label>

            <textarea
                id="descripcion"
                name="descripcion"
                placeholder="Describe las características generales del producto."
            ><?= e($product['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="field field--full">
            <label for="imagen">
                <?= $isEdit ? 'Reemplazar imagen' : 'Imagen del producto' ?>
                <span class="field__optional">Opcional</span>
            </label>

            <input
                id="imagen"
                name="imagen"
                type="file"
                accept=".jpg,.jpeg,.png,.webp"
            >

            <small class="field__help">
                JPG, PNG o WEBP. Máximo 2 MB.
            </small>

            <?php if (!empty($errors['imagen'])): ?>
                <small class="field__error">
                    <?= e($errors['imagen']) ?>
                </small>
            <?php endif; ?>
        </div>

        <?php if ($isEdit && !empty($product['imagen'])): ?>
            <div class="field field--full">
                <span class="field__label">
                    Imagen actual
                </span>

                <div class="current-image-preview">
                    <img
                        src="<?= e(asset_url($product['imagen'])) ?>"
                        alt="<?= e($product['nombreProducto']) ?>"
                    >
                </div>

                <label class="checkbox-field">
                    <input
                        type="checkbox"
                        name="eliminarImagen"
                        value="1"
                    >

                    Eliminar imagen actual
                </label>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-actions">
        <a
            class="button button--secondary"
            href="<?= e(
                base_url(
                    'inventario/productos?subcategoria='
                    . ($product['idSubcategoria'] ?? 0)
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
