<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Permissions;
use App\Core\Session;
use App\Core\ValidationException;
use App\Repositories\SubcategoriaRepository;
use App\Services\ImageUploadService;
use App\Services\SubcategoriaService;

final class SubcategoriaController extends Controller
{
    public function index(): void
    {
        Auth::requirePermission(
            Permissions::INVENTARIO_GESTIONAR
        );

        $categoryId = $this->getQueryId(
            'categoria'
        );

        $result = $this->buildService()
            ->listByCategory($categoryId);

        if ($result === null) {
            $this->renderNotFound();

            return;
        }

        $this->view(
            'inventario/subcategories/index',
            [
                'title' => 'Administrar subcategorías',
                'category' => $result['category'],
                'subcategories' =>
                    $result['subcategories'],
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

        $categoryId = $this->getQueryId(
            'categoria'
        );

        $service = $this->buildService();

        $this->view(
            'inventario/subcategories/create',
            [
                'title' => 'Registrar subcategoría',
                'categories' =>
                    $service->listCategories(),
                'errors' => flash('errors', []),
                'old' => flash(
                    'old',
                    [
                        'idCategoria' => $categoryId,
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

        try {
            $service = $this->buildService();

            $service->create($_POST, $_FILES);

            Session::flash(
                'success',
                'La subcategoría fue registrada correctamente.'
            );

            $this->redirectToSubcategories(
                (int) ($_POST['idCategoria'] ?? 0)
            );
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
                    'inventario/subcategorias/crear?categoria='
                    . (int) (
                        $_POST['idCategoria']
                        ?? 0
                    )
                )
            );

            exit;
        }
    }

    public function edit(): void
    {
        Auth::requirePermission(
            Permissions::INVENTARIO_GESTIONAR
        );

        $subcategoryId = $this->getQueryId('id');

        $service = $this->buildService();

        $subcategory = $service->findById(
            $subcategoryId
        );

        if ($subcategory === null) {
            $this->renderNotFound();

            return;
        }

        $old = flash('old', []);

        $this->view(
            'inventario/subcategories/edit',
            [
                'title' => 'Editar subcategoría',
                'subcategory' => array_replace(
                    $subcategory,
                    $old
                ),
                'categories' =>
                    $service->listCategories(),
                'errors' => flash('errors', []),
            ]
        );
    }

    public function update(): void
    {
        Auth::requirePermission(
            Permissions::INVENTARIO_GESTIONAR
        );

        $subcategoryId = filter_input(
            INPUT_POST,
            'idSubcategoria',
            FILTER_VALIDATE_INT
        );

        if (
            !is_int($subcategoryId)
            || $subcategoryId <= 0
        ) {
            $this->renderNotFound();

            return;
        }

        try {
            $service = $this->buildService();

            $service->update(
                $subcategoryId,
                $_POST,
                $_FILES
            );

            Session::flash(
                'success',
                'La subcategoría fue actualizada correctamente.'
            );

            $this->redirectToSubcategories(
                (int) ($_POST['idCategoria'] ?? 0)
            );
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
                    'inventario/subcategorias/editar?id='
                    . $subcategoryId
                )
            );

            exit;
        }
    }

    public function changeState(): void
    {
        Auth::requirePermission(
            Permissions::INVENTARIO_GESTIONAR
        );

        $subcategoryId = filter_input(
            INPUT_POST,
            'idSubcategoria',
            FILTER_VALIDATE_INT
        );

        $categoryId = filter_input(
            INPUT_POST,
            'idCategoria',
            FILTER_VALIDATE_INT
        );

        $activeValue = filter_input(
            INPUT_POST,
            'activo',
            FILTER_VALIDATE_INT
        );

        if (
            !is_int($subcategoryId)
            || $subcategoryId <= 0
            || !is_int($categoryId)
            || $categoryId <= 0
            || !is_int($activeValue)
            || !in_array($activeValue, [0, 1], true)
        ) {
            Session::flash(
                'error',
                'No fue posible cambiar el estado.'
            );

            $this->redirectToSubcategories(
                is_int($categoryId)
                    ? $categoryId
                    : 0
            );
        }

        try {
            $active = $activeValue === 1;

            $this->buildService()
                ->changeActiveState(
                    $subcategoryId,
                    $active
                );

            Session::flash(
                'success',
                $active
                    ? 'La subcategoría fue activada.'
                    : 'La subcategoría fue desactivada.'
            );
        } catch (ValidationException $exception) {
            Session::flash(
                'error',
                $exception->getErrors()['general']
                    ?? 'No fue posible cambiar el estado.'
            );
        }

        $this->redirectToSubcategories(
            $categoryId
        );
    }

    private function buildService(): SubcategoriaService
    {
        return new SubcategoriaService(
            new SubcategoriaRepository(),
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

        return is_int($value)
            ? $value
            : 0;
    }

    private function safeOldInput(
        array $input
    ): array {
        return [
            'idCategoria' => (int) (
                $input['idCategoria'] ?? 0
            ),

            'nombreSubcategoria' => trim(
                (string) (
                    $input['nombreSubcategoria']
                    ?? ''
                )
            ),

            'descripcion' => trim(
                (string) (
                    $input['descripcion']
                    ?? ''
                )
            ),
        ];
    }

    private function redirectToSubcategories(
        int $categoryId
    ): void {
        header(
            'Location: '
            . base_url(
                'inventario/subcategorias?categoria='
                . $categoryId
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
                'title' =>
                    'Subcategoría no encontrada',
                'path' =>
                    '/inventario/subcategorias',
            ]
        );
    }
}