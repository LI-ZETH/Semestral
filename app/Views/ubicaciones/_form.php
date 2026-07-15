<form method="POST" action="<?= e($action) ?>">
    <?= csrf_field() ?>

    <?php if (!empty($location['idUbicacion'])): ?>
        <input
            type="hidden"
            name="idUbicacion"
            value="<?= e($location['idUbicacion']) ?>"
        >
    <?php endif; ?>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert--error">
            <?= e($errors['general']) ?>
        </div>
    <?php endif; ?>

    <div class="form-grid">
        <div class="field">
            <label for="nombreUbicacion">
                Nombre de la ubicación
            </label>

            <input
                id="nombreUbicacion"
                name="nombreUbicacion"
                type="text"
                maxlength="100"
                value="<?= e(
                    $location['nombreUbicacion'] ?? ''
                ) ?>"
                placeholder="Ej. Oficina principal - Piso 3"
                required
            >

            <?php if (!empty($errors['nombreUbicacion'])): ?>
                <small class="field__error">
                    <?= e($errors['nombreUbicacion']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="tipoUbicacion">
                Tipo de ubicación
            </label>

            <select
                id="tipoUbicacion"
                name="tipoUbicacion"
                required
            >
                <?php foreach ($types as $type): ?>
                    <option
                        value="<?= e($type) ?>"
                        <?= (
                            ($location['tipoUbicacion'] ?? '')
                            === $type
                        ) ? 'selected' : '' ?>
                    >
                        <?= e(match ($type) {
                            'EDIFICIO' => 'Edificio',
                            'OFICINA' => 'Oficina',
                            'CASA' => 'Casa',
                            'BODEGA' => 'Bodega',
                            default => 'Otra',
                        }) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if (!empty($errors['tipoUbicacion'])): ?>
                <small class="field__error">
                    <?= e($errors['tipoUbicacion']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="edificio">Edificio</label>

            <input
                id="edificio"
                name="edificio"
                type="text"
                maxlength="80"
                value="<?= e($location['edificio'] ?? '') ?>"
                placeholder="Ej. Edificio Central"
            >

            <?php if (!empty($errors['edificio'])): ?>
                <small class="field__error">
                    <?= e($errors['edificio']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="piso">Piso</label>

            <input
                id="piso"
                name="piso"
                type="text"
                maxlength="30"
                value="<?= e($location['piso'] ?? '') ?>"
                placeholder="Ej. Piso 3"
            >

            <?php if (!empty($errors['piso'])): ?>
                <small class="field__error">
                    <?= e($errors['piso']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="oficina">Oficina o área</label>

            <input
                id="oficina"
                name="oficina"
                type="text"
                maxlength="50"
                value="<?= e($location['oficina'] ?? '') ?>"
                placeholder="Ej. Recursos Humanos"
            >

            <?php if (!empty($errors['oficina'])): ?>
                <small class="field__error">
                    <?= e($errors['oficina']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="direccion">Dirección</label>

            <input
                id="direccion"
                name="direccion"
                type="text"
                maxlength="255"
                value="<?= e($location['direccion'] ?? '') ?>"
                placeholder="Dirección física"
            >

            <?php if (!empty($errors['direccion'])): ?>
                <small class="field__error">
                    <?= e($errors['direccion']) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="field field--full">
            <label for="descripcion">Descripción</label>

            <textarea
                id="descripcion"
                name="descripcion"
                maxlength="255"
                placeholder="Información adicional de la ubicación."
            ><?= e($location['descripcion'] ?? '') ?></textarea>

            <?php if (!empty($errors['descripcion'])): ?>
                <small class="field__error">
                    <?= e($errors['descripcion']) ?>
                </small>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <a
            class="button button--secondary"
            href="<?= e(base_url('ubicaciones')) ?>"
        >
            Cancelar
        </a>

        <button class="button" type="submit">
            <?= e($submitLabel) ?>
        </button>
    </div>
</form>
