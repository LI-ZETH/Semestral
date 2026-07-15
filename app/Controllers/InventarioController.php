<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Roles;
use App\Repositories\InventarioConsultaRepository;

final class InventarioController extends Controller
{
    public function index(): void
    {
        Auth::requireAnyRole([
            Roles::ADMINISTRADOR,
            Roles::TECNICO,
        ]);

        $repository =
            new InventarioConsultaRepository();

        $this->view(
            'inventario/index',
            [
                'title' => 'Inventario',
                'categories' =>
                    $repository->getCategorySummary(),
            ]
        );
    }

    public function category(): void
    {
        Auth::requireAnyRole([
            Roles::ADMINISTRADOR,
            Roles::TECNICO,
        ]);

        $categoryId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (
            !is_int($categoryId)
            || $categoryId <= 0
        ) {
            $this->renderNotFound('/inventario');

            return;
        }

        $repository =
            new InventarioConsultaRepository();

        $result = $repository->getCategorySubcategories(
            $categoryId
        );

        if ($result === null) {
            $this->renderNotFound('/inventario');

            return;
        }

        $this->view(
            'inventario/category',
            [
                'title' =>
                    $result['category']['nombreCategoria'],
                'category' => $result['category'],
                'subcategories' =>
                    $result['subcategories'],
            ]
        );
    }

    public function subcategory(): void
    {
        Auth::requireAnyRole([
            Roles::ADMINISTRADOR,
            Roles::TECNICO,
        ]);

        $subcategoryId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (
            !is_int($subcategoryId)
            || $subcategoryId <= 0
        ) {
            $this->renderNotFound('/inventario');

            return;
        }

        $repository =
            new InventarioConsultaRepository();

        $result = $repository
            ->getSubcategoryProducts(
                $subcategoryId
            );

        if ($result === null) {
            $this->renderNotFound('/inventario');

            return;
        }

        $this->view(
            'inventario/subcategory',
            [
                'title' =>
                    $result['subcategory'][
                        'nombreSubcategoria'
                    ],
                'subcategory' =>
                    $result['subcategory'],
                'products' =>
                    $result['products'],
            ]
        );
    }

    public function product(): void
    {
        Auth::requireAnyRole([
            Roles::ADMINISTRADOR,
            Roles::TECNICO,
        ]);

        $productId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (
            !is_int($productId)
            || $productId <= 0
        ) {
            $this->renderNotFound('/inventario');

            return;
        }

        $repository =
            new InventarioConsultaRepository();

        $result = $repository->getProductDetail(
            $productId
        );

        if ($result === null) {
            $this->renderNotFound('/inventario');

            return;
        }

        $this->view(
            'inventario/product',
            [
                'title' =>
                    $result['product']['nombreProducto'],
                'product' => $result['product'],
                'assets' => $result['assets'],
            ]
        );
    }

    private function renderNotFound(
        string $path
    ): void {
        http_response_code(404);

        $this->view(
            'errors/404',
            [
                'title' => 'Elemento no encontrado',
                'path' => $path,
            ]
        );
    }
}