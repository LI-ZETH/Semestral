<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Permissions;
use App\Core\Session;
use App\Core\ValidationException;
use App\Repositories\BajaActivoRepository;
use App\Services\BajaActivoService;
use Throwable;

final class BajaActivoController extends Controller
{
    public function index(): void
    {
        Auth::requirePermission(
            Permissions::INVENTARIO_GESTIONAR
        );

        $filters = [
            'search' => trim(
                (string) ($_GET['buscar'] ?? '')
            ),
            'type' => (int) ($_GET['tipo'] ?? 0),
        ];

        $service = $this->buildService();

        $this->view(
            'bajas/index',
            [
                'title' => 'Bajas de activos',
                'disposals' => $service->listAll($filters),
                'types' => $service->listTypes(),
                'filters' => $filters,
                'success' => flash('success'),
                'error' => flash('error'),
            ]
        );
    }

    public function create(): void
    {
        Auth::requirePermission(
            Permissions::INVENTARIO_GESTIONAR
        );

        $selectedAssetId = filter_input(
            INPUT_GET,
            'activo',
            FILTER_VALIDATE_INT
        );

        $service = $this->buildService();

        $this->view(
            'bajas/create',
            [
                'title' => 'Registrar baja de activo',
                'assets' => $service->listEligibleAssets(),
                'types' => $service->listTypes(),
                'errors' => flash('errors', []),
                'old' => flash(
                    'old',
                    [
                        'idActivo' => is_int($selectedAssetId)
                            ? $selectedAssetId
                            : 0,
                        'fechaBaja' => date('Y-m-d'),
                    ]
                ),
            ]
        );
    }

    public function store(): void
    {
        Auth::requirePermission(
            Permissions::INVENTARIO_GESTIONAR
        );

        $assetId = (int) ($_POST['idActivo'] ?? 0);

        try {
            $disposalId = $this->buildService()->create(
                $_POST,
                (int) Auth::id()
            );

            Session::flash(
                'success',
                'La baja del activo fue registrada correctamente.'
            );

            header(
                'Location: '
                . base_url(
                    'bajas/ver?id=' . $disposalId
                )
            );

            exit;
        } catch (ValidationException $exception) {
            Session::flash(
                'errors',
                $exception->getErrors()
            );
            Session::flash(
                'old',
                $this->safeOldInput($_POST)
            );

            header(
                'Location: '
                . base_url(
                    'bajas/crear?activo=' . $assetId
                )
            );

            exit;
        } catch (Throwable) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        'No fue posible registrar la baja. Revisa los datos e inténtalo nuevamente.',
                ]
            );
            Session::flash(
                'old',
                $this->safeOldInput($_POST)
            );

            header(
                'Location: '
                . base_url(
                    'bajas/crear?activo=' . $assetId
                )
            );

            exit;
        }
    }

    public function show(): void
    {
        Auth::requirePermission(
            Permissions::INVENTARIO_GESTIONAR
        );

        $disposalId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!is_int($disposalId) || $disposalId <= 0) {
            $this->renderNotFound();
            return;
        }

        $disposal = $this->buildService()->findById(
            $disposalId
        );

        if ($disposal === null) {
            $this->renderNotFound();
            return;
        }

        $this->view(
            'bajas/show',
            [
                'title' => 'Detalle de baja',
                'disposal' => $disposal,
                'success' => flash('success'),
            ]
        );
    }

    private function buildService(): BajaActivoService
    {
        return new BajaActivoService(
            new BajaActivoRepository()
        );
    }

    private function safeOldInput(array $input): array
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
        ];
    }

    private function renderNotFound(): void
    {
        http_response_code(404);

        $this->view(
            'errors/404',
            [
                'title' => 'Baja no encontrada',
                'path' => '/bajas',
            ]
        );
    }
}
