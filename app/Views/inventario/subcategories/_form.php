<form
    method="POST"
    action="<?= e($action) ?>"
    enctype="multipart/form-data"
>
    <?= csrf_field() ?>

    <?php if (!empty($subcategory['idSubcategoria'])): ?>
        <input
            type="hidden"
            name="idSubcategoria"
            value="<?= e(
                $subcategory['idSubcategoria']
            ) ?>"
        >
    <?php endif; ?>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert--error">
            <?= e($errors['general']) ?>
        </div>
    <?php endif; ?>

    <div class="form-grid">
        <div class="field">
            <label for="idCategoria">
                Categoría
            </label>

            <select
                id="idCategoria"
                name="idCategoria"
                required
            >
                <option value="">
                    Selecciona una categoría
                </option>

                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?= e(
                            $category['idCategoria']
                        ) ?>"
                        <?= (
                            (int) (
                                $subcategory['idCategoria']
                                ?? 0
                            )
                            ===
                            (int) $category['idCategoria']
                        ) ? 'selected' : '' ?>
                    >
                        <?= e(
                            $category['nombreCategoria']
                        ) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if (!empty($errors['idCategoria'])): ?>
                <small class="field__error">
                    <?= e($errors['idCategoria']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="nombreSubcategoria">
                Nombre de la subcategoría
            </label>

            <input
                id="nombreSubcategoria"
                name="nombreSubcategoria"
                type="text"
                maxlength="80"
                value="<?= e(
                    $subcategory['nombreSubcategoria']
                    ?? ''
                ) ?>"
                required
            >

            <?php if (
                !empty($errors['nombreSubcategoria'])
            ): ?>
                <small class="field__error">
                    <?= e(
                        $errors['nombreSubcategoria']
                    ) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field field--full">
            <label for="descripcion">
                Descripción
            </label>

            <textarea
                id="descripcion"
                name="descripcion"
                maxlength="255"
            ><?= e(
                $subcategory['descripcion']
                ?? ''
            ) ?></textarea>

            <?php if (!empty($errors['descripcion'])): ?>
                <small class="field__error">
                    <?= e($errors['descripcion']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field field--full">
            <label for="imagen">
                Imagen opcional
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

        <?php if (
            $isEdit
            && !empty($subcategory['imagen'])
        ): ?>
            <div class="field field--full">
                <span class="field__label">
                    Imagen actual
                </span>

                <div class="current-image-preview">
                    <img
                        src="<?= e(
                            asset_url(
                                $subcategory['imagen']
                            )
                        ) ?>"
                        alt="<?= e(
                            $subcategory[
                                'nombreSubcategoria'
                            ]
                        ) ?>"
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
                    'inventario/subcategorias?categoria='
                    . (
                        $subcategory['idCategoria']
                        ?? 0
                    )
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