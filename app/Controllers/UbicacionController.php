<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Roles;
use App\Core\Session;
use App\Core\ValidationException;
use App\Repositories\UbicacionRepository;
use App\Services\UbicacionService;
use Throwable;

final class UbicacionController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        $filters = [
            'search' => trim((string) ($_GET['buscar'] ?? '')),
            'type' => trim((string) ($_GET['tipo'] ?? '')),
            'active' => (string) ($_GET['activo'] ?? ''),
        ];

        $service = $this->buildService();

        $this->view(
            'ubicaciones/index',
            [
                'title' => 'Administrar ubicaciones',
                'locations' => $service->listAll($filters),
                'types' => $service->types(),
                'filters' => $filters,
                'success' => flash('success'),
                'error' => flash('error'),
            ]
        );
    }

    public function create(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        $service = $this->buildService();

        $this->view(
            'ubicaciones/create',
            [
                'title' => 'Registrar ubicación',
                'types' => $service->types(),
                'errors' => flash('errors', []),
                'old' => flash(
                    'old',
                    [
                        'tipoUbicacion' => 'OFICINA',
                    ]
                ),
            ]
        );
    }

    public function store(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        try {
            $this->buildService()->create($_POST);

            Session::flash(
                'success',
                'La ubicación fue registrada correctamente.'
            );

            $this->redirectToLocations();
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            Session::flash('old', $this->safeOldInput($_POST));

            header(
                'Location: '
                . base_url('ubicaciones/crear')
            );

            exit;
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        'No fue posible registrar la ubicación.',
                ]
            );
            Session::flash('old', $this->safeOldInput($_POST));

            header(
                'Location: '
                . base_url('ubicaciones/crear')
            );

            exit;
        }
    }

    public function edit(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        $locationId = $this->getQueryId('id');
        $service = $this->buildService();
        $location = $service->findById($locationId);

        if ($location === null) {
            $this->renderNotFound();
            return;
        }

        $this->view(
            'ubicaciones/edit',
            [
                'title' => 'Editar ubicación',
                'location' => array_replace(
                    $location,
                    flash('old', [])
                ),
                'types' => $service->types(),
                'errors' => flash('errors', []),
            ]
        );
    }

    public function update(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        $locationId = filter_input(
            INPUT_POST,
            'idUbicacion',
            FILTER_VALIDATE_INT
        );

        if (!is_int($locationId) || $locationId <= 0) {
            $this->renderNotFound();
            return;
        }

        try {
            $this->buildService()->update(
                $locationId,
                $_POST
            );

            Session::flash(
                'success',
                'La ubicación fue actualizada correctamente.'
            );

            $this->redirectToLocations();
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            Session::flash('old', $this->safeOldInput($_POST));

            header(
                'Location: '
                . base_url(
                    'ubicaciones/editar?id=' . $locationId
                )
            );

            exit;
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        'No fue posible actualizar la ubicación.',
                ]
            );
            Session::flash('old', $this->safeOldInput($_POST));

            header(
                'Location: '
                . base_url(
                    'ubicaciones/editar?id=' . $locationId
                )
            );

            exit;
        }
    }

    public function changeState(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        $locationId = filter_input(
            INPUT_POST,
            'idUbicacion',
            FILTER_VALIDATE_INT
        );
        $activeValue = filter_input(
            INPUT_POST,
            'activo',
            FILTER_VALIDATE_INT
        );

        if (
            !is_int($locationId)
            || $locationId <= 0
            || !is_int($activeValue)
            || !in_array($activeValue, [0, 1], true)
        ) {
            Session::flash(
                'error',
                'No fue posible cambiar el estado de la ubicación.'
            );

            $this->redirectToLocations();
        }

        try {
            $active = $activeValue === 1;

            $this->buildService()->changeActiveState(
                $locationId,
                $active
            );

            Session::flash(
                'success',
                $active
                    ? 'La ubicación fue activada.'
                    : 'La ubicación fue desactivada.'
            );
        } catch (ValidationException $exception) {
            Session::flash(
                'error',
                $exception->getErrors()['general']
                    ?? 'No fue posible cambiar el estado.'
            );
        } catch (Throwable $exception) {
            Session::flash(
                'error',
                'No fue posible cambiar el estado de la ubicación.'
            );
        }

        $this->redirectToLocations();
    }

    private function buildService(): UbicacionService
    {
        return new UbicacionService(
            new UbicacionRepository()
        );
    }

    private function safeOldInput(array $input): array
    {
        return [
            'nombreUbicacion' => trim(
                (string) ($input['nombreUbicacion'] ?? '')
            ),
            'tipoUbicacion' => trim(
                (string) ($input['tipoUbicacion'] ?? '')
            ),
            'edificio' => trim(
                (string) ($input['edificio'] ?? '')
            ),
            'piso' => trim((string) ($input['piso'] ?? '')),
            'oficina' => trim(
                (string) ($input['oficina'] ?? '')
            ),
            'direccion' => trim(
                (string) ($input['direccion'] ?? '')
            ),
            'descripcion' => trim(
                (string) ($input['descripcion'] ?? '')
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

    private function redirectToLocations(): never
    {
        header('Location: ' . base_url('ubicaciones'));
        exit;
    }

    private function renderNotFound(): void
    {
        http_response_code(404);

        $this->view(
            'errors/404',
            [
                'title' => 'Ubicación no encontrada',
                'path' => '/ubicaciones',
            ]
        );
    }
}
