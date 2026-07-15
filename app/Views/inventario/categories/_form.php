<form
    method="POST"
    action="<?= e($action) ?>"
    enctype="multipart/form-data"
>
    <?= csrf_field() ?>

    <?php if (!empty($category['idCategoria'])): ?>
        <input
            type="hidden"
            name="idCategoria"
            value="<?= e($category['idCategoria']) ?>"
        >
    <?php endif; ?>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert--error">
            <?= e($errors['general']) ?>
        </div>
    <?php endif; ?>

    <div class="form-grid">
        <div class="field field--full">
            <label for="nombreCategoria">
                Nombre de la categoría
            </label>

            <input
                id="nombreCategoria"
                name="nombreCategoria"
                type="text"
                maxlength="80"
                value="<?= e($category['nombreCategoria'] ?? '') ?>"
                required
            >

            <?php if (!empty($errors['nombreCategoria'])): ?>
                <small class="field__error">
                    <?= e($errors['nombreCategoria']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field field--full">
            <label for="descripcion">Descripción</label>

            <textarea
                id="descripcion"
                name="descripcion"
                maxlength="255"
                placeholder="Describe los productos que pertenecen a esta categoría."
            ><?= e($category['descripcion'] ?? '') ?></textarea>

            <?php if (!empty($errors['descripcion'])): ?>
                <small class="field__error">
                    <?= e($errors['descripcion']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="imagenTamano">
                Tamaño de la imagen en la tarjeta
            </label>

            <select
                id="imagenTamano"
                name="imagenTamano"
                required
            >
                <?php
                $selectedSize = $category['imagenTamano'] ?? 'mediana';
                ?>
                <option value="compacta" <?= $selectedSize === 'compacta' ? 'selected' : '' ?>>
                    Compacta
                </option>
                <option value="mediana" <?= $selectedSize === 'mediana' ? 'selected' : '' ?>>
                    Mediana
                </option>
                <option value="amplia" <?= $selectedSize === 'amplia' ? 'selected' : '' ?>>
                    Amplia
                </option>
            </select>

            <?php if (!empty($errors['imagenTamano'])): ?>
                <small class="field__error">
                    <?= e($errors['imagenTamano']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="imagenAjuste">
                Ajuste de la imagen
            </label>

            <?php
            $selectedFit = $category['imagenAjuste'] ?? 'cover';
            ?>
            <select
                id="imagenAjuste"
                name="imagenAjuste"
                required
            >
                <option value="cover" <?= $selectedFit === 'cover' ? 'selected' : '' ?>>
                    Rellenar espacio (puede recortar)
                </option>
                <option value="contain" <?= $selectedFit === 'contain' ? 'selected' : '' ?>>
                    Mostrar completa (sin recortar)
                </option>
            </select>

            <?php if (!empty($errors['imagenAjuste'])): ?>
                <small class="field__error">
                    <?= e($errors['imagenAjuste']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field field--full">
            <label for="imagen">
                <?= $isEdit ? 'Reemplazar imagen' : 'Imagen de la categoría (opcional)' ?>
            </label>

            <input
                id="imagen"
                name="imagen"
                type="file"
                accept=".jpg,.jpeg,.png,.webp"
            >

            <small class="field__help">
                JPG, PNG o WEBP. Máximo 2 MB.
                En edición, selecciona un archivo solo cuando quieras reemplazar la imagen actual.
            </small>

            <?php if (!empty($errors['imagen'])): ?>
                <small class="field__error">
                    <?= e($errors['imagen']) ?>
                </small>
            <?php endif; ?>
        </div>

        <?php if ($isEdit && !empty($category['imagen'])): ?>
            <div class="field field--full">
                <span class="field__label">Imagen actual</span>

                <div class="image-management-row">
                    <div class="current-image-preview current-image-preview--<?= e($category['imagenAjuste'] ?? 'cover') ?>">
                        <img
                            src="<?= e(asset_url($category['imagen'])) ?>"
                            alt="<?= e($category['nombreCategoria']) ?>"
                        >
                    </div>

                    <label class="checkbox-card">
                        <input
                            type="checkbox"
                            name="eliminarImagen"
                            value="1"
                            <?= ($category['eliminarImagen'] ?? '0') === '1' ? 'checked' : '' ?>
                        >

                        <span>
                            <strong>Eliminar imagen actual</strong>
                            <small>
                                La categoría volverá a mostrar su inicial como imagen de respaldo.
                            </small>
                        </span>
                    </label>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-actions">
        <a
            class="button button--secondary"
            href="<?= e(base_url('inventario/categorias')) ?>"
        >
            Cancelar
        </a>

        <button class="button" type="submit">
            <?= e($submitLabel) ?>
        </button>
    </div>
</form>
