<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\ValidationException;
use App\Interfaces\ReparacionRepositoryInterface;
use Throwable;

final class ReparacionService
{
    private const WORK_STATES = [
        'Pendiente',
        'En proceso',
        'Finalizada',
        'No reparable',
    ];

    public function __construct(
        private readonly ReparacionRepositoryInterface $repository
    ) {
    }

    public function listTasks(
        int $userId,
        bool $administrator,
        array $filters = []
    ): array {
        return $this->repository->listTasks(
            $userId,
            $administrator,
            $filters
        );
    }

    public function findTask(
        int $requestId,
        int $userId,
        bool $administrator
    ): ?array {
        return $this->repository->findTask(
            $requestId,
            $userId,
            $administrator
        );
    }

    public function workStates(): array
    {
        return $this->repository->listWorkStates();
    }

    public function updateTask(
        int $requestId,
        array $input,
        int $userId,
        bool $administrator
    ): void {
        $data = $this->normalize($input);
        $errors = $this->validate($data);
        $task = $this->repository->findTask(
            $requestId,
            $userId,
            $administrator
        );
        $repairState = $this->repository
            ->findRepairStateById($data['idEstadoReparacion']);

        if ($task === null) {
            $errors['general'] =
                'La reparación solicitada no existe o no te pertenece.';
        }

        if (
            $repairState === null
            || !(bool) ($repairState['activo'] ?? false)
            || !in_array(
                (string) ($repairState['nombreEstado'] ?? ''),
                self::WORK_STATES,
                true
            )
        ) {
            $errors['idEstadoReparacion'] =
                'Selecciona un estado de reparación válido.';
        }

        if (
            $repairState !== null
            && in_array(
                (string) $repairState['nombreEstado'],
                ['Finalizada', 'No reparable'],
                true
            )
            && $data['diagnostico'] === ''
        ) {
            $errors['diagnostico'] =
                'El diagnóstico es obligatorio para cerrar la reparación.';
        }

        if (
            $repairState !== null
            && $repairState['nombreEstado'] === 'Finalizada'
            && $data['trabajoRealizado'] === ''
        ) {
            $errors['trabajoRealizado'] =
                'Describe el trabajo realizado antes de finalizar.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $repairStateName = (string) $repairState['nombreEstado'];
        $requestState = 'ASIGNADA';
        $assetStateCode = 'REVISION_TECNICA';
        $finishDate = null;

        if ($repairStateName === 'En proceso') {
            $requestState = 'EN_PROCESO';
            $assetStateCode = 'EN_REPARACION';
        } elseif ($repairStateName === 'Finalizada') {
            $requestState = 'FINALIZADA';
            $assetStateCode = $this->repository->hasActiveAssignment(
                (int) $task['idActivo']
            ) ? 'ASIGNADO' : 'EN_INVENTARIO';
            $finishDate = date('Y-m-d H:i:s');
        } elseif ($repairStateName === 'No reparable') {
            $requestState = 'FINALIZADA';
            $assetStateCode = 'REVISION_TECNICA';
            $finishDate = date('Y-m-d H:i:s');
        }

        $assetState = $this->repository
            ->findAssetStateByCode($assetStateCode);

        if (
            $assetState === null
            || !(bool) ($assetState['activo'] ?? false)
        ) {
            throw new ValidationException([
                'general' =>
                    'No existe el estado requerido para el activo.',
            ]);
        }

        try {
            $this->repository->beginTransaction();

            $lockedTask = $this->repository->findTask(
                $requestId,
                $userId,
                $administrator,
                true
            );

            if ($lockedTask === null) {
                throw new ValidationException([
                    'general' =>
                        'La reparación fue modificada por otro usuario.',
                ]);
            }

            $this->repository->updateRepair(
                (int) $lockedTask['idReparacion'],
                [
                    'idEstadoReparacion' =>
                        $data['idEstadoReparacion'],
                    'diagnostico' =>
                        $data['diagnostico'] !== ''
                            ? $data['diagnostico']
                            : null,
                    'trabajoRealizado' =>
                        $data['trabajoRealizado'] !== ''
                            ? $data['trabajoRealizado']
                            : null,
                    'costoReparacion' => $data['costoReparacion'],
                    'fechaFin' => $finishDate,
                    'observaciones' =>
                        $data['observaciones'] !== ''
                            ? $data['observaciones']
                            : null,
                ]
            );

            $this->repository->updateRepairRequest(
                $requestId,
                [
                    'estadoSolicitud' => $requestState,
                    'fechaCierre' => $finishDate,
                ]
            );

            $this->repository->updateAssetState(
                (int) $lockedTask['idActivo'],
                (int) $assetState['idEstadoActivo']
            );

            $this->repository->insertMovement([
                'idActivo' => (int) $lockedTask['idActivo'],
                'idUsuario' => $userId,
                'tipoMovimiento' => 'REPARACION',
                'idEstadoAnterior' =>
                    (int) $lockedTask['idEstadoActivo'],
                'idEstadoNuevo' =>
                    (int) $assetState['idEstadoActivo'],
                'idUbicacionAnterior' =>
                    $this->nullableInt($lockedTask['idUbicacion']),
                'idUbicacionNueva' =>
                    $this->nullableInt($lockedTask['idUbicacion']),
                'descripcion' =>
                    'Reparación actualizada a estado '
                    . $repairStateName
                    . '. ',
            ]);

            $this->repository->commit();
        } catch (Throwable $exception) {
            $this->repository->rollBack();
            throw $exception;
        }
    }

    private function normalize(array $input): array
    {
        $cost = trim((string) ($input['costoReparacion'] ?? '0'));

        return [
            'idEstadoReparacion' =>
                (int) ($input['idEstadoReparacion'] ?? 0),
            'diagnostico' => trim(
                (string) ($input['diagnostico'] ?? '')
            ),
            'trabajoRealizado' => trim(
                (string) ($input['trabajoRealizado'] ?? '')
            ),
            'costoReparacion' =>
                is_numeric($cost) ? round((float) $cost, 2) : -1,
            'observaciones' => trim(
                (string) ($input['observaciones'] ?? '')
            ),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['idEstadoReparacion'] <= 0) {
            $errors['idEstadoReparacion'] =
                'Selecciona el estado de la reparación.';
        }

        if (mb_strlen($data['diagnostico']) > 4000) {
            $errors['diagnostico'] =
                'El diagnóstico no puede superar 4000 caracteres.';
        }

        if (mb_strlen($data['trabajoRealizado']) > 4000) {
            $errors['trabajoRealizado'] =
                'El trabajo realizado no puede superar 4000 caracteres.';
        }

        if ($data['costoReparacion'] < 0) {
            $errors['costoReparacion'] =
                'El costo debe ser un número igual o mayor que cero.';
        }

        if ($data['costoReparacion'] > 9999999999.99) {
            $errors['costoReparacion'] =
                'El costo indicado es demasiado alto.';
        }

        if (mb_strlen($data['observaciones']) > 4000) {
            $errors['observaciones'] =
                'Las observaciones no pueden superar 4000 caracteres.';
        }

        return $errors;
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
