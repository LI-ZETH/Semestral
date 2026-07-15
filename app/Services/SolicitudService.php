<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\ValidationException;
use App\Interfaces\SolicitudRepositoryInterface;
use Throwable;

final class SolicitudService
{
    private const NEED_TYPES = [
        'EQUIPO',
        'SOFTWARE',
        'LICENCIA',
        'OTRA',
    ];

    private const PRIORITIES = [
        'BAJA',
        'MEDIA',
        'ALTA',
        'URGENTE',
    ];

    private const PERIODS = [
        'INMEDIATA',
        'ANUAL',
        'QUINQUENAL',
    ];

    private const REPAIR_REQUEST_STATES = [
        'EN_ESPERA',
        'ASIGNADA',
        'EN_PROCESO',
        'FINALIZADA',
        'RECHAZADA',
        'CANCELADA',
    ];

    public function __construct(
        private readonly SolicitudRepositoryInterface $repository
    ) {
    }

    public function myRequests(int $userId): array
    {
        return [
            'needs' =>
                $this->repository->listMyNeedRequests($userId),
            'repairs' =>
                $this->repository->listMyRepairRequests($userId),
        ];
    }

    public function formCatalogs(int $userId): array
    {
        return [
            'subcategories' =>
                $this->repository->listActiveSubcategories(),
            'products' =>
                $this->repository->listActiveProducts(),
            'assets' =>
                $this->repository->listMyAssignedAssets($userId),
        ];
    }

    public function createNeed(
        array $input,
        int $userId
    ): int {
        $data = $this->normalizeNeed($input);
        $errors = $this->validateNeed($data);

        $collaborator = $this->repository
            ->findCollaboratorByUserId($userId);

        if (
            $collaborator === null
            || !(bool) ($collaborator['activo'] ?? false)
            || !(bool) ($collaborator['usuarioActivo'] ?? false)
        ) {
            $errors['general'] =
                'Tu cuenta no tiene un perfil de colaborador activo.';
        }

        $waitingState = $this->repository
            ->findNeedStateByName('En espera');

        if (
            $waitingState === null
            || !(bool) ($waitingState['activo'] ?? false)
        ) {
            $errors['general'] =
                'No está configurado el estado inicial de solicitudes.';
        }

        $validSubcategoryIds = array_map(
            'intval',
            array_column(
                $this->repository->listActiveSubcategories(),
                'idSubcategoria'
            )
        );

        $products = $this->repository->listActiveProducts();
        $validProductIds = array_map(
            'intval',
            array_column($products, 'idProducto')
        );

        if (
            $data['idSubcategoria'] !== null
            && !in_array(
                $data['idSubcategoria'],
                $validSubcategoryIds,
                true
            )
        ) {
            $errors['idSubcategoria'] =
                'Selecciona una subcategoría activa.';
        }

        if (
            $data['idProducto'] !== null
            && !in_array(
                $data['idProducto'],
                $validProductIds,
                true
            )
        ) {
            $errors['idProducto'] =
                'Selecciona un producto activo.';
        }

        if ($data['idProducto'] !== null) {
            foreach ($products as $product) {
                if (
                    (int) $product['idProducto']
                    === $data['idProducto']
                ) {
                    $productSubcategory =
                        (int) $product['idSubcategoria'];

                    if (
                        $data['idSubcategoria'] !== null
                        && $data['idSubcategoria']
                            !== $productSubcategory
                    ) {
                        $errors['idProducto'] =
                            'El producto no pertenece a la '
                            . 'subcategoría seleccionada.';
                    }

                    if ($data['idSubcategoria'] === null) {
                        $data['idSubcategoria'] =
                            $productSubcategory;
                    }

                    break;
                }
            }
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        return $this->repository->createNeedRequest([
            'idColaborador' =>
                (int) $collaborator['idColaborador'],
            'idSubcategoria' => $data['idSubcategoria'],
            'idProducto' => $data['idProducto'],
            'idEstadoSolicitud' =>
                (int) $waitingState['idEstadoSolicitud'],
            'tipoSolicitud' => $data['tipoSolicitud'],
            'titulo' => $data['titulo'],
            'descripcionNecesidad' =>
                $data['descripcionNecesidad'],
            'justificacion' => $data['justificacion'],
            'cantidad' => $data['cantidad'],
            'prioridad' => $data['prioridad'],
            'periodoNecesidad' => $data['periodoNecesidad'],
            'anioPresupuestado' => $data['anioPresupuestado'],
        ]);
    }

    public function createRepairRequest(
        array $input,
        int $userId
    ): int {
        $data = $this->normalizeRepairRequest($input);
        $errors = $this->validateRepairRequest($data);

        $asset = $this->repository->findAssignedAssetForUser(
            $data['idActivo'],
            $userId
        );

        if (
            $asset === null
            || !(bool) ($asset['activo'] ?? false)
            || !(bool) ($asset['colaboradorActivo'] ?? false)
        ) {
            $errors['idActivo'] =
                'Selecciona un equipo que esté bajo tu custodia.';
        }

        if (
            $asset !== null
            && $this->repository->hasOpenRepairRequest(
                (int) $asset['idActivo']
            )
        ) {
            $errors['idActivo'] =
                'Este equipo ya tiene una solicitud de reparación activa.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $location = $this->repository
            ->findCurrentLocationForCollaborator(
                (int) $asset['idColaborador']
            );

        return $this->repository->createRepairRequest([
            'idActivo' => (int) $asset['idActivo'],
            'idColaborador' =>
                (int) $asset['idColaborador'],
            'idUbicacionSolicitud' =>
                $location !== null
                    ? (int) $location['idUbicacion']
                    : $this->nullableInt($asset['idUbicacion']),
            'titulo' => $data['titulo'],
            'descripcionFalla' => $data['descripcionFalla'],
            'prioridad' => $data['prioridad'],
        ]);
    }

    public function cancelNeed(
        int $requestId,
        int $userId
    ): void {
        if (
            $requestId <= 0
            || !$this->repository->cancelOwnNeedRequest(
                $requestId,
                $userId
            )
        ) {
            throw new ValidationException([
                'general' =>
                    'La solicitud no existe o ya no puede cancelarse.',
            ]);
        }
    }

    public function cancelRepairRequest(
        int $requestId,
        int $userId
    ): void {
        if (
            $requestId <= 0
            || !$this->repository->cancelOwnRepairRequest(
                $requestId,
                $userId
            )
        ) {
            throw new ValidationException([
                'general' =>
                    'El reporte no existe o ya fue procesado.',
            ]);
        }
    }

    public function administrationData(
        array $filters = []
    ): array {
        return [
            'needs' =>
                $this->repository->listAllNeedRequests($filters),
            'repairs' =>
                $this->repository->listAllRepairRequests($filters),
        ];
    }

    public function findNeedRequest(
        int $requestId
    ): ?array {
        return $this->repository->findNeedRequest($requestId);
    }

    public function needReviewStates(): array
    {
        return $this->repository->listNeedReviewStates();
    }

    public function reviewNeed(
        int $requestId,
        array $input,
        int $administratorUserId
    ): void {
        $stateId = (int) ($input['idEstadoSolicitud'] ?? 0);
        $cost = trim((string) ($input['costoEstimado'] ?? ''));
        $observation = trim(
            (string) ($input['observacionRevision'] ?? '')
        );
        $errors = [];

        $validStateIds = array_map(
            'intval',
            array_column(
                $this->repository->listNeedReviewStates(),
                'idEstadoSolicitud'
            )
        );

        if (!in_array($stateId, $validStateIds, true)) {
            $errors['idEstadoSolicitud'] =
                'Selecciona un estado de revisión válido.';
        }

        $normalizedCost = null;

        if ($cost !== '') {
            if (!is_numeric($cost) || (float) $cost < 0) {
                $errors['costoEstimado'] =
                    'El costo estimado debe ser un número positivo.';
            } else {
                $normalizedCost = round((float) $cost, 2);
            }
        }

        if (mb_strlen($observation) > 2000) {
            $errors['observacionRevision'] =
                'La observación no puede superar 2000 caracteres.';
        }

        if ($this->repository->findNeedRequest($requestId) === null) {
            $errors['general'] =
                'La solicitud indicada no existe.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $this->repository->updateNeedReview(
            $requestId,
            [
                'idEstadoSolicitud' => $stateId,
                'costoEstimado' => $normalizedCost,
                'usuarioRevisa' => $administratorUserId,
                'observacionRevision' =>
                    $observation !== '' ? $observation : null,
            ]
        );
    }

    public function technicians(): array
    {
        return $this->repository->listActiveTechnicians();
    }

    public function findRepairForAssignment(
        int $requestId
    ): ?array {
        return $this->repository
            ->findRepairRequestForAssignment($requestId);
    }

    public function assignRepair(
        int $requestId,
        array $input,
        int $administratorUserId
    ): void {
        $technicianId = (int) ($input['idTecnico'] ?? 0);
        $observation = trim(
            (string) ($input['observacionRevision'] ?? '')
        );
        $errors = [];

        $request = $this->repository
            ->findRepairRequestForAssignment($requestId);
        $technician = $this->repository
            ->findTechnician($technicianId);
        $pendingState = $this->repository
            ->findRepairStateByName('Pendiente');
        $reviewState = $this->repository
            ->findAssetStateByCode('REVISION_TECNICA');

        if ($request === null) {
            $errors['general'] =
                'El reporte de reparación no existe.';
        } elseif (
            !in_array(
                (string) $request['estadoSolicitud'],
                ['EN_ESPERA'],
                true
            )
        ) {
            $errors['general'] =
                'Este reporte ya fue asignado o cerrado.';
        }

        if (
            $technician === null
            || !(bool) ($technician['activo'] ?? false)
            || (bool) ($technician['bloqueado'] ?? false)
            || !(bool) ($technician['rolActivo'] ?? false)
        ) {
            $errors['idTecnico'] =
                'Selecciona un técnico activo.';
        }

        if (
            $pendingState === null
            || !(bool) ($pendingState['activo'] ?? false)
        ) {
            $errors['general'] =
                'No existe el estado Pendiente para reparaciones.';
        }

        if (
            $reviewState === null
            || !(bool) ($reviewState['activo'] ?? false)
        ) {
            $errors['general'] =
                'No existe el estado REVISION_TECNICA para activos.';
        }

        if (mb_strlen($observation) > 2000) {
            $errors['observacionRevision'] =
                'La observación no puede superar 2000 caracteres.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        try {
            $this->repository->beginTransaction();

            $lockedRequest = $this->repository
                ->findRepairRequestForAssignment(
                    $requestId,
                    true
                );

            if (
                $lockedRequest === null
                || $lockedRequest['estadoSolicitud']
                    !== 'EN_ESPERA'
            ) {
                throw new ValidationException([
                    'general' =>
                        'El reporte fue procesado por otro usuario.',
                ]);
            }

            $repairId = $this->repository->createRepair([
                'idActivo' => (int) $lockedRequest['idActivo'],
                'idTecnico' => $technicianId,
                'idEstadoReparacion' =>
                    (int) $pendingState['idEstadoReparacion'],
                'descripcionFalla' =>
                    (string) $lockedRequest['descripcionFalla'],
                'observaciones' =>
                    $observation !== '' ? $observation : null,
            ]);

            $this->repository->assignRepairRequest(
                $requestId,
                [
                    'idTecnico' => $technicianId,
                    'idReparacion' => $repairId,
                    'usuarioRevisa' => $administratorUserId,
                    'observacionRevision' =>
                        $observation !== '' ? $observation : null,
                ]
            );

            $this->repository->updateAssetState(
                (int) $lockedRequest['idActivo'],
                (int) $reviewState['idEstadoActivo']
            );

            $this->repository->insertMovement([
                'idActivo' => (int) $lockedRequest['idActivo'],
                'idUsuario' => $administratorUserId,
                'tipoMovimiento' => 'REPARACION',
                'idEstadoAnterior' =>
                    (int) $lockedRequest['idEstadoActivo'],
                'idEstadoNuevo' =>
                    (int) $reviewState['idEstadoActivo'],
                'idUbicacionAnterior' =>
                    $this->nullableInt(
                        $lockedRequest['idUbicacion']
                    ),
                'idUbicacionNueva' =>
                    $this->nullableInt(
                        $lockedRequest['idUbicacion']
                    ),
                'descripcion' =>
                    'Solicitud de reparación asignada al técnico '
                    . $technician['nombre']
                    . ' '
                    . $technician['apellido']
                    . '.',
            ]);

            $this->repository->commit();
        } catch (Throwable $exception) {
            $this->repository->rollBack();
            throw $exception;
        }
    }

    public function repairRequestStates(): array
    {
        return self::REPAIR_REQUEST_STATES;
    }

    private function normalizeNeed(array $input): array
    {
        $subcategoryId = (int) ($input['idSubcategoria'] ?? 0);
        $productId = (int) ($input['idProducto'] ?? 0);
        $year = trim((string) ($input['anioPresupuestado'] ?? ''));

        return [
            'tipoSolicitud' => strtoupper(
                trim((string) ($input['tipoSolicitud'] ?? ''))
            ),
            'titulo' => trim((string) ($input['titulo'] ?? '')),
            'descripcionNecesidad' => trim(
                (string) ($input['descripcionNecesidad'] ?? '')
            ),
            'justificacion' => trim(
                (string) ($input['justificacion'] ?? '')
            ),
            'cantidad' => (int) ($input['cantidad'] ?? 1),
            'prioridad' => strtoupper(
                trim((string) ($input['prioridad'] ?? 'MEDIA'))
            ),
            'periodoNecesidad' => strtoupper(
                trim(
                    (string) (
                        $input['periodoNecesidad']
                        ?? 'INMEDIATA'
                    )
                )
            ),
            'anioPresupuestado' =>
                $year !== '' ? (int) $year : null,
            'idSubcategoria' =>
                $subcategoryId > 0 ? $subcategoryId : null,
            'idProducto' => $productId > 0 ? $productId : null,
        ];
    }

    private function validateNeed(array $data): array
    {
        $errors = [];

        if (!in_array($data['tipoSolicitud'], self::NEED_TYPES, true)) {
            $errors['tipoSolicitud'] =
                'Selecciona un tipo de solicitud válido.';
        }

        $titleLength = mb_strlen($data['titulo']);
        if ($titleLength < 5 || $titleLength > 150) {
            $errors['titulo'] =
                'El título debe contener entre 5 y 150 caracteres.';
        }

        $descriptionLength = mb_strlen(
            $data['descripcionNecesidad']
        );
        if ($descriptionLength < 10 || $descriptionLength > 3000) {
            $errors['descripcionNecesidad'] =
                'La descripción debe contener entre 10 y 3000 caracteres.';
        }

        $justificationLength = mb_strlen($data['justificacion']);
        if ($justificationLength < 10 || $justificationLength > 3000) {
            $errors['justificacion'] =
                'La justificación debe contener entre 10 y 3000 caracteres.';
        }

        if ($data['cantidad'] < 1 || $data['cantidad'] > 1000) {
            $errors['cantidad'] =
                'La cantidad debe estar entre 1 y 1000.';
        }

        if (!in_array($data['prioridad'], self::PRIORITIES, true)) {
            $errors['prioridad'] =
                'Selecciona una prioridad válida.';
        }

        if (!in_array($data['periodoNecesidad'], self::PERIODS, true)) {
            $errors['periodoNecesidad'] =
                'Selecciona un período válido.';
        }

        if (
            $data['anioPresupuestado'] !== null
            && (
                $data['anioPresupuestado'] < (int) date('Y')
                || $data['anioPresupuestado']
                    > (int) date('Y') + 10
            )
        ) {
            $errors['anioPresupuestado'] =
                'El año presupuestado no es válido.';
        }

        return $errors;
    }

    private function normalizeRepairRequest(array $input): array
    {
        return [
            'idActivo' => (int) ($input['idActivo'] ?? 0),
            'titulo' => trim((string) ($input['titulo'] ?? '')),
            'descripcionFalla' => trim(
                (string) ($input['descripcionFalla'] ?? '')
            ),
            'prioridad' => strtoupper(
                trim((string) ($input['prioridad'] ?? 'MEDIA'))
            ),
        ];
    }

    private function validateRepairRequest(array $data): array
    {
        $errors = [];

        if ($data['idActivo'] <= 0) {
            $errors['idActivo'] =
                'Selecciona el equipo que presenta la falla.';
        }

        $titleLength = mb_strlen($data['titulo']);
        if ($titleLength < 5 || $titleLength > 150) {
            $errors['titulo'] =
                'El título debe contener entre 5 y 150 caracteres.';
        }

        $descriptionLength = mb_strlen($data['descripcionFalla']);
        if ($descriptionLength < 10 || $descriptionLength > 3000) {
            $errors['descripcionFalla'] =
                'Describe la falla usando entre 10 y 3000 caracteres.';
        }

        if (!in_array($data['prioridad'], self::PRIORITIES, true)) {
            $errors['prioridad'] =
                'Selecciona una prioridad válida.';
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
