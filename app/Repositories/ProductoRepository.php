<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\ProductoRepositoryInterface;
use PDO;

final class ProductoRepository implements ProductoRepositoryInterface
{
    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection ?? Database::getConnection();
    }

    public function listBySubcategory(int $subcategoryId): ?array
    {
        $subcategory = $this->findSubcategoryById($subcategoryId);

        if ($subcategory === null) {
            return null;
        }

        $statement = $this->connection->prepare(
            '
            SELECT
                p.idProducto,
                p.idSubcategoria,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.descripcion,
                p.tipoProducto,
                p.vidaUtilMeses,
                p.imagen,
                p.activo,
                p.fechaRegistro,
                p.fechaActualizacion,

                COUNT(
                    DISTINCT CASE
                        WHEN a.activo = 1
                        THEN a.idActivo
                    END
                ) AS totalActivos,

                COUNT(
                    DISTINCT CASE
                        WHEN a.activo = 1
                         AND ea.codigoEstado = "EN_INVENTARIO"
                        THEN a.idActivo
                    END
                ) AS disponibles,

                COUNT(
                    DISTINCT CASE
                        WHEN a.activo = 1
                         AND ea.codigoEstado = "ASIGNADO"
                        THEN a.idActivo
                    END
                ) AS asignados,

                COUNT(
                    DISTINCT CASE
                        WHEN a.activo = 1
                         AND ea.codigoEstado IN (
                            "REVISION_TECNICA",
                            "EN_REPARACION"
                         )
                        THEN a.idActivo
                    END
                ) AS enServicioTecnico

            FROM Producto p

            LEFT JOIN Activo a
                ON a.idProducto = p.idProducto

            LEFT JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo

            WHERE p.idSubcategoria = :idSubcategoria

            GROUP BY
                p.idProducto,
                p.idSubcategoria,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.descripcion,
                p.tipoProducto,
                p.vidaUtilMeses,
                p.imagen,
                p.activo,
                p.fechaRegistro,
                p.fechaActualizacion

            ORDER BY
                p.activo DESC,
                p.nombreProducto ASC,
                p.marca ASC,
                p.modelo ASC
            '
        );

        $statement->execute([
            'idSubcategoria' => $subcategoryId,
        ]);

        return [
            'subcategory' => $subcategory,
            'products' => $statement->fetchAll(),
        ];
    }

    public function listActiveSubcategories(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                s.idSubcategoria,
                s.nombreSubcategoria,
                c.idCategoria,
                c.nombreCategoria
            FROM Subcategoria s
            INNER JOIN Categoria c
                ON c.idCategoria = s.idCategoria
            WHERE s.activo = 1
              AND c.activo = 1
            ORDER BY
                c.nombreCategoria ASC,
                s.nombreSubcategoria ASC
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function findSubcategoryById(int $subcategoryId): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                s.idSubcategoria,
                s.idCategoria,
                s.nombreSubcategoria,
                s.descripcion,
                s.imagen,
                s.activo,
                c.nombreCategoria,
                c.activo AS categoriaActiva
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

        return is_array($subcategory) ? $subcategory : null;
    }

    public function findById(int $productId): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                p.idProducto,
                p.idSubcategoria,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.descripcion,
                p.tipoProducto,
                p.vidaUtilMeses,
                p.imagen,
                p.activo,
                p.fechaRegistro,
                p.fechaActualizacion,
                s.nombreSubcategoria,
                s.activo AS subcategoriaActiva,
                c.idCategoria,
                c.nombreCategoria,
                c.activo AS categoriaActiva
            FROM Producto p
            INNER JOIN Subcategoria s
                ON s.idSubcategoria = p.idSubcategoria
            INNER JOIN Categoria c
                ON c.idCategoria = s.idCategoria
            WHERE p.idProducto = :idProducto
            LIMIT 1
            '
        );

        $statement->execute([
            'idProducto' => $productId,
        ]);

        $product = $statement->fetch();

        return is_array($product) ? $product : null;
    }

    public function productExists(
        int $subcategoryId,
        string $name,
        string $brand,
        string $model,
        ?int $excludeId = null
    ): bool {
        $sql = '
            SELECT 1
            FROM Producto
            WHERE idSubcategoria = :idSubcategoria
              AND LOWER(TRIM(nombreProducto)) = LOWER(TRIM(:nombreProducto))
              AND LOWER(TRIM(COALESCE(marca, ""))) = LOWER(TRIM(:marca))
              AND LOWER(TRIM(COALESCE(modelo, ""))) = LOWER(TRIM(:modelo))
        ';

        $parameters = [
            'idSubcategoria' => $subcategoryId,
            'nombreProducto' => $name,
            'marca' => $brand,
            'modelo' => $model,
        ];

        if ($excludeId !== null) {
            $sql .= ' AND idProducto <> :excludeId ';
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
            INSERT INTO Producto (
                idSubcategoria,
                nombreProducto,
                marca,
                modelo,
                descripcion,
                tipoProducto,
                vidaUtilMeses,
                imagen,
                activo
            ) VALUES (
                :idSubcategoria,
                :nombreProducto,
                :marca,
                :modelo,
                :descripcion,
                :tipoProducto,
                :vidaUtilMeses,
                :imagen,
                1
            )
            '
        );

        $statement->execute([
            'idSubcategoria' => $data['idSubcategoria'],
            'nombreProducto' => $data['nombreProducto'],
            'marca' => $data['marca'],
            'modelo' => $data['modelo'],
            'descripcion' => $data['descripcion'],
            'tipoProducto' => $data['tipoProducto'],
            'vidaUtilMeses' => $data['vidaUtilMeses'],
            'imagen' => $data['imagen'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function update(int $productId, array $data): void
    {
        $statement = $this->connection->prepare(
            '
            UPDATE Producto
            SET
                idSubcategoria = :idSubcategoria,
                nombreProducto = :nombreProducto,
                marca = :marca,
                modelo = :modelo,
                descripcion = :descripcion,
                tipoProducto = :tipoProducto,
                vidaUtilMeses = :vidaUtilMeses,
                imagen = :imagen
            WHERE idProducto = :idProducto
            '
        );

        $statement->execute([
            'idSubcategoria' => $data['idSubcategoria'],
            'nombreProducto' => $data['nombreProducto'],
            'marca' => $data['marca'],
            'modelo' => $data['modelo'],
            'descripcion' => $data['descripcion'],
            'tipoProducto' => $data['tipoProducto'],
            'vidaUtilMeses' => $data['vidaUtilMeses'],
            'imagen' => $data['imagen'],
            'idProducto' => $productId,
        ]);
    }

    public function setActiveState(int $productId, bool $active): void
    {
        $statement = $this->connection->prepare(
            '
            UPDATE Producto
            SET activo = :activo
            WHERE idProducto = :idProducto
            '
        );

        $statement->execute([
            'activo' => $active ? 1 : 0,
            'idProducto' => $productId,
        ]);
    }
}
