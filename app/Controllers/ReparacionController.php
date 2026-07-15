<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Permissions;
use App\Core\Roles;
use App\Core\Session;
use App\Core\ValidationException;
use App\Repositories\ReparacionRepository;
use App\Services\ReparacionService;
use Throwable;

final class ReparacionController extends Controller
{
    public function index(): void
    {
        Auth::requirePermission(
            Permissions::REPARACIONES_VER
        );

        $filters = [
            'search' => trim((string) ($_GET['buscar'] ?? '')),
            'status' => trim((string) ($_GET['estado'] ?? '')),
        ];
        $administrator = Auth::hasRole(
            Roles::ADMINISTRADOR
        );

        $this->view(
            'reparaciones/index',
            [
                'title' => 'Reparaciones',
                'tasks' => $this->buildService()->listTasks(
                    (int) Auth::id(),
                    $administrator,
                    $filters
                ),
                'filters' => $filters,
                'administrator' => $administrator,
                'success' => flash('success'),
                'error' => flash('error'),
            ]
        );
    }

    public function manage(): void
    {
        Auth::requirePermission(
            Permissions::REPARACIONES_GESTIONAR
        );

        $requestId = $this->getQueryId('id');
        $administrator = Auth::hasRole(
            Roles::ADMINISTRADOR
        );
        $service = $this->buildService();
        $task = $service->findTask(
            $requestId,
            (int) Auth::id(),
            $administrator
        );

        if ($task === null) {
            $this->renderNotFound();
            return;
        }

        $this->view(
            'reparaciones/manage',
            [
                'title' => 'Gestionar reparación',
                'task' => array_replace(
                    $task,
                    flash('old', [])
                ),
                'states' => $service->workStates(),
                'errors' => flash('errors', []),
            ]
        );
    }

    public function update(): void
    {
        Auth::requirePermission(
            Permissions::REPARACIONES_GESTIONAR
        );

        $requestId = filter_input(
            INPUT_POST,
            'idSolicitudReparacion',
            FILTER_VALIDATE_INT
        );

        if (!is_int($requestId) || $requestId <= 0) {
            $this->renderNotFound();
            return;
        }

        try {
            $this->buildService()->updateTask(
                $requestId,
                $_POST,
                (int) Auth::id(),
                Auth::hasRole(Roles::ADMINISTRADOR)
            );

            Session::flash(
                'success',
                'La reparación fue actualizada correctamente.'
            );

            $this->redirectToRepairs();
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            Session::flash('old', $this->safeInput($_POST));
            header(
                'Location: '
                . base_url(
                    'reparaciones/gestionar?id=' . $requestId
                )
            );
            exit;
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        'No fue posible actualizar la reparación.',
                ]
            );
            Session::flash('old', $this->safeInput($_POST));
            header(
                'Location: '
                . base_url(
                    'reparaciones/gestionar?id=' . $requestId
                )
            );
            exit;
        }
    }

    private function buildService(): ReparacionService
    {
        return new ReparacionService(
            new ReparacionRepository()
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

    private function safeInput(array $input): array
    {
        return [
            'idEstadoReparacion' =>
                (int) ($input['idEstadoReparacion'] ?? 0),
            'diagnostico' => trim(
                (string) ($input['diagnostico'] ?? '')
            ),
            'trabajoRealizado' => trim(
                (string) ($input['trabajoRealizado'] ?? '')
            ),
            'costoReparacion' => trim(
                (string) ($input['costoReparacion'] ?? '0')
            ),
            'observaciones' => trim(
                (string) ($input['observaciones'] ?? '')
            ),
        ];
    }

    private function redirectToRepairs(): never
    {
        header('Location: ' . base_url('reparaciones'));
        exit;
    }

    private function renderNotFound(): void
    {
        http_response_code(404);
        $this->view(
            'errors/404',
            [
                'title' => 'Reparación no encontrada',
                'path' => '/reparaciones',
            ]
        );
    }
}
