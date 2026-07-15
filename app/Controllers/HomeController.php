<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use PDO;

final class HomeController extends Controller
{
    public function index(): void
    {
        $connection = Database::getConnection();

        $statistics = [
            'categorias' => $this->countActiveRecords(
                $connection,
                'Categoria'
            ),
            'subcategorias' => $this->countActiveRecords(
                $connection,
                'Subcategoria'
            ),
            'productos' => $this->countActiveRecords(
                $connection,
                'Producto'
            ),
            'activos' => $this->countActiveRecords(
                $connection,
                'Activo'
            ),
        ];

        $this->view(
            'public/home',
            [
                'title' => 'Inicio',
                'activePage' => 'inicio',
                'bodyClass' => 'public-home',
                'statistics' => $statistics,
            ],
            'public'
        );
    }

    public function features(): void
    {
        $this->renderPublicPage(
            'public/features',
            'Funcionalidades',
            'funcionalidades'
        );
    }

    public function news(): void
    {
        $this->renderPublicPage(
            'public/news',
            'Noticias',
            'noticias'
        );
    }

    public function about(): void
    {
        $this->renderPublicPage(
            'public/about',
            'Nosotros',
            'nosotros'
        );
    }

    public function help(): void
    {
        $this->renderPublicPage(
            'public/help',
            'Manual de usuario',
            'ayuda'
        );
    }

    private function renderPublicPage(
        string $view,
        string $title,
        string $activePage
    ): void {
        $this->view(
            $view,
            [
                'title' => $title,
                'activePage' => $activePage,
                'bodyClass' => 'public-content-page',
            ],
            'public'
        );
    }

    private function countActiveRecords(
        PDO $connection,
        string $table
    ): int {
        $allowedTables = [
            'Categoria',
            'Subcategoria',
            'Producto',
            'Activo',
        ];

        if (!in_array($table, $allowedTables, true)) {
            return 0;
        }

        $statement = $connection->query(
            "SELECT COUNT(*) FROM {$table} WHERE activo = 1"
        );

        return (int) $statement->fetchColumn();
    }
}
