<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Permissions;
use App\Core\Session;
use App\Core\ValidationException;
use App\Repositories\ProductoRepository;
use App\Services\ImageUploadService;
use App\Services\ProductoService;
use Throwable;

final class ProductoController extends Controller
{
    public function index(): void
    {
        Auth::requirePermission(Permissions::INVENTARIO_GESTIONAR);

        $subcategoryId = $this->getQueryId('subcategoria');
        $result = $this->buildService()->listBySubcategory($subcategoryId);

        if ($result === null) {
            $this->renderNotFound();
            return;
        }

        $this->view(
            'inventario/products/index',
            [
                'title' => 'Administrar productos',
                'subcategory' => $result['subcategory'],
                'products' => $result['products'],
                'success' => flash('success'),
                'error' => flash('error'),
            ]
        );
    }

    public function create(): void
    {
        Auth::requirePermission(Permissions::INVENTARIO_GESTIONAR);

        $subcategoryId = $this->getQueryId('subcategoria');
        $service = $this->buildService();

        $this->view(
            'inventario/products/create',
            [
                'title' => 'Registrar producto',
                'subcategories' => $service->listSubcategories(),
                'errors' => flash('errors', []),
                'old' => flash(
                    'old',
                    [
                        'idSubcategoria' => $subcategoryId,
                        'tipoProducto' => 'HARDWARE',
                    ]
                ),
            ]
        );
    }

    public function store(): void
    {
        Auth::requirePermission(Permissions::INVENTARIO_GESTIONAR);

        try {
            $this->buildService()->create($_POST, $_FILES);

            Session::flash(
                'success',
                'El producto fue registrado correctamente.'
            );

            $this->redirectToProducts(
                (int) ($_POST['idSubcategoria'] ?? 0)
            );
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            Session::flash('old', $this->safeOldInput($_POST));

            header(
                'Location: '
                . base_url(
                    'inventario/productos/crear?subcategoria='
                    . (int) ($_POST['idSubcategoria'] ?? 0)
                )
            );

            exit;
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        'No fue posible registrar el producto. '
                        . 'Revisa los datos e inténtalo nuevamente.',
                ]
            );
            Session::flash('old', $this->safeOldInput($_POST));

            header(
                'Location: '
                . base_url(
                    'inventario/productos/crear?subcategoria='
                    . (int) ($_POST['idSubcategoria'] ?? 0)
                )
            );

            exit;
        }
    }

    public function edit(): void
    {
        Auth::requirePermission(Permissions::INVENTARIO_GESTIONAR);

        $productId = $this->getQueryId('id');
        $service = $this->buildService();
        $product = $service->findById($productId);

        if ($product === null) {
            $this->renderNotFound();
            return;
        }

        $old = flash('old', []);

        $this->view(
            'inventario/products/edit',
            [
                'title' => 'Editar producto',
                'product' => array_replace($product, $old),
                'subcategories' => $service->listSubcategories(),
                'errors' => flash('errors', []),
            ]
        );
    }

    public function update(): void
    {
        Auth::requirePermission(Permissions::INVENTARIO_GESTIONAR);

        $productId = filter_input(
            INPUT_POST,
            'idProducto',
            FILTER_VALIDATE_INT
        );

        if (!is_int($productId) || $productId <= 0) {
            $this->renderNotFound();
            return;
        }

        try {
            $this->buildService()->update(
                $productId,
                $_POST,
                $_FILES
            );

            Session::flash(
                'success',
                'El producto fue actualizado correctamente.'
            );

            $this->redirectToProducts(
                (int) ($_POST['idSubcategoria'] ?? 0)
            );
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            Session::flash('old', $this->safeOldInput($_POST));

            header(
                'Location: '
                . base_url(
                    'inventario/productos/editar?id=' . $productId
                )
            );

            exit;
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        'No fue posible actualizar el producto. '
                        . 'Revisa los datos e inténtalo nuevamente.',
                ]
            );
            Session::flash('old', $this->safeOldInput($_POST));

            header(
                'Location: '
                . base_url(
                    'inventario/productos/editar?id=' . $productId
                )
            );

            exit;
        }
    }

    public function changeState(): void
    {
        Auth::requirePermission(Permissions::INVENTARIO_GESTIONAR);

        $productId = filter_input(
            INPUT_POST,
            'idProducto',
            FILTER_VALIDATE_INT
        );
        $subcategoryId = filter_input(
            INPUT_POST,
            'idSubcategoria',
            FILTER_VALIDATE_INT
        );
        $activeValue = filter_input(
            INPUT_POST,
            'activo',
            FILTER_VALIDATE_INT
        );

        if (
            !is_int($productId)
            || $productId <= 0
            || !is_int($subcategoryId)
            || $subcategoryId <= 0
            || !is_int($activeValue)
            || !in_array($activeValue, [0, 1], true)
        ) {
            Session::flash(
                'error',
                'No fue posible cambiar el estado del producto.'
            );

            $this->redirectToProducts(
                is_int($subcategoryId) ? $subcategoryId : 0
            );
        }

        try {
            $active = $activeValue === 1;

            $this->buildService()->changeActiveState(
                $productId,
                $active
            );

            Session::flash(
                'success',
                $active
                    ? 'El producto fue activado.'
                    : 'El producto fue desactivado.'
            );
        } catch (ValidationException $exception) {
            Session::flash(
                'error',
                $exception->getErrors()['general']
                    ?? 'No fue posible cambiar el estado del producto.'
            );
        }

        $this->redirectToProducts($subcategoryId);
    }

    private function buildService(): ProductoService
    {
        return new ProductoService(
            new ProductoRepository(),
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
            'idSubcategoria' => (int) ($input['idSubcategoria'] ?? 0),
            'nombreProducto' => trim(
                (string) ($input['nombreProducto'] ?? '')
            ),
            'marca' => trim((string) ($input['marca'] ?? '')),
            'modelo' => trim((string) ($input['modelo'] ?? '')),
            'descripcion' => trim(
                (string) ($input['descripcion'] ?? '')
            ),
            'tipoProducto' => trim(
                (string) ($input['tipoProducto'] ?? '')
            ),
            'vidaUtilMeses' => trim(
                (string) ($input['vidaUtilMeses'] ?? '')
            ),
        ];
    }

    private function redirectToProducts(int $subcategoryId): never
    {
        header(
            'Location: '
            . base_url(
                'inventario/productos?subcategoria=' . $subcategoryId
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
                'title' => 'Producto no encontrado',
                'path' => '/inventario/productos',
            ]
        );
    }
}
