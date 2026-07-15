<section class="form-section form-section--wide">
    <div class="form-card">
        <div class="form-card__header">
            <span class="section-heading__eyebrow">
                Ciclo de vida del inventario
            </span>

            <h1>Registrar baja de activo</h1>

            <p>
                Formaliza un descarte o una donación. El registro
                conservará todo el historial previo del activo.
            </p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert--error">
                <?= e($errors['general']) ?>
            </div>
        <?php endif; ?>

        <?php if ($assets === []): ?>
            <div class="alert alert--warning">
                No hay copias disponibles para baja. Una copia no puede
                estar asignada, tener reparaciones abiertas ni puestos de
                licencia activos.
            </div>
        <?php endif; ?>

        <form
            method="POST"
            action="<?= e(base_url('bajas/guardar')) ?>"
            data-disposal-form
        >
            <?= csrf_field() ?>

            <div class="form-grid">
                <div class="field field--full">
                    <label for="idActivo">
                        Copia del inventario
                    </label>

                    <select
                        id="idActivo"
                        name="idActivo"
                        required
                    >
                        <option value="">
                            Selecciona una copia disponible
                        </option>

                        <?php foreach ($assets as $asset): ?>
                            <option
                                value="<?= e($asset['idActivo']) ?>"
                                <?= (
                                    (int) ($old['idActivo'] ?? 0)
                                    === (int) $asset['idActivo']
                                ) ? 'selected' : '' ?>
                            >
                                <?= e(
                                    $asset['codigoActivo']
                                    . ' — '
                                    . $asset['nombreProducto']
                                    . ' — '
                                    . $asset['nombreEstado']
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <small class="field__help">
                        Solo aparecen copias devueltas, sin procesos
                        técnicos abiertos y sin una baja anterior.
                    </small>

                    <?php if (!empty($errors['idActivo'])): ?>
                        <small class="field__error">
                            <?= e($errors['idActivo']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="idTipoBaja">
                        Tipo de baja
                    </label>

                    <select
                        id="idTipoBaja"
                        name="idTipoBaja"
                        required
                        data-disposal-type
                    >
                        <option value="">
                            Selecciona el tipo
                        </option>

                        <?php foreach ($types as $type): ?>
                            <option
                                value="<?= e($type['idTipoBaja']) ?>"
                                data-type-code="<?= e($type['codigoTipo']) ?>"
                                <?= (
                                    (int) ($old['idTipoBaja'] ?? 0)
                                    === (int) $type['idTipoBaja']
                                ) ? 'selected' : '' ?>
                            >
                                <?= e($type['nombreTipo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <?php if (!empty($errors['idTipoBaja'])): ?>
                        <small class="field__error">
                            <?= e($errors['idTipoBaja']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="fechaBaja">
                        Fecha de baja
                    </label>

                    <input
                        id="fechaBaja"
                        name="fechaBaja"
                        type="date"
                        max="<?= e(date('Y-m-d')) ?>"
                        value="<?= e(
                            $old['fechaBaja']
                            ?? date('Y-m-d')
                        ) ?>"
                        required
                    >

                    <?php if (!empty($errors['fechaBaja'])): ?>
                        <small class="field__error">
                            <?= e($errors['fechaBaja']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field field--full">
                    <label for="motivo">
                        Motivo de la baja
                    </label>

                    <textarea
                        id="motivo"
                        name="motivo"
                        maxlength="4000"
                        required
                        placeholder="Explica por qué el activo debe salir de operación."
                    ><?= e($old['motivo'] ?? '') ?></textarea>

                    <?php if (!empty($errors['motivo'])): ?>
                        <small class="field__error">
                            <?= e($errors['motivo']) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="field field--full">
                    <label for="opinionTecnica">
                        Opinión o diagnóstico técnico
                    </label>

                    <textarea
                        id="opinionTecnica"
                        name="opinionTecnica"
                        maxlength="4000"
                        placeholder="Describe el estado técnico y la recomendación final."
                    ><?= e($old['opinionTecnica'] ?? '') ?></textarea>

                    <small class="field__help">
                        Es obligatoria cuando el tipo seleccionado es
                        Descarte.
                    </small>

                    <?php if (!empty($errors['opinionTecnica'])): ?>
                        <small class="field__error">
                            <?= e($errors['opinionTecnica']) ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>

            <section
                class="disposal-donation-section"
                data-donation-fields
            >
                <div class="form-subsection__header">
                    <h2>Información de la donación</h2>
                    <p>
                        Completa estos datos únicamente cuando el activo
                        será entregado a una entidad beneficiaria.
                    </p>
                </div>

                <div class="form-grid">
                    <div class="field">
                        <label for="entidadBeneficiaria">
                            Entidad beneficiaria
                        </label>

                        <input
                            id="entidadBeneficiaria"
                            name="entidadBeneficiaria"
                            type="text"
                            maxlength="180"
                            value="<?= e(
                                $old['entidadBeneficiaria']
                                ?? ''
                            ) ?>"
                            data-donation-required
                        >

                        <?php if (!empty($errors['entidadBeneficiaria'])): ?>
                            <small class="field__error">
                                <?= e($errors['entidadBeneficiaria']) ?>
                            </small>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="responsableDonacion">
                            Responsable de recepción
                        </label>

                        <input
                            id="responsableDonacion"
                            name="responsableDonacion"
                            type="text"
                            maxlength="150"
                            value="<?= e(
                                $old['responsableDonacion']
                                ?? ''
                            ) ?>"
                            data-donation-required
                        >

                        <?php if (!empty($errors['responsableDonacion'])): ?>
                            <small class="field__error">
                                <?= e($errors['responsableDonacion']) ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <div class="form-grid disposal-document-grid">
                <div class="field field--full">
                    <label for="documentoReferencia">
                        Documento o referencia
                        <span class="field__optional">Opcional</span>
                    </label>

                    <input
                        id="documentoReferencia"
                        name="documentoReferencia"
                        type="text"
                        maxlength="100"
                        value="<?= e(
                            $old['documentoReferencia']
                            ?? ''
                        ) ?>"
                        placeholder="Ejemplo: ACTA-2026-015"
                    >

                    <?php if (!empty($errors['documentoReferencia'])): ?>
                        <small class="field__error">
                            <?= e($errors['documentoReferencia']) ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="disposal-confirmation">
                <label class="checkbox-field checkbox-field--danger">
                    <input
                        type="checkbox"
                        name="confirmarBaja"
                        value="1"
                        required
                    >

                    Confirmo que la información fue revisada y comprendo
                    que esta baja no podrá revertirse desde el sistema.
                </label>

                <?php if (!empty($errors['confirmarBaja'])): ?>
                    <small class="field__error">
                        <?= e($errors['confirmarBaja']) ?>
                    </small>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <a
                    class="button button--secondary"
                    href="<?= e(base_url('bajas')) ?>"
                >
                    Cancelar
                </a>

                <button
                    class="button button--danger"
                    type="submit"
                    <?= $assets === [] ? 'disabled' : '' ?>
                >
                    Registrar baja definitiva
                </button>
            </div>
        </form>
    </div>
</section>

<script src="<?= e(asset_url('assets/js/disposal-form.js')) ?>"></script>
