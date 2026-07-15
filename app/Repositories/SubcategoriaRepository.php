<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\SubcategoriaRepositoryInterface;
use PDO;

final class SubcategoriaRepository implements
    SubcategoriaRepositoryInterface
{
    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection
            ?? Database::getConnection();
    }

    public function listByCategory(
        int $categoryId
    ): ?array {
        $category = $this->findCategoryById(
            $categoryId
        );

        if ($category === null) {
            return null;
        }

        $statement = $this->connection->prepare(
            '
            SELECT
                s.idSubcategoria,
                s.idCategoria,
                s.nombreSubcategoria,
                s.descripcion,
                s.imagen,
                s.activo,
                s.fechaRegistro,

                COUNT(
                    DISTINCT CASE
                        WHEN p.activo = 1
                        THEN p.idProducto
                    END
                ) AS totalProductos,

                COUNT(
                    DISTINCT CASE
                        WHEN a.activo = 1
                        THEN a.idActivo
                    END
                ) AS totalActivos

            FROM Subcategoria s

            LEFT JOIN Producto p
                ON p.idSubcategoria = s.idSubcategoria

            LEFT JOIN Activo a
                ON a.idProducto = p.idProducto

            WHERE s.idCategoria = :idCategoria

            GROUP BY
                s.idSubcategoria,
                s.idCategoria,
                s.nombreSubcategoria,
                s.descripcion,
                s.imagen,
                s.activo,
                s.fechaRegistro

            ORDER BY
                s.activo DESC,
                s.nombreSubcategoria ASC
            '
        );

        $statement->execute([
            'idCategoria' => $categoryId,
        ]);

        return [
            'category' => $category,
            'subcategories' => $statement->fetchAll(),
        ];
    }

    public function listActiveCategories(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                idCategoria,
                nombreCategoria
            FROM Categoria
            WHERE activo = 1
            ORDER BY nombreCategoria ASC
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function findCategoryById(
        int $categoryId
    ): ?array {
        $statement = $this->connection->prepare(
            '
            SELECT
                idCategoria,
                nombreCategoria,
                descripcion,
                imagen,
                activo
            FROM Categoria
            WHERE idCategoria = :idCategoria
            LIMIT 1
            '
        );

        $statement->execute([
            'idCategoria' => $categoryId,
        ]);

        $category = $statement->fetch();

        return is_array($category)
            ? $category
            : null;
    }

    public function findById(
        int $subcategoryId
    ): ?array {
        $statement = $this->connection->prepare(
            '
            SELECT
                s.idSubcategoria,
                s.idCategoria,
                s.nombreSubcategoria,
                s.descripcion,
                s.imagen,
                s.activo,
                s.fechaRegistro,
                c.nombreCategoria
            FROM Subcategoria s
            INNER JOIN Categoria c
                ON c.idCategoria = s.idCategoria
            WHERE s.idSubcategoria = :idSubcategoria
            LIMIT 1
            '
        );

        $statement->execute([
            'idSubcategoria' => $subcategoryId,
        ]);

        $subcategory = $statement->fetch();

        return is_array($subcategory)
            ? $subcategory
            : null;
    }

    public function nameExists(
        int $categoryId,
        string $name,
        ?int $excludeId = null
    ): bool {
        $sql = '
            SELECT 1
            FROM Subcategoria
            WHERE idCategoria = :idCategoria
              AND nombreSubcategoria = :nombreSubcategoria
        ';

        $parameters = [
            'idCategoria' => $categoryId,
            'nombreSubcategoria' => $name,
        ];

        if ($excludeId !== null) {
            $sql .= '
                AND idSubcategoria <> :excludeId
            ';

            $parameters['excludeId'] = $excludeId;
        }

        $sql .= ' LIMIT 1 ';

        $statement = $this->connection->prepare(
            $sql
        );

        $statement->execute($parameters);

        return $statement->fetchColumn() !== false;
    }

    public function create(array $data): int
    {
        $statement = $this->connection->prepare(
            '
            INSERT INTO Subcategoria (
                idCategoria,
                nombreSubcategoria,
                descripcion,
                imagen,
                activo
            ) VALUES (
                :idCategoria,
                :nombreSubcategoria,
                :descripcion,
                :imagen,
                1
            )
            '
        );

        $statement->execute([
            'idCategoria' => $data['idCategoria'],
            'nombreSubcategoria' =>
                $data['nombreSubcategoria'],
            'descripcion' => $data['descripcion'],
            'imagen' => $data['imagen'],
        ]);

        return (int) $this->connection
            ->lastInsertId();
    }

    public function update(
        int $subcategoryId,
        array $data
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE Subcategoria
            SET
                idCategoria = :idCategoria,
                nombreSubcategoria = :nombreSubcategoria,
                descripcion = :descripcion,
                imagen = :imagen
            WHERE idSubcategoria = :idSubcategoria
            '
        );

        $statement->execute([
            'idCategoria' => $data['idCategoria'],
            'nombreSubcategoria' =>
                $data['nombreSubcategoria'],
            'descripcion' => $data['descripcion'],
            'imagen' => $data['imagen'],
            'idSubcategoria' => $subcategoryId,
        ]);
    }

    public function setActiveState(
        int $subcategoryId,
        bool $active
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE Subcategoria
            SET activo = :activo
            WHERE idSubcategoria = :idSubcategoria
            '
        );

        $statement->execute([
            'activo' => $active ? 1 : 0,
            'idSubcategoria' => $subcategoryId,
        ]);
    }
}