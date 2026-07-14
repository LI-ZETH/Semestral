<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Permissions;
use App\Core\Session;
use App\Core\ValidationException;
use App\Repositories\CategoriaRepository;
use App\Services\CategoriaService;
use App\Services\ImageUploadService;

final class CategoriaController extends Controller
{
    public function index(): void
    {
        Auth::requirePermission(
            Permissions::INVENTARIO_GESTIONAR
        );

        $this->view(
            'inventario/categories/index',
            [
                'title' =>
                    'Administrar categorías',

                'categories' =>
                    $this->buildService()->listAll(),

                'success' =>
                    flash('success'),

                'error' =>
                    flash('error'),
            ]
        );
    }

    public function create(): void
    {
        Auth::requirePermission(
            Permissions::INVENTARIO_GESTIONAR
        );

        $this->view(
            'inventario/categories/create',
            [
                'title' =>
                    'Registrar categoría',

                'errors' =>
                    flash('errors', []),

                'old' =>
                    flash('old', []),
            ]
        );
    }

    public function store(): void
    {
        Auth::requirePermission(
            Permissions::INVENTARIO_GESTIONAR
        );

        try {
            $this->buildService()->create(
                $_POST,
                $_FILES
            );

            Session::flash(
                'success',
                'La categoría fue registrada correctamente.'
            );

            $this->redirectToCategories();
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
                    'inventario/categorias/crear'
                )
            );

            exit;
        } catch (\Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        $exception->getMessage(),
                ]
            );

            Session::flash(
                'old',
                $this->safeOldInput($_POST)
            );

            header(
                'Location: '
                . base_url(
                    'inventario/categorias/crear'
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

        $categoryId = $this->getQueryId();

        $category = $this->buildService()
            ->findById($categoryId);

        if ($category === null) {
            $this->renderNotFound();

            return;
        }

        $old = flash('old', []);

        $this->view(
            'inventario/categories/edit',
            [
                'title' => 'Editar categoría',

                'category' => array_replace(
                    $category,
                    $old
                ),

                'errors' =>
                    flash('errors', []),
            ]
        );
    }

    public function update(): void
    {
        Auth::requirePermission(
            Permissions::INVENTARIO_GESTIONAR
        );

        $categoryId = filter_input(
            INPUT_POST,
            'idCategoria',
            FILTER_VALIDATE_INT
        );

        if (
            !is_int($categoryId)
            || $categoryId <= 0
        ) {
            $this->renderNotFound();

            return;
        }

        try {
            $this->buildService()->update(
                $categoryId,
                $_POST,
                $_FILES
            );

            Session::flash(
                'success',
                'La categoría fue actualizada correctamente.'
            );

            $this->redirectToCategories();
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
                    'inventario/categorias/editar?id='
                    . $categoryId
                )
            );

            exit;
        } catch (\Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        $exception->getMessage(),
                ]
            );

            Session::flash(
                'old',
                $this->safeOldInput($_POST)
            );

            header(
                'Location: '
                . base_url(
                    'inventario/categorias/editar?id='
                    . $categoryId
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
            !is_int($categoryId)
            || $categoryId <= 0
            || !is_int($activeValue)
            || !in_array(
                $activeValue,
                [0, 1],
                true
            )
        ) {
            Session::flash(
                'error',
                'No fue posible cambiar el estado.'
            );

            $this->redirectToCategories();
        }

        try {
            $active = $activeValue === 1;

            $this->buildService()
                ->changeActiveState(
                    $categoryId,
                    $active
                );

            Session::flash(
                'success',
                $active
                    ? 'La categoría fue activada.'
                    : 'La categoría fue desactivada.'
            );
        } catch (ValidationException $exception) {
            Session::flash(
                'error',
                $exception->getErrors()['general']
                    ?? 'No fue posible cambiar el estado.'
            );
        }

        $this->redirectToCategories();
    }

    private function buildService(): CategoriaService
    {
        return new CategoriaService(
            new CategoriaRepository(),
            new ImageUploadService()
        );
    }

    private function getQueryId(): int
    {
        $categoryId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        return is_int($categoryId)
            ? $categoryId
            : 0;
    }

    private function safeOldInput(
        array $input
    ): array {
        return [
            'nombreCategoria' => trim(
                (string) (
                    $input['nombreCategoria']
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

    private function redirectToCategories(): void
    {
        header(
            'Location: '
            . base_url(
                'inventario/categorias'
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
                    'Categoría no encontrada',

                'path' =>
                    '/inventario/categorias',
            ]
        );
    }
}