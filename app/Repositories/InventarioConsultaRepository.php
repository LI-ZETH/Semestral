<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\InventarioConsultaRepositoryInterface;
use PDO;

final class InventarioConsultaRepository implements
    InventarioConsultaRepositoryInterface
{
    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection
            ?? Database::getConnection();
    }

    public function getCategorySummary(): array
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
                COALESCE(v.totalActivos, 0)
                    AS totalActivos,
                COALESCE(v.enInventario, 0)
                    AS enInventario,
                COALESCE(v.asignados, 0)
                    AS asignados,
                COALESCE(v.enRevision, 0)
                    AS enRevision,
                COALESCE(v.enReparacion, 0)
                    AS enReparacion
            FROM Categoria c
            LEFT JOIN VistaResumenCategoria v
                ON v.idCategoria = c.idCategoria
            WHERE c.activo = 1
            ORDER BY c.nombreCategoria ASC
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function getCategorySubcategories(
        int $categoryId
    ): ?array {
        $categoryStatement = $this->connection->prepare(
            '
            SELECT
                idCategoria,
                nombreCategoria,
                descripcion,
                imagen
            FROM Categoria
            WHERE idCategoria = :idCategoria
            AND activo = 1
            LIMIT 1
            '
        );

        $categoryStatement->execute([
            'idCategoria' => $categoryId,
        ]);

        $category = $categoryStatement->fetch();

        if (!is_array($category)) {
            return null;
        }

        $statement = $this->connection->prepare(
            '
            SELECT
                s.idSubcategoria,
                s.nombreSubcategoria,
                s.descripcion,
                s.imagen,

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

            WHERE s.idCategoria = :subcategoryCategoryId
            AND s.activo = 1

            GROUP BY
                s.idSubcategoria,
                s.nombreSubcategoria,
                s.descripcion,
                s.imagen

            ORDER BY s.nombreSubcategoria ASC
            '
        );

        $statement->execute([
            'subcategoryCategoryId' => $categoryId,
        ]);

        return [
            'category' => $category,
            'subcategories' => $statement->fetchAll(),
        ];
    }

    public function getSubcategoryProducts(
        int $subcategoryId
    ): ?array {
        $subcategoryStatement = $this->connection->prepare(
            '
            SELECT
                s.idSubcategoria,
                s.idCategoria,
                s.nombreSubcategoria,
                s.descripcion,
                s.imagen,
                c.nombreCategoria
            FROM Subcategoria s
            INNER JOIN Categoria c
                ON c.idCategoria = s.idCategoria
            WHERE s.idSubcategoria = :idSubcategoria
            AND s.activo = 1
            AND c.activo = 1
            LIMIT 1
            '
        );

        $subcategoryStatement->execute([
            'idSubcategoria' => $subcategoryId,
        ]);

        $subcategory = $subcategoryStatement->fetch();

        if (!is_array($subcategory)) {
            return null;
        }

        $productStatement = $this->connection->prepare(
            '
            SELECT
                p.idProducto,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.descripcion,
                p.tipoProducto,
                p.vidaUtilMeses,
                p.imagen,

                COUNT(a.idActivo) AS totalActivos,

                SUM(
                    CASE
                        WHEN ea.codigoEstado = "EN_INVENTARIO"
                        THEN 1
                        ELSE 0
                    END
                ) AS disponibles,

                SUM(
                    CASE
                        WHEN ea.codigoEstado = "ASIGNADO"
                        THEN 1
                        ELSE 0
                    END
                ) AS asignados,

                SUM(
                    CASE
                        WHEN ea.codigoEstado = "EN_REPARACION"
                        THEN 1
                        ELSE 0
                    END
                ) AS enReparacion

            FROM Producto p

            LEFT JOIN Activo a
                ON a.idProducto = p.idProducto
            AND a.activo = 1

            LEFT JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo

            WHERE p.idSubcategoria = :productSubcategoryId
            AND p.activo = 1

            GROUP BY
                p.idProducto,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.descripcion,
                p.tipoProducto,
                p.vidaUtilMeses,
                p.imagen

            ORDER BY
                p.nombreProducto ASC,
                p.marca ASC,
                p.modelo ASC
            '
        );

        $productStatement->execute([
            'productSubcategoryId' => $subcategoryId,
        ]);

        return [
            'subcategory' => $subcategory,
            'products' => $productStatement->fetchAll(),
        ];
    }

    public function getProductDetail(
        int $productId
    ): ?array {
        $productStatement = $this->connection->prepare(
            '
            SELECT
                p.idProducto,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.descripcion,
                p.tipoProducto,
                p.vidaUtilMeses,
                p.imagen,
                s.idSubcategoria,
                s.nombreSubcategoria,
                c.idCategoria,
                c.nombreCategoria
            FROM Producto p

            INNER JOIN Subcategoria s
                ON s.idSubcategoria = p.idSubcategoria

            INNER JOIN Categoria c
                ON c.idCategoria = s.idCategoria

            WHERE p.idProducto = :idProducto
              AND p.activo = 1

            LIMIT 1
            '
        );

        $productStatement->execute([
            'idProducto' => $productId,
        ]);

        $product = $productStatement->fetch();

        if (!is_array($product)) {
            return null;
        }

        $assetStatement = $this->connection->prepare(
            '
            SELECT
                idActivo,
                codigoActivo,
                numeroSerie,
                direccionIP,
                costo,
                fechaAdquisicion,
                fechaIngreso,
                vidaUtilMesesAplicada,
                fechaFinVidaUtil,
                imagenPrincipal,
                cantidadImagenes,
                codigoEstado,
                nombreEstado,
                nombreUbicacion,
                idColaborador,
                nombreColaborador
            FROM VistaInventarioDetalle
            WHERE idProducto = :idProducto
            ORDER BY codigoActivo ASC
            '
        );

        $assetStatement->execute([
            'idProducto' => $productId,
        ]);

        return [
            'product' => $product,
            'assets' => $assetStatement->fetchAll(),
        ];
    }
}