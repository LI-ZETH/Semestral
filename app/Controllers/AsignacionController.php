<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Permissions;
use App\Core\Roles;
use App\Core\Session;
use App\Core\ValidationException;
use App\Repositories\AsignacionRepository;
use App\Services\AsignacionService;
use Throwable;

final class AsignacionController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        $filters = [
            'search' => trim((string) ($_GET['buscar'] ?? '')),
            'status' => trim((string) ($_GET['estado'] ?? '')),
        ];

        $this->view(
            'asignaciones/index',
            [
                'title' => 'Asignaciones de activos',
                'assignments' =>
                    $this->buildService()->listAll($filters),
                'filters' => $filters,
                'success' => flash('success'),
                'error' => flash('error'),
            ]
        );
    }

    public function create(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        $preselectedAssetId = $this->getQueryId('activo');
        $service = $this->buildService();

        $this->view(
            'asignaciones/create',
            [
                'title' => 'Asignar copia',
                'assets' => $service->listAvailableAssets(),
                'collaborators' => $service->listCollaborators(),
                'locations' => $service->listLocations(),
                'errors' => flash('errors', []),
                'old' => flash(
                    'old',
                    [
                        'idActivo' => $preselectedAssetId,
                        'actualizarUbicacionColaborador' => '1',
                    ]
                ),
            ]
        );
    }

    public function store(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        try {
            $this->buildService()->create(
                $_POST,
                (int) Auth::id()
            );

            Session::flash(
                'success',
                'La copia fue asignada correctamente al colaborador.'
            );

            $this->redirectToAssignments();
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            Session::flash('old', $this->safeAssignmentInput($_POST));

            header(
                'Location: '
                . base_url('asignaciones/crear')
            );

            exit;
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        'No fue posible completar la asignación.',
                ]
            );
            Session::flash('old', $this->safeAssignmentInput($_POST));

            header(
                'Location: '
                . base_url('asignaciones/crear')
            );

            exit;
        }
    }

    public function returnForm(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        $assignmentId = $this->getQueryId('id');
        $service = $this->buildService();
        $assignment = $service->findActiveAssignment(
            $assignmentId
        );

        if ($assignment === null) {
            $this->renderNotFound();
            return;
        }

        $defaultStateId = 0;

        foreach ($service->listReturnStates() as $state) {
            if ($state['codigoEstado'] === 'EN_INVENTARIO') {
                $defaultStateId = (int) $state['idEstadoActivo'];
                break;
            }
        }

        $this->view(
            'asignaciones/return',
            [
                'title' => 'Registrar devolución',
                'assignment' => $assignment,
                'reasons' => $service->listReturnReasons(),
                'conditions' => $service->returnConditions(),
                'states' => $service->listReturnStates(),
                'locations' => $service->listLocations(),
                'errors' => flash('errors', []),
                'old' => flash(
                    'old',
                    [
                        'condicionRecepcion' => 'BUENO',
                        'idEstadoActivo' => $defaultStateId,
                        'idUbicacion' =>
                            (int) ($assignment['idUbicacion'] ?? 0),
                    ]
                ),
            ]
        );
    }

    public function storeReturn(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        $assignmentId = filter_input(
            INPUT_POST,
            'idAsignacion',
            FILTER_VALIDATE_INT
        );

        if (!is_int($assignmentId) || $assignmentId <= 0) {
            $this->renderNotFound();
            return;
        }

        try {
            $this->buildService()->returnAsset(
                $assignmentId,
                $_POST,
                (int) Auth::id()
            );

            Session::flash(
                'success',
                'La devolución fue registrada correctamente.'
            );

            $this->redirectToAssignments();
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            Session::flash('old', $this->safeReturnInput($_POST));

            header(
                'Location: '
                . base_url(
                    'asignaciones/devolver?id=' . $assignmentId
                )
            );

            exit;
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        'No fue posible registrar la devolución.',
                ]
            );
            Session::flash('old', $this->safeReturnInput($_POST));

            header(
                'Location: '
                . base_url(
                    'asignaciones/devolver?id=' . $assignmentId
                )
            );

            exit;
        }
    }

    public function myEquipment(): void
    {
        Auth::requirePermission(
            Permissions::INVENTARIO_VER_PROPIO
        );

        $this->view(
            'asignaciones/my_equipment',
            [
                'title' => 'Mis equipos',
                'assignments' =>
                    $this->buildService()->myEquipment(
                        (int) Auth::id()
                    ),
            ]
        );
    }

    private function buildService(): AsignacionService
    {
        return new AsignacionService(
            new AsignacionRepository()
        );
    }

    private function safeAssignmentInput(array $input): array
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
                isset($input['actualizarUbicacionColaborador'])
                    ? '1'
                    : '0',
            'observacionesEntrega' => trim(
                (string) (
                    $input['observacionesEntrega'] ?? ''
                )
            ),
        ];
    }

    private function safeReturnInput(array $input): array
    {
        return [
            'idMotivoDevolucion' => (int) (
                $input['idMotivoDevolucion'] ?? 0
            ),
            'condicionRecepcion' => trim(
                (string) (
                    $input['condicionRecepcion'] ?? ''
                )
            ),
            'idEstadoActivo' => (int) (
                $input['idEstadoActivo'] ?? 0
            ),
            'idUbicacion' => (int) (
                $input['idUbicacion'] ?? 0
            ),
            'observaciones' => trim(
                (string) ($input['observaciones'] ?? '')
            ),
        ];
    }

    private function getQueryId(string $field): int
    {
        $value = filter_input(
            INPUT_GET,
            $field,
            FILTER_VALIDATE_INT
        );

        return is_int($value) ? $value : 0;
    }

    private function redirectToAssignments(): never
    {
        header('Location: ' . base_url('asignaciones'));
        exit;
    }

    private function renderNotFound(): void
    {
        http_response_code(404);

        $this->view(
            'errors/404',
            [
                'title' => 'Asignación no encontrada',
                'path' => '/asignaciones',
            ]
        );
    }
}
