<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\CategoriaRepositoryInterface;
use PDO;

final class CategoriaRepository implements
    CategoriaRepositoryInterface
{
    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection
            ?? Database::getConnection();
    }

    public function listAll(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                c.idCategoria,
                c.nombreCategoria,
                c.descripcion,
                c.imagen,
                c.imagenAjuste,
                c.imagenTamano,
                c.activo,
                c.fechaRegistro,
                c.fechaActualizacion,

                COUNT(
                    DISTINCT CASE
                        WHEN s.activo = 1
                        THEN s.idSubcategoria
                    END
                ) AS totalSubcategorias,

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

            FROM Categoria c

            LEFT JOIN Subcategoria s
                ON s.idCategoria = c.idCategoria

            LEFT JOIN Producto p
                ON p.idSubcategoria = s.idSubcategoria

            LEFT JOIN Activo a
                ON a.idProducto = p.idProducto

            GROUP BY
                c.idCategoria,
                c.nombreCategoria,
                c.descripcion,
                c.imagen,
                c.imagenAjuste,
                c.imagenTamano,
                c.activo,
                c.fechaRegistro,
                c.fechaActualizacion

            ORDER BY
                c.activo DESC,
                c.nombreCategoria ASC
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function findById(
        int $categoryId
    ): ?array {
        $statement = $this->connection->prepare(
            '
            SELECT
                idCategoria,
                nombreCategoria,
                descripcion,
                imagen,
                imagenAjuste,
                imagenTamano,
                activo,
                fechaRegistro,
                fechaActualizacion
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

    public function nameExists(
        string $name,
        ?int $excludeId = null
    ): bool {
        $sql = '
            SELECT 1
            FROM Categoria
            WHERE nombreCategoria = :nombre
        ';

        $parameters = [
            'nombre' => $name,
        ];

        if ($excludeId !== null) {
            $sql .= '
                AND idCategoria <> :excludeId
            ';

            $parameters['excludeId'] = $excludeId;
        }

        $sql .= ' LIMIT 1 ';

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchColumn() !== false;
    }

    public function create(array $data): int
    {
        $statement = $this->connection->prepare(
            '
            INSERT INTO Categoria (
                nombreCategoria,
                descripcion,
                imagen,
                imagenAjuste,
                imagenTamano,
                activo
            ) VALUES (
                :nombreCategoria,
                :descripcion,
                :imagen,
                :imagenAjuste,
                :imagenTamano,
                1
            )
            '
        );

        $statement->execute([
            'nombreCategoria' => $data['nombreCategoria'],
            'descripcion' => $data['descripcion'],
            'imagen' => $data['imagen'],
            'imagenAjuste' => $data['imagenAjuste'],
            'imagenTamano' => $data['imagenTamano'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function update(
        int $categoryId,
        array $data
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE Categoria
            SET
                nombreCategoria = :nombreCategoria,
                descripcion = :descripcion,
                imagen = :imagen,
                imagenAjuste = :imagenAjuste,
                imagenTamano = :imagenTamano
            WHERE idCategoria = :idCategoria
            '
        );

        $statement->execute([
            'nombreCategoria' => $data['nombreCategoria'],
            'descripcion' => $data['descripcion'],
            'imagen' => $data['imagen'],
            'imagenAjuste' => $data['imagenAjuste'],
            'imagenTamano' => $data['imagenTamano'],
            'idCategoria' => $categoryId,
        ]);
    }

    public function setActiveState(
        int $categoryId,
        bool $active
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE Categoria
            SET activo = :activo
            WHERE idCategoria = :idCategoria
            '
        );

        $statement->execute([
            'activo' => $active ? 1 : 0,
            'idCategoria' => $categoryId,
        ]);
    }
}
