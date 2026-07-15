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
            'home/index',
            [
                'title' => 'Inicio',
                'statistics' => $statistics,
            ]
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