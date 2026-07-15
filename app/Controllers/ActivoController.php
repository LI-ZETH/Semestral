<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Permissions;
use App\Core\Session;
use App\Core\ValidationException;
use App\Repositories\ActivoRepository;
use App\Services\ActivoService;
use App\Services\ImageUploadService;
use Throwable;

final class ActivoController extends Controller
{
    public function index(): void
    {
        Auth::requirePermission(Permissions::INVENTARIO_GESTIONAR);

        $productId = $this->getQueryId('producto');
        $filters = [
            'search' => trim(
                (string) ($_GET['buscar'] ?? '')
            ),
            'state' => (int) ($_GET['estado'] ?? 0),
            'active' => (string) ($_GET['activo'] ?? ''),
        ];

        $service = $this->buildService();
        $result = $service->listByProduct(
            $productId,
            $filters
        );

        if ($result === null) {
            $this->renderNotFound();
            return;
        }

        $this->view(
            'inventario/assets/index',
            [
                'title' => 'Administrar copias',
                'product' => $result['product'],
                'assets' => $result['assets'],
                'states' => $service->listStates(),
                'filters' => $filters,
                'success' => flash('success'),
                'error' => flash('error'),
            ]
        );
    }

    public function create(): void
    {
        Auth::requirePermission(Permissions::INVENTARIO_GESTIONAR);

        $productId = $this->getQueryId('producto');
        $service = $this->buildService();
        $states = $service->listStates();

        $this->view(
            'inventario/assets/create',
            [
                'title' => 'Registrar copia',
                'products' => $service->listProducts(),
                'states' => $states,
                'locations' => $service->listLocations(),
                'errors' => flash('errors', []),
                'old' => flash(
                    'old',
                    [
                        'idProducto' => $productId,
                        'idEstadoActivo' =>
                            $this->defaultInventoryStateId($states),
                        'fechaAdquisicion' => date('Y-m-d'),
                        'fechaIngreso' => date('Y-m-d'),
                        'costo' => '0.00',
                        'valorResidual' => '0.00',
                    ]
                ),
            ]
        );
    }

    public function store(): void
    {
        Auth::requirePermission(Permissions::INVENTARIO_GESTIONAR);

        $productId = (int) ($_POST['idProducto'] ?? 0);

        try {
            $this->buildService()->create(
                $_POST,
                $_FILES,
                (int) Auth::id()
            );

            Session::flash(
                'success',
                'La copia fue registrada correctamente con sus imágenes.'
            );

            $this->redirectToAssets($productId);
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            Session::flash('old', $this->safeOldInput($_POST));

            header(
                'Location: '
                . base_url(
                    'inventario/activos/crear?producto='
                    . $productId
                )
            );

            exit;
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        'No fue posible registrar la copia. '
                        . 'Revisa los datos y vuelve a intentarlo.',
                ]
            );
            Session::flash('old', $this->safeOldInput($_POST));

            header(
                'Location: '
                . base_url(
                    'inventario/activos/crear?producto='
                    . $productId
                )
            );

            exit;
        }
    }

    public function edit(): void
    {
        Auth::requirePermission(Permissions::INVENTARIO_GESTIONAR);

        $assetId = $this->getQueryId('id');
        $service = $this->buildService();
        $asset = $service->findById($assetId);

        if ($asset === null) {
            $this->renderNotFound();
            return;
        }

        $old = flash('old', []);
        $asset = array_replace($asset, $old);

        $this->view(
            'inventario/assets/edit',
            [
                'title' => 'Editar copia',
                'asset' => $asset,
                'products' => $service->listProducts(),
                'states' => $service->listStates(
                    (int) $asset['idEstadoActivo']
                ),
                'locations' => $service->listLocations(),
                'errors' => flash('errors', []),
            ]
        );
    }

    public function update(): void
    {
        Auth::requirePermission(Permissions::INVENTARIO_GESTIONAR);

        $assetId = filter_input(
            INPUT_POST,
            'idActivo',
            FILTER_VALIDATE_INT
        );
        $productId = (int) ($_POST['idProducto'] ?? 0);

        if (!is_int($assetId) || $assetId <= 0) {
            $this->renderNotFound();
            return;
        }

        try {
            $this->buildService()->update(
                $assetId,
                $_POST,
                $_FILES,
                (int) Auth::id()
            );

            Session::flash(
                'success',
                'La copia fue actualizada correctamente.'
            );

            $this->redirectToAssets($productId);
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            Session::flash('old', $this->safeOldInput($_POST));

            header(
                'Location: '
                . base_url(
                    'inventario/activos/editar?id=' . $assetId
                )
            );

            exit;
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        'No fue posible actualizar la copia. '
                        . 'Revisa los datos y vuelve a intentarlo.',
                ]
            );
            Session::flash('old', $this->safeOldInput($_POST));

            header(
                'Location: '
                . base_url(
                    'inventario/activos/editar?id=' . $assetId
                )
            );

            exit;
        }
    }

    public function changeState(): void
    {
        Auth::requirePermission(Permissions::INVENTARIO_GESTIONAR);

        $assetId = filter_input(
            INPUT_POST,
            'idActivo',
            FILTER_VALIDATE_INT
        );
        $productId = filter_input(
            INPUT_POST,
            'idProducto',
            FILTER_VALIDATE_INT
        );
        $activeValue = filter_input(
            INPUT_POST,
            'activo',
            FILTER_VALIDATE_INT
        );

        if (
            !is_int($assetId)
            || $assetId <= 0
            || !is_int($productId)
            || $productId <= 0
            || !is_int($activeValue)
            || !in_array($activeValue, [0, 1], true)
        ) {
            Session::flash(
                'error',
                'No fue posible cambiar el estado de la copia.'
            );

            $this->redirectToAssets(
                is_int($productId) ? $productId : 0
            );
        }

        try {
            $active = $activeValue === 1;

            $this->buildService()->changeActiveState(
                $assetId,
                $active,
                (int) Auth::id()
            );

            Session::flash(
                'success',
                $active
                    ? 'La copia fue activada.'
                    : 'La copia fue desactivada.'
            );
        } catch (ValidationException $exception) {
            Session::flash(
                'error',
                $exception->getErrors()['general']
                    ?? 'No fue posible cambiar el estado de la copia.'
            );
        } catch (Throwable $exception) {
            Session::flash(
                'error',
                'No fue posible cambiar el estado de la copia.'
            );
        }

        $this->redirectToAssets($productId);
    }

    private function buildService(): ActivoService
    {
        return new ActivoService(
            new ActivoRepository(),
            new ImageUploadService()
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

    private function safeOldInput(array $input): array
    {
        return [
            'idProducto' => (int) ($input['idProducto'] ?? 0),
            'codigoActivo' => trim(
                (string) ($input['codigoActivo'] ?? '')
            ),
            'numeroSerie' => trim(
                (string) ($input['numeroSerie'] ?? '')
            ),
            'direccionIP' => trim(
                (string) ($input['direccionIP'] ?? '')
            ),
            'costo' => trim((string) ($input['costo'] ?? '0')),
            'fechaAdquisicion' => trim(
                (string) ($input['fechaAdquisicion'] ?? '')
            ),
            'fechaIngreso' => trim(
                (string) ($input['fechaIngreso'] ?? '')
            ),
            'vidaUtilMeses' => trim(
                (string) ($input['vidaUtilMeses'] ?? '')
            ),
            'valorResidual' => trim(
                (string) ($input['valorResidual'] ?? '0')
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
            'imagenPrincipalId' => (int) (
                $input['imagenPrincipalId'] ?? 0
            ),
        ];
    }

    private function defaultInventoryStateId(array $states): int
    {
        foreach ($states as $state) {
            if ($state['codigoEstado'] === 'EN_INVENTARIO') {
                return (int) $state['idEstadoActivo'];
            }
        }

        return isset($states[0]['idEstadoActivo'])
            ? (int) $states[0]['idEstadoActivo']
            : 0;
    }

    private function redirectToAssets(int $productId): never
    {
        header(
            'Location: '
            . base_url(
                'inventario/activos?producto=' . $productId
            )
        );

        exit;
    }

    private function renderNotFound(): void
    {
        http_response_code(404);

        $this->view(
            'errors/404',
            [
                'title' => 'Copia no encontrada',
                'path' => '/inventario/activos',
            ]
        );
    }
}
