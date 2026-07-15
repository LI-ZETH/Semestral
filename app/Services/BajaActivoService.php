<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\ValidationException;
use App\Interfaces\BajaActivoRepositoryInterface;
use DateTimeImmutable;
use Throwable;

final class BajaActivoService
{
    public function __construct(
        private readonly BajaActivoRepositoryInterface $repository
    ) {
    }

    public function listAll(array $filters = []): array
    {
        return $this->repository->listAll($filters);
    }

    public function listEligibleAssets(): array
    {
        return $this->repository->listEligibleAssets();
    }

    public function listTypes(): array
    {
        return $this->repository->listTypes();
    }

    public function findById(int $disposalId): ?array
    {
        return $this->repository->findById($disposalId);
    }

    public function create(
        array $input,
        int $administratorUserId
    ): int {
        $data = $this->normalize($input);
        $errors = $this->validate($data);

        $asset = $this->repository->findAssetById(
            $data['idActivo']
        );
        $type = $this->repository->findTypeById(
            $data['idTipoBaja']
        );

        if (
            $asset === null
            || !(bool) ($asset['activo'] ?? false)
            || !(bool) ($asset['productoActivo'] ?? false)
            || !(bool) ($asset['subcategoriaActiva'] ?? false)
            || !(bool) ($asset['categoriaActiva'] ?? false)
        ) {
            $errors['idActivo'] =
                'Selecciona una copia activa del inventario.';
        }

        if (
            $asset !== null
            && !in_array(
                (string) ($asset['codigoEstado'] ?? ''),
                ['EN_INVENTARIO', 'REVISION_TECNICA'],
                true
            )
        ) {
            $errors['idActivo'] =
                'La copia debe estar en inventario o en revisión técnica antes de registrar su baja.';
        }

        if ($type === null) {
            $errors['idTipoBaja'] =
                'Selecciona un tipo de baja válido.';
        }

        if (
            $asset !== null
            && $data['fechaBaja'] !== ''
            && $data['fechaBaja'] < (string) $asset['fechaIngreso']
        ) {
            $errors['fechaBaja'] =
                'La fecha de baja no puede ser anterior al ingreso del activo.';
        }

        if ($type !== null) {
            $typeCode = (string) $type['codigoTipo'];

            if (
                $typeCode === 'DESCARTE'
                && mb_strlen($data['opinionTecnica']) < 10
            ) {
                $errors['opinionTecnica'] =
                    'La opinión técnica es obligatoria para justificar un descarte.';
            }

            if ($typeCode === 'DONACION') {
                if (
                    mb_strlen($data['responsableDonacion']) < 3
                    || mb_strlen($data['responsableDonacion']) > 150
                ) {
                    $errors['responsableDonacion'] =
                        'Indica el nombre de la persona responsable de recibir la donación.';
                }

                if (
                    mb_strlen($data['entidadBeneficiaria']) < 3
                    || mb_strlen($data['entidadBeneficiaria']) > 180
                ) {
                    $errors['entidadBeneficiaria'] =
                        'Indica la entidad beneficiaria de la donación.';
                }
            }
        }

        if (
            $asset !== null
            && $this->repository->hasRegisteredDisposal(
                (int) $asset['idActivo']
            )
        ) {
            $errors['idActivo'] =
                'La copia seleccionada ya tiene una baja registrada.';
        }

        if (
            $asset !== null
            && $this->repository->hasActiveAssignment(
                (int) $asset['idActivo']
            )
        ) {
            $errors['idActivo'] =
                'Primero debes registrar la devolución de la copia asignada.';
        }

        if (
            $asset !== null
            && $this->repository->hasOpenRepair(
                (int) $asset['idActivo']
            )
        ) {
            $errors['idActivo'] =
                'La copia tiene una reparación pendiente o en proceso.';
        }

        if (
            $asset !== null
            && $this->repository->hasOpenRepairRequest(
                (int) $asset['idActivo']
            )
        ) {
            $errors['idActivo'] =
                'La copia tiene una solicitud de reparación abierta.';
        }

        if (
            $asset !== null
            && $this->repository->hasActiveLicenseAssignments(
                (int) $asset['idActivo']
            )
        ) {
            $errors['idActivo'] =
                'La licencia tiene puestos activos. Revócalos antes de registrar la baja.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $stateCode = $type['codigoTipo'] === 'DONACION'
            ? 'DONADO'
            : 'DESCARTE';
        $targetState = $this->repository->findStateByCode(
            $stateCode
        );

        if (
            $targetState === null
            || !(bool) ($targetState['activo'] ?? false)
        ) {
            throw new ValidationException([
                'general' =>
                    'No existe el estado final requerido para registrar la baja.',
            ]);
        }

        try {
            $this->repository->beginTransaction();

            $lockedAsset = $this->repository->findAssetById(
                $data['idActivo'],
                true
            );

            if (
                $lockedAsset === null
                || !(bool) $lockedAsset['activo']
                || !in_array(
                    (string) $lockedAsset['codigoEstado'],
                    ['EN_INVENTARIO', 'REVISION_TECNICA'],
                    true
                )
                || $this->repository->hasRegisteredDisposal(
                    $data['idActivo']
                )
                || $this->repository->hasActiveAssignment(
                    $data['idActivo']
                )
                || $this->repository->hasOpenRepair(
                    $data['idActivo']
                )
                || $this->repository->hasOpenRepairRequest(
                    $data['idActivo']
                )
                || $this->repository->hasActiveLicenseAssignments(
                    $data['idActivo']
                )
            ) {
                throw new ValidationException([
                    'general' =>
                        'La copia dejó de estar disponible para registrar la baja.',
                ]);
            }

            $disposalId = $this->repository->create([
                'idActivo' => $data['idActivo'],
                'idTipoBaja' => $data['idTipoBaja'],
                'idUsuario' => $administratorUserId,
                'motivo' => $data['motivo'],
                'opinionTecnica' =>
                    $data['opinionTecnica'] !== ''
                        ? $data['opinionTecnica']
                        : null,
                'responsableDonacion' =>
                    $type['codigoTipo'] === 'DONACION'
                        ? $data['responsableDonacion']
                        : null,
                'entidadBeneficiaria' =>
                    $type['codigoTipo'] === 'DONACION'
                        ? $data['entidadBeneficiaria']
                        : null,
                'documentoReferencia' =>
                    $data['documentoReferencia'] !== ''
                        ? $data['documentoReferencia']
                        : null,
                'fechaBaja' => $this->formatDisposalDate(
                    $data['fechaBaja']
                ),
            ]);

            $this->repository->updateAssetState(
                $data['idActivo'],
                (int) $targetState['idEstadoActivo']
            );

            $description = $type['codigoTipo'] === 'DONACION'
                ? 'Donación del activo '
                    . $lockedAsset['codigoActivo']
                    . ' a '
                    . $data['entidadBeneficiaria']
                    . '.'
                : 'Descarte formal del activo '
                    . $lockedAsset['codigoActivo']
                    . '.';

            $this->repository->insertMovement([
                'idActivo' => $data['idActivo'],
                'idUsuario' => $administratorUserId,
                'tipoMovimiento' => $type['codigoTipo'],
                'idEstadoAnterior' =>
                    (int) $lockedAsset['idEstadoActivo'],
                'idEstadoNuevo' =>
                    (int) $targetState['idEstadoActivo'],
                'idUbicacionAnterior' =>
                    $this->nullableInt($lockedAsset['idUbicacion']),
                'idUbicacionNueva' =>
                    $this->nullableInt($lockedAsset['idUbicacion']),
                'descripcion' => $description,
            ]);

            $this->repository->commit();

            return $disposalId;
        } catch (Throwable $exception) {
            $this->repository->rollBack();
            throw $exception;
        }
    }

    private function normalize(array $input): array
    {
        return [
            'idActivo' => (int) ($input['idActivo'] ?? 0),
            'idTipoBaja' => (int) ($input['idTipoBaja'] ?? 0),
            'fechaBaja' => trim(
                (string) ($input['fechaBaja'] ?? '')
            ),
            'motivo' => trim(
                (string) ($input['motivo'] ?? '')
            ),
            'opinionTecnica' => trim(
                (string) ($input['opinionTecnica'] ?? '')
            ),
            'responsableDonacion' => trim(
                (string) ($input['responsableDonacion'] ?? '')
            ),
            'entidadBeneficiaria' => trim(
                (string) ($input['entidadBeneficiaria'] ?? '')
            ),
            'documentoReferencia' => trim(
                (string) ($input['documentoReferencia'] ?? '')
            ),
            'confirmarBaja' => (string) (
                $input['confirmarBaja'] ?? '0'
            ),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['idActivo'] <= 0) {
            $errors['idActivo'] =
                'Selecciona la copia que será dada de baja.';
        }

        if ($data['idTipoBaja'] <= 0) {
            $errors['idTipoBaja'] =
                'Selecciona el tipo de baja.';
        }

        if (!$this->isValidDate($data['fechaBaja'])) {
            $errors['fechaBaja'] =
                'Indica una fecha de baja válida.';
        } elseif ($data['fechaBaja'] > date('Y-m-d')) {
            $errors['fechaBaja'] =
                'La fecha de baja no puede estar en el futuro.';
        }

        $reasonLength = mb_strlen($data['motivo']);

        if ($reasonLength < 10 || $reasonLength > 4000) {
            $errors['motivo'] =
                'El motivo debe contener entre 10 y 4000 caracteres.';
        }

        if (mb_strlen($data['opinionTecnica']) > 4000) {
            $errors['opinionTecnica'] =
                'La opinión técnica no puede superar 4000 caracteres.';
        }

        if (mb_strlen($data['documentoReferencia']) > 100) {
            $errors['documentoReferencia'] =
                'La referencia documental no puede superar 100 caracteres.';
        }

        if ($data['confirmarBaja'] !== '1') {
            $errors['confirmarBaja'] =
                'Debes confirmar que comprendes que la baja es definitiva.';
        }

        return $errors;
    }

    private function isValidDate(string $date): bool
    {
        if ($date === '') {
            return false;
        }

        $parsed = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $date
        );

        return $parsed !== false
            && $parsed->format('Y-m-d') === $date;
    }

    private function formatDisposalDate(string $date): string
    {
        return $date === date('Y-m-d')
            ? $date . ' ' . date('H:i:s')
            : $date . ' 00:00:00';
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $integer = (int) $value;

        return $integer > 0 ? $integer : null;
    }
}
