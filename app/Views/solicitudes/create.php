<section class="form-section">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Solicitudes
            </span>

            <h1>Nueva solicitud</h1>

            <p>
                Solicita un equipo, software, licencia u otra
                necesidad relacionada con tus funciones.
            </p>
        </div>

        <form
            method="POST"
            action="<?= e(base_url('solicitudes/guardar')) ?>"
        >
            <?= csrf_field() ?>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert--error">
                    <?= e($errors['general']) ?>
                </div>
            <?php endif; ?>

            <div class="form-grid">
                <div class="field">
                    <label for="tipoSolicitud">Tipo</label>

                    <select
                        id="tipoSolicitud"
                        name="tipoSolicitud"
                        required
                    >
                        <?php foreach ([
                            'EQUIPO' => 'Equipo',
                            'SOFTWARE' => 'Software',
                            'LICENCIA' => 'Licencia',
                            'OTRA' => 'Otra necesidad',
                        ] as $value => $label): ?>
                            <option
                                value="<?= e($value) ?>"
                                <?= (($old['tipoSolicitud'] ?? '') === $value)
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (!empty($errors['tipoSolicitud'])): ?>
                        <small class="field__error">
                            <?= e($errors['tipoSolicitud']) ?>
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
                    <label for="titulo">Título</label>

                    <input
                        id="titulo"
                        name="titulo"
                        type="text"
                        maxlength="150"
                        value="<?= e($old['titulo'] ?? '') ?>"
                        placeholder="Ejemplo: Laptop para trabajo de campo"
                        required
                    >

                    <?php if (!empty($errors['titulo'])): ?>
                        <small class="field__error">
                            <?= e($errors['titulo']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="idSubcategoria">
                        Subcategoría opcional
                    </label>

                    <select id="idSubcategoria" name="idSubcategoria">
                        <option value="">Sin especificar</option>

                        <?php foreach ($subcategories as $subcategory): ?>
                            <option
                                value="<?= e($subcategory['idSubcategoria']) ?>"
                                <?= ((int) ($old['idSubcategoria'] ?? 0)
                                    === (int) $subcategory['idSubcategoria'])
                                    ? 'selected'
                                    : '' ?>
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

                <div class="field">
                    <label for="idProducto">Producto opcional</label>

                    <select id="idProducto" name="idProducto">
                        <option value="">Sin producto específico</option>

                        <?php foreach ($products as $product): ?>
                            <option
                                value="<?= e($product['idProducto']) ?>"
                                <?= ((int) ($old['idProducto'] ?? 0)
                                    === (int) $product['idProducto'])
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= e($product['nombreCategoria']) ?>
                                —
                                <?= e($product['nombreSubcategoria']) ?>
                                —
                                <?= e($product['nombreProducto']) ?>
                                <?= e(trim(
                                    ($product['marca'] ?? '')
                                    . ' '
                                    . ($product['modelo'] ?? '')
                                )) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (!empty($errors['idProducto'])): ?>
                        <small class="field__error">
                            <?= e($errors['idProducto']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field field--full">
                    <label for="descripcionNecesidad">
                        Descripción de la necesidad
                    </label>

                    <textarea
                        id="descripcionNecesidad"
                        name="descripcionNecesidad"
                        maxlength="3000"
                        required
                    ><?= e($old['descripcionNecesidad'] ?? '') ?></textarea>

                    <?php if (!empty($errors['descripcionNecesidad'])): ?>
                        <small class="field__error">
                            <?= e($errors['descripcionNecesidad']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field field--full">
                    <label for="justificacion">Justificación</label>

                    <textarea
                        id="justificacion"
                        name="justificacion"
                        maxlength="3000"
                        required
                    ><?= e($old['justificacion'] ?? '') ?></textarea>

                    <?php if (!empty($errors['justificacion'])): ?>
                        <small class="field__error">
                            <?= e($errors['justificacion']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="cantidad">Cantidad</label>

                    <input
                        id="cantidad"
                        name="cantidad"
                        type="number"
                        min="1"
                        max="1000"
                        value="<?= e($old['cantidad'] ?? 1) ?>"
                        required
                    >

                    <?php if (!empty($errors['cantidad'])): ?>
                        <small class="field__error">
                            <?= e($errors['cantidad']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="periodoNecesidad">Período</label>

                    <select
                        id="periodoNecesidad"
                        name="periodoNecesidad"
                        required
                    >
                        <?php foreach ([
                            'INMEDIATA' => 'Inmediata',
                            'ANUAL' => 'Plan anual',
                            'QUINQUENAL' => 'Plan quinquenal',
                        ] as $value => $label): ?>
                            <option
                                value="<?= e($value) ?>"
                                <?= (($old['periodoNecesidad'] ?? 'INMEDIATA') === $value)
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="anioPresupuestado">
                        Año presupuestado
                    </label>

                    <input
                        id="anioPresupuestado"
                        name="anioPresupuestado"
                        type="number"
                        min="<?= e(date('Y')) ?>"
                        max="<?= e((int) date('Y') + 10) ?>"
                        value="<?= e($old['anioPresupuestado'] ?? '') ?>"
                        placeholder="Opcional"
                    >

                    <?php if (!empty($errors['anioPresupuestado'])): ?>
                        <small class="field__error">
                            <?= e($errors['anioPresupuestado']) ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-actions">
                <a
                    class="button button--secondary"
                    href="<?= e(base_url('solicitudes')) ?>"
                >
                    Cancelar
                </a>

                <button class="button" type="submit">
                    Registrar solicitud
                </button>
            </div>
        </form>
    </div>
</section>
