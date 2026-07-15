<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Permissions;
use App\Core\Roles;
use App\Core\Session;
use App\Core\ValidationException;
use App\Repositories\SolicitudRepository;
use App\Services\SolicitudService;
use Throwable;

final class SolicitudController extends Controller
{
    public function index(): void
    {
        Auth::requirePermission(
            Permissions::SOLICITUDES_VER_PROPIAS
        );

        $requests = $this->buildService()->myRequests(
            (int) Auth::id()
        );

        $this->view(
            'solicitudes/index',
            [
                'title' => 'Mis solicitudes',
                'needs' => $requests['needs'],
                'repairs' => $requests['repairs'],
                'success' => flash('success'),
                'error' => flash('error'),
            ]
        );
    }

    public function create(): void
    {
        Auth::requirePermission(
            Permissions::SOLICITUDES_CREAR
        );

        $catalogs = $this->buildService()->formCatalogs(
            (int) Auth::id()
        );

        $this->view(
            'solicitudes/create',
            [
                'title' => 'Nueva solicitud',
                'subcategories' => $catalogs['subcategories'],
                'products' => $catalogs['products'],
                'errors' => flash('errors', []),
                'old' => flash(
                    'old',
                    [
                        'tipoSolicitud' => 'EQUIPO',
                        'cantidad' => 1,
                        'prioridad' => 'MEDIA',
                        'periodoNecesidad' => 'INMEDIATA',
                    ]
                ),
            ]
        );
    }

    public function store(): void
    {
        Auth::requirePermission(
            Permissions::SOLICITUDES_CREAR
        );

        try {
            $this->buildService()->createNeed(
                $_POST,
                (int) Auth::id()
            );

            Session::flash(
                'success',
                'La solicitud fue registrada correctamente.'
            );

            $this->redirectToMyRequests();
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            Session::flash('old', $this->safeNeedInput($_POST));
            $this->redirectTo('solicitudes/crear');
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        'No fue posible registrar la solicitud.',
                ]
            );
            Session::flash('old', $this->safeNeedInput($_POST));
            $this->redirectTo('solicitudes/crear');
        }
    }

    public function repairCreate(): void
    {
        Auth::requirePermission(
            Permissions::SOLICITUDES_CREAR
        );

        $catalogs = $this->buildService()->formCatalogs(
            (int) Auth::id()
        );
        $preselectedAsset = $this->getQueryId('activo');

        $this->view(
            'solicitudes/repair_create',
            [
                'title' => 'Reportar reparación',
                'assets' => $catalogs['assets'],
                'errors' => flash('errors', []),
                'old' => flash(
                    'old',
                    [
                        'idActivo' => $preselectedAsset,
                        'prioridad' => 'MEDIA',
                    ]
                ),
            ]
        );
    }

    public function repairStore(): void
    {
        Auth::requirePermission(
            Permissions::SOLICITUDES_CREAR
        );

        try {
            $this->buildService()->createRepairRequest(
                $_POST,
                (int) Auth::id()
            );

            Session::flash(
                'success',
                'El reporte de reparación fue registrado correctamente.'
            );

            $this->redirectToMyRequests();
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            Session::flash('old', $this->safeRepairInput($_POST));
            $this->redirectTo('solicitudes/reparacion/crear');
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        'No fue posible registrar el reporte de reparación.',
                ]
            );
            Session::flash('old', $this->safeRepairInput($_POST));
            $this->redirectTo('solicitudes/reparacion/crear');
        }
    }

    public function cancelNeed(): void
    {
        Auth::requirePermission(
            Permissions::SOLICITUDES_VER_PROPIAS
        );

        $requestId = filter_input(
            INPUT_POST,
            'idSolicitud',
            FILTER_VALIDATE_INT
        );

        try {
            $this->buildService()->cancelNeed(
                is_int($requestId) ? $requestId : 0,
                (int) Auth::id()
            );

            Session::flash(
                'success',
                'La solicitud fue cancelada.'
            );
        } catch (ValidationException $exception) {
            Session::flash(
                'error',
                $exception->getErrors()['general']
                    ?? 'No fue posible cancelar la solicitud.'
            );
        }

        $this->redirectToMyRequests();
    }

    public function cancelRepair(): void
    {
        Auth::requirePermission(
            Permissions::SOLICITUDES_VER_PROPIAS
        );

        $requestId = filter_input(
            INPUT_POST,
            'idSolicitudReparacion',
            FILTER_VALIDATE_INT
        );

        try {
            $this->buildService()->cancelRepairRequest(
                is_int($requestId) ? $requestId : 0,
                (int) Auth::id()
            );

            Session::flash(
                'success',
                'El reporte de reparación fue cancelado.'
            );
        } catch (ValidationException $exception) {
            Session::flash(
                'error',
                $exception->getErrors()['general']
                    ?? 'No fue posible cancelar el reporte.'
            );
        }

        $this->redirectToMyRequests();
    }

    public function administration(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        $filters = [
            'search' => trim((string) ($_GET['buscar'] ?? '')),
            'status' => trim((string) ($_GET['estado'] ?? '')),
            'repairSearch' => trim(
                (string) ($_GET['buscarReparacion'] ?? '')
            ),
            'repairStatus' => trim(
                (string) ($_GET['estadoReparacion'] ?? '')
            ),
        ];
        $data = $this->buildService()
            ->administrationData($filters);

        $this->view(
            'solicitudes/admin',
            [
                'title' => 'Administrar solicitudes',
                'needs' => $data['needs'],
                'repairs' => $data['repairs'],
                'filters' => $filters,
                'success' => flash('success'),
                'error' => flash('error'),
            ]
        );
    }

    public function review(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        $requestId = $this->getQueryId('id');
        $service = $this->buildService();
        $request = $service->findNeedRequest($requestId);

        if ($request === null) {
            $this->renderNotFound('Solicitud no encontrada');
            return;
        }

        $this->view(
            'solicitudes/review',
            [
                'title' => 'Revisar solicitud',
                'request' => array_replace(
                    $request,
                    flash('old', [])
                ),
                'states' => $service->needReviewStates(),
                'errors' => flash('errors', []),
            ]
        );
    }

    public function updateReview(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        $requestId = filter_input(
            INPUT_POST,
            'idSolicitud',
            FILTER_VALIDATE_INT
        );

        if (!is_int($requestId) || $requestId <= 0) {
            $this->renderNotFound('Solicitud no encontrada');
            return;
        }

        try {
            $this->buildService()->reviewNeed(
                $requestId,
                $_POST,
                (int) Auth::id()
            );

            Session::flash(
                'success',
                'La solicitud fue revisada correctamente.'
            );

            $this->redirectToAdministration();
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            Session::flash('old', $this->safeReviewInput($_POST));
            $this->redirectTo(
                'solicitudes/revisar?id=' . $requestId
            );
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        'No fue posible revisar la solicitud.',
                ]
            );
            Session::flash('old', $this->safeReviewInput($_POST));
            $this->redirectTo(
                'solicitudes/revisar?id=' . $requestId
            );
        }
    }

    public function assignRepair(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        $requestId = $this->getQueryId('id');
        $service = $this->buildService();
        $request = $service->findRepairForAssignment($requestId);

        if ($request === null) {
            $this->renderNotFound('Reporte no encontrado');
            return;
        }

        $this->view(
            'solicitudes/assign_repair',
            [
                'title' => 'Asignar reparación',
                'request' => $request,
                'technicians' => $service->technicians(),
                'errors' => flash('errors', []),
                'old' => flash('old', []),
            ]
        );
    }

    public function storeRepairAssignment(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        $requestId = filter_input(
            INPUT_POST,
            'idSolicitudReparacion',
            FILTER_VALIDATE_INT
        );

        if (!is_int($requestId) || $requestId <= 0) {
            $this->renderNotFound('Reporte no encontrado');
            return;
        }

        try {
            $this->buildService()->assignRepair(
                $requestId,
                $_POST,
                (int) Auth::id()
            );

            Session::flash(
                'success',
                'La reparación fue asignada al técnico.'
            );

            $this->redirectToAdministration();
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            Session::flash(
                'old',
                [
                    'idTecnico' =>
                        (int) ($_POST['idTecnico'] ?? 0),
                    'observacionRevision' => trim(
                        (string) (
                            $_POST['observacionRevision']
                            ?? ''
                        )
                    ),
                ]
            );
            $this->redirectTo(
                'solicitudes/reparacion/asignar?id=' . $requestId
            );
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        'No fue posible asignar la reparación.',
                ]
            );
            $this->redirectTo(
                'solicitudes/reparacion/asignar?id=' . $requestId
            );
        }
    }

    private function buildService(): SolicitudService
    {
        return new SolicitudService(
            new SolicitudRepository()
        );
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

    private function safeNeedInput(array $input): array
    {
        return [
            'tipoSolicitud' => trim(
                (string) ($input['tipoSolicitud'] ?? '')
            ),
            'titulo' => trim((string) ($input['titulo'] ?? '')),
            'descripcionNecesidad' => trim(
                (string) ($input['descripcionNecesidad'] ?? '')
            ),
            'justificacion' => trim(
                (string) ($input['justificacion'] ?? '')
            ),
            'cantidad' => (int) ($input['cantidad'] ?? 1),
            'prioridad' => trim(
                (string) ($input['prioridad'] ?? 'MEDIA')
            ),
            'periodoNecesidad' => trim(
                (string) (
                    $input['periodoNecesidad']
                    ?? 'INMEDIATA'
                )
            ),
            'anioPresupuestado' => trim(
                (string) ($input['anioPresupuestado'] ?? '')
            ),
            'idSubcategoria' =>
                (int) ($input['idSubcategoria'] ?? 0),
            'idProducto' =>
                (int) ($input['idProducto'] ?? 0),
        ];
    }

    private function safeRepairInput(array $input): array
    {
        return [
            'idActivo' => (int) ($input['idActivo'] ?? 0),
            'titulo' => trim((string) ($input['titulo'] ?? '')),
            'descripcionFalla' => trim(
                (string) ($input['descripcionFalla'] ?? '')
            ),
            'prioridad' => trim(
                (string) ($input['prioridad'] ?? 'MEDIA')
            ),
        ];
    }

    private function safeReviewInput(array $input): array
    {
        return [
            'idEstadoSolicitud' =>
                (int) ($input['idEstadoSolicitud'] ?? 0),
            'costoEstimado' => trim(
                (string) ($input['costoEstimado'] ?? '')
            ),
            'observacionRevision' => trim(
                (string) ($input['observacionRevision'] ?? '')
            ),
        ];
    }

    private function redirectToMyRequests(): never
    {
        $this->redirectTo('solicitudes');
    }

    private function redirectToAdministration(): never
    {
        $this->redirectTo('solicitudes/administrar');
    }

    private function redirectTo(string $path): never
    {
        header('Location: ' . base_url($path));
        exit;
    }

    private function renderNotFound(string $title): void
    {
        http_response_code(404);
        $this->view(
            'errors/404',
            [
                'title' => $title,
                'path' => '/solicitudes',
            ]
        );
    }
}
