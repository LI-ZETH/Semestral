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
                value="<?= e(
                    $category['nombreCategoria']
                    ?? ''
                ) ?>"
                required
            >

            <?php if (
                !empty($errors['nombreCategoria'])
            ): ?>
                <small class="field__error">
                    <?= e(
                        $errors['nombreCategoria']
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
                placeholder="Describe los productos que pertenecen a esta categoría."
            ><?= e(
                $category['descripcion']
                ?? ''
            ) ?></textarea>

            <?php if (
                !empty($errors['descripcion'])
            ): ?>
                <small class="field__error">
                    <?= e($errors['descripcion']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field field--full">
            <label for="imagen">
                Imagen de la categoría
            </label>

            <input
                id="imagen"
                name="imagen"
                type="file"
                accept=".jpg,.jpeg,.png,.webp"
                <?= $isEdit ? '' : 'required' ?>
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
            && !empty($category['imagen'])
        ): ?>
            <div class="field field--full">
                <span class="field__label">
                    Imagen actual
                </span>

                <div class="current-image-preview">
                    <img
                        src="<?= e(
                            asset_url(
                                $category['imagen']
                            )
                        ) ?>"
                        alt="<?= e(
                            $category[
                                'nombreCategoria'
                            ]
                        ) ?>"
                    >
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-actions">
        <a
            class="button button--secondary"
            href="<?= e(
                base_url(
                    'inventario/categorias'
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