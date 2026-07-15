<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\ValidationException;
use App\Interfaces\AsignacionRepositoryInterface;
use Throwable;

final class AsignacionService
{
    private const RETURN_CONDITIONS = [
        'BUENO',
        'DANADO',
        'INCOMPLETO',
        'NO_VERIFICADO',
    ];

    private const RETURN_STATE_CODES = [
        'EN_INVENTARIO',
        'REVISION_TECNICA',
        'EN_REPARACION',
    ];

    public function __construct(
        private readonly AsignacionRepositoryInterface $repository
    ) {
    }

    public function listAll(array $filters = []): array
    {
        return $this->repository->listAll($filters);
    }

    public function listAvailableAssets(): array
    {
        return $this->repository->listAvailableAssets();
    }

    public function listCollaborators(): array
    {
        return $this->repository->listActiveCollaborators();
    }

    public function listLocations(): array
    {
        return $this->repository->listActiveLocations();
    }

    public function listReturnReasons(): array
    {
        return $this->repository->listReturnReasons();
    }

    public function listReturnStates(): array
    {
        return $this->repository->listReturnStates();
    }

    public function findActiveAssignment(
        int $assignmentId
    ): ?array {
        return $this->repository->findActiveAssignment(
            $assignmentId
        );
    }

    public function myEquipment(int $userId): array
    {
        return $this->repository->listMyActiveAssignments(
            $userId
        );
    }

    public function create(
        array $input,
        int $administratorUserId
    ): int {
        $data = $this->normalizeAssignment($input);
        $errors = $this->validateAssignmentInput($data);

        $asset = $this->repository->findAssetForAssignment(
            $data['idActivo']
        );
        $collaborator = $this->repository->findCollaborator(
            $data['idColaborador']
        );
        $location = $this->repository->findLocation(
            $data['idUbicacion']
        );
        $assignedState = $this->repository->findStateByCode(
            'ASIGNADO'
        );

        if (
            $asset === null
            || !(bool) ($asset['activo'] ?? false)
            || !(bool) ($asset['productoActivo'] ?? false)
            || !(bool) ($asset['subcategoriaActiva'] ?? false)
            || !(bool) ($asset['categoriaActiva'] ?? false)
            || !(bool) ($asset['estadoActivo'] ?? false)
            || !(bool) ($asset['permiteAsignacion'] ?? false)
        ) {
            $errors['idActivo'] =
                'Selecciona una copia activa y disponible.';
        }

        if (
            $collaborator === null
            || !(bool) ($collaborator['activo'] ?? false)
            || !(bool) ($collaborator['usuarioActivo'] ?? false)
        ) {
            $errors['idColaborador'] =
                'Selecciona un colaborador activo.';
        }

        if (
            $location === null
            || !(bool) ($location['activo'] ?? false)
        ) {
            $errors['idUbicacion'] =
                'Selecciona una ubicación activa.';
        }

        if (
            $assignedState === null
            || !(bool) ($assignedState['activo'] ?? false)
        ) {
            $errors['general'] =
                'No existe un estado ASIGNADO activo en la base de datos.';
        }

        if (
            $asset !== null
            && $this->repository->hasActiveAssignment(
                (int) $asset['idActivo']
            )
        ) {
            $errors['idActivo'] =
                'La copia seleccionada ya tiene una asignación activa.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        try {
            $this->repository->beginTransaction();

            $lockedAsset = $this->repository
                ->findAssetForAssignment(
                    $data['idActivo'],
                    true
                );

            if (
                $lockedAsset === null
                || !(bool) $lockedAsset['activo']
                || !(bool) $lockedAsset['permiteAsignacion']
                || $this->repository->hasActiveAssignment(
                    $data['idActivo']
                )
            ) {
                throw new ValidationException([
                    'idActivo' =>
                        'La copia dejó de estar disponible para asignación.',
                ]);
            }

            $assignmentId = $this->repository
                ->createAssignment([
                    'idActivo' => $data['idActivo'],
                    'idColaborador' => $data['idColaborador'],
                    'usuarioEntrega' => $administratorUserId,
                    'observacionesEntrega' =>
                        $data['observacionesEntrega'],
                ]);

            $this->repository->updateAssetStateAndLocation(
                $data['idActivo'],
                (int) $assignedState['idEstadoActivo'],
                $data['idUbicacion']
            );

            if ($data['actualizarUbicacionColaborador']) {
                $this->repository->setCollaboratorCurrentLocation(
                    $data['idColaborador'],
                    $data['idUbicacion'],
                    'Ubicación actualizada durante la asignación '
                    . 'del activo '
                    . $lockedAsset['codigoActivo']
                    . '.'
                );
            }

            $this->repository->insertMovement([
                'idActivo' => $data['idActivo'],
                'idUsuario' => $administratorUserId,
                'tipoMovimiento' => 'ASIGNACION',
                'idEstadoAnterior' =>
                    (int) $lockedAsset['idEstadoActivo'],
                'idEstadoNuevo' =>
                    (int) $assignedState['idEstadoActivo'],
                'idUbicacionAnterior' =>
                    $this->nullableInt($lockedAsset['idUbicacion']),
                'idUbicacionNueva' => $data['idUbicacion'],
                'descripcion' =>
                    'Asignación del activo '
                    . $lockedAsset['codigoActivo']
                    . ' al colaborador '
                    . $collaborator['nombre']
                    . ' '
                    . $collaborator['apellido']
                    . '.',
            ]);

            $this->repository->commit();

            return $assignmentId;
        } catch (Throwable $exception) {
            $this->repository->rollBack();
            throw $exception;
        }
    }

    public function returnAsset(
        int $assignmentId,
        array $input,
        int $administratorUserId
    ): void {
        $data = $this->normalizeReturn($input);
        $errors = $this->validateReturnInput($data);

        $assignment = $this->repository->findActiveAssignment(
            $assignmentId
        );
        $reason = $this->repository->findReturnReason(
            $data['idMotivoDevolucion']
        );
        $location = $this->repository->findLocation(
            $data['idUbicacion']
        );
        $nextState = $this->repository->findStateById(
            $data['idEstadoActivo']
        );

        if ($assignment === null) {
            $errors['general'] =
                'La asignación ya no está activa o no existe.';
        }

        if (
            $reason === null
            || !(bool) ($reason['activo'] ?? false)
        ) {
            $errors['idMotivoDevolucion'] =
                'Selecciona un motivo de devolución válido.';
        }

        if (
            $location === null
            || !(bool) ($location['activo'] ?? false)
        ) {
            $errors['idUbicacion'] =
                'Selecciona una ubicación activa para recibir el equipo.';
        }

        if (
            $nextState === null
            || !(bool) ($nextState['activo'] ?? false)
            || !in_array(
                (string) ($nextState['codigoEstado'] ?? ''),
                self::RETURN_STATE_CODES,
                true
            )
        ) {
            $errors['idEstadoActivo'] =
                'Selecciona un estado válido para el activo devuelto.';
        }

        if (
            in_array(
                $data['condicionRecepcion'],
                ['DANADO', 'INCOMPLETO'],
                true
            )
            && ($nextState['codigoEstado'] ?? '')
                === 'EN_INVENTARIO'
        ) {
            $errors['idEstadoActivo'] =
                'Un equipo dañado o incompleto debe enviarse a revisión '
                . 'técnica o reparación.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        try {
            $this->repository->beginTransaction();

            $lockedAssignment = $this->repository
                ->findActiveAssignment(
                    $assignmentId,
                    true
                );

            if ($lockedAssignment === null) {
                throw new ValidationException([
                    'general' =>
                        'La asignación ya fue procesada por otro usuario.',
                ]);
            }

            $this->repository->createReturn([
                'idAsignacion' => $assignmentId,
                'usuarioRecibe' => $administratorUserId,
                'idMotivoDevolucion' =>
                    $data['idMotivoDevolucion'],
                'condicionRecepcion' =>
                    $data['condicionRecepcion'],
                'observaciones' => $data['observaciones'],
            ]);

            $this->repository->completeAssignment(
                $assignmentId
            );

            $this->repository->updateAssetStateAndLocation(
                (int) $lockedAssignment['idActivo'],
                $data['idEstadoActivo'],
                $data['idUbicacion']
            );

            $this->repository->insertMovement([
                'idActivo' =>
                    (int) $lockedAssignment['idActivo'],
                'idUsuario' => $administratorUserId,
                'tipoMovimiento' => 'DEVOLUCION',
                'idEstadoAnterior' =>
                    (int) $lockedAssignment['idEstadoActivo'],
                'idEstadoNuevo' =>
                    $data['idEstadoActivo'],
                'idUbicacionAnterior' =>
                    $this->nullableInt(
                        $lockedAssignment['idUbicacion']
                    ),
                'idUbicacionNueva' => $data['idUbicacion'],
                'descripcion' =>
                    'Devolución del activo '
                    . $lockedAssignment['codigoActivo']
                    . ' por '
                    . $lockedAssignment['nombreColaborador']
                    . ' '
                    . $lockedAssignment['apellidoColaborador']
                    . '. Motivo: '
                    . $reason['nombreMotivo']
                    . '.',
            ]);

            $this->repository->commit();
        } catch (Throwable $exception) {
            $this->repository->rollBack();
            throw $exception;
        }
    }

    public function returnConditions(): array
    {
        return self::RETURN_CONDITIONS;
    }

    private function normalizeAssignment(array $input): array
    {
        return [
            'idActivo' => (int) ($input['idActivo'] ?? 0),
            'idColaborador' => (int) (
                $input['idColaborador'] ?? 0
            ),
            'idUbicacion' => (int) (
                $input['idUbicacion'] ?? 0
            ),
            'actualizarUbicacionColaborador' =>
                (string) (
                    $input['actualizarUbicacionColaborador']
                    ?? '0'
                ) === '1',
            'observacionesEntrega' => $this->nullableText(
                $input['observacionesEntrega'] ?? null
            ),
        ];
    }

    private function normalizeReturn(array $input): array
    {
        return [
            'idMotivoDevolucion' => (int) (
                $input['idMotivoDevolucion'] ?? 0
            ),
            'condicionRecepcion' => strtoupper(trim(
                (string) (
                    $input['condicionRecepcion']
                    ?? ''
                )
            )),
            'idEstadoActivo' => (int) (
                $input['idEstadoActivo'] ?? 0
            ),
            'idUbicacion' => (int) (
                $input['idUbicacion'] ?? 0
            ),
            'observaciones' => $this->nullableText(
                $input['observaciones'] ?? null
            ),
        ];
    }

    private function validateAssignmentInput(array $data): array
    {
        $errors = [];

        if ($data['idActivo'] <= 0) {
            $errors['idActivo'] =
                'Selecciona una copia disponible.';
        }

        if ($data['idColaborador'] <= 0) {
            $errors['idColaborador'] =
                'Selecciona un colaborador.';
        }

        if ($data['idUbicacion'] <= 0) {
            $errors['idUbicacion'] =
                'Selecciona la ubicación de entrega.';
        }

        if (
            $data['observacionesEntrega'] !== null
            && mb_strlen($data['observacionesEntrega']) > 2000
        ) {
            $errors['observacionesEntrega'] =
                'Las observaciones no pueden superar 2000 caracteres.';
        }

        return $errors;
    }

    private function validateReturnInput(array $data): array
    {
        $errors = [];

        if ($data['idMotivoDevolucion'] <= 0) {
            $errors['idMotivoDevolucion'] =
                'Selecciona el motivo de devolución.';
        }

        if (
            !in_array(
                $data['condicionRecepcion'],
                self::RETURN_CONDITIONS,
                true
            )
        ) {
            $errors['condicionRecepcion'] =
                'Selecciona la condición de recepción.';
        }

        if ($data['idEstadoActivo'] <= 0) {
            $errors['idEstadoActivo'] =
                'Selecciona el estado posterior a la devolución.';
        }

        if ($data['idUbicacion'] <= 0) {
            $errors['idUbicacion'] =
                'Selecciona la ubicación donde se recibe el equipo.';
        }

        if (
            $data['observaciones'] !== null
            && mb_strlen($data['observaciones']) > 2000
        ) {
            $errors['observaciones'] =
                'Las observaciones no pueden superar 2000 caracteres.';
        }

        return $errors;
    }

    private function nullableText(mixed $value): ?string
    {
        $normalized = trim((string) ($value ?? ''));

        return $normalized === '' ? null : $normalized;
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = (int) $value;

        return $normalized > 0 ? $normalized : null;
    }
}
