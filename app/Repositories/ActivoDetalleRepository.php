<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\ActivoDetalleRepositoryInterface;
use PDO;

final class ActivoDetalleRepository implements
    ActivoDetalleRepositoryInterface
{
    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection
            ?? Database::getConnection();
    }

    public function findInternalById(int $assetId): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                a.idActivo,
                a.idProducto,
                a.codigoActivo,
                a.numeroSerie,
                a.direccionIP,
                a.costo,
                a.fechaAdquisicion,
                a.fechaIngreso,
                a.vidaUtilMeses,
                a.valorResidual,
                a.idEstadoActivo,
                a.idUbicacion,
                a.qrToken,
                a.observaciones,
                a.activo,
                a.fechaRegistro,
                a.fechaActualizacion,

                COALESCE(
                    a.vidaUtilMeses,
                    p.vidaUtilMeses
                ) AS vidaUtilMesesAplicada,

                CASE
                    WHEN COALESCE(
                        a.vidaUtilMeses,
                        p.vidaUtilMeses
                    ) IS NULL
                    THEN NULL
                    ELSE DATE_ADD(
                        a.fechaAdquisicion,
                        INTERVAL COALESCE(
                            a.vidaUtilMeses,
                            p.vidaUtilMeses
                        ) MONTH
                    )
                END AS fechaFinVidaUtil,

                ea.codigoEstado,
                ea.nombreEstado,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.descripcion AS descripcionProducto,
                p.tipoProducto,
                p.idSubcategoria,
                s.nombreSubcategoria,
                s.idCategoria,
                cat.nombreCategoria,

                u.nombreUbicacion,
                u.tipoUbicacion,
                u.edificio,
                u.piso,
                u.oficina,
                u.direccion,
                u.descripcion AS descripcionUbicacion,

                aa.idAsignacion,
                aa.fechaEntrega,
                aa.observacionesEntrega,
                col.idColaborador,
                CONCAT_WS(
                    " ",
                    col.nombre,
                    col.apellido
                ) AS nombreColaborador,
                col.correo AS correoColaborador,
                col.telefono AS telefonoColaborador,
                col.cargo AS cargoColaborador,
                col.departamento AS departamentoColaborador

            FROM Activo a

            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo

            INNER JOIN Producto p
                ON p.idProducto = a.idProducto

            INNER JOIN Subcategoria s
                ON s.idSubcategoria = p.idSubcategoria

            INNER JOIN Categoria cat
                ON cat.idCategoria = s.idCategoria

            LEFT JOIN Ubicacion u
                ON u.idUbicacion = a.idUbicacion

            LEFT JOIN AsignacionActivo aa
                ON aa.idAsignacion = (
                    SELECT MAX(aa2.idAsignacion)
                    FROM AsignacionActivo aa2
                    WHERE aa2.idActivo = a.idActivo
                      AND aa2.estadoAsignacion = "ACTIVA"
                )

            LEFT JOIN Colaborador col
                ON col.idColaborador = aa.idColaborador

            WHERE a.idActivo = :idActivo
            LIMIT 1
            '
        );

        $statement->execute([
            'idActivo' => $assetId,
        ]);

        $asset = $statement->fetch();

        return is_array($asset)
            ? $asset
            : null;
    }

    public function findPublicByToken(string $token): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                a.idActivo,
                a.codigoActivo,
                a.qrToken,
                a.activo,
                a.fechaIngreso,
                a.fechaActualizacion,
                ea.codigoEstado,
                ea.nombreEstado,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.tipoProducto,
                s.nombreSubcategoria,
                cat.nombreCategoria
            FROM Activo a
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN Subcategoria s
                ON s.idSubcategoria = p.idSubcategoria
            INNER JOIN Categoria cat
                ON cat.idCategoria = s.idCategoria
            WHERE a.qrToken = :qrToken
            LIMIT 1
            '
        );

        $statement->execute([
            'qrToken' => $token,
        ]);

        $asset = $statement->fetch();

        return is_array($asset)
            ? $asset
            : null;
    }

    public function findImages(int $assetId): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                idImagenActivo,
                rutaImagen,
                nombreOriginal,
                mimeType,
                tamanoBytes,
                esPrincipal,
                ordenVisual,
                fechaRegistro
            FROM ImagenActivo
            WHERE idActivo = :idActivo
              AND activo = 1
            ORDER BY
                esPrincipal DESC,
                ordenVisual ASC,
                idImagenActivo ASC
            '
        );

        $statement->execute([
            'idActivo' => $assetId,
        ]);

        return $statement->fetchAll();
    }

    public function findRecentMovements(int $assetId): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                m.idMovimiento,
                m.tipoMovimiento,
                m.descripcion,
                m.fechaMovimiento,
                ea.nombreEstado AS estadoAnterior,
                en.nombreEstado AS estadoNuevo,
                ua.nombreUbicacion AS ubicacionAnterior,
                un.nombreUbicacion AS ubicacionNueva,
                CONCAT_WS(
                    " ",
                    usr.nombre,
                    usr.apellido
                ) AS realizadoPor
            FROM MovimientoActivo m
            INNER JOIN Usuario usr
                ON usr.idUsuario = m.idUsuario
            LEFT JOIN EstadoActivo ea
                ON ea.idEstadoActivo = m.idEstadoAnterior
            LEFT JOIN EstadoActivo en
                ON en.idEstadoActivo = m.idEstadoNuevo
            LEFT JOIN Ubicacion ua
                ON ua.idUbicacion = m.idUbicacionAnterior
            LEFT JOIN Ubicacion un
                ON un.idUbicacion = m.idUbicacionNueva
            WHERE m.idActivo = :idActivo
            ORDER BY
                m.fechaMovimiento DESC,
                m.idMovimiento DESC
            LIMIT 20
            '
        );

        $statement->execute([
            'idActivo' => $assetId,
        ]);

        return $statement->fetchAll();
    }

    public function findRepairs(int $assetId): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                r.idReparacion,
                r.descripcionFalla,
                r.diagnostico,
                r.trabajoRealizado,
                r.costoReparacion,
                r.fechaInicio,
                r.fechaFin,
                r.observaciones,
                er.nombreEstado AS estadoReparacion,
                CONCAT_WS(
                    " ",
                    t.nombre,
                    t.apellido
                ) AS tecnico
            FROM Reparacion r
            INNER JOIN EstadoReparacion er
                ON er.idEstadoReparacion = r.idEstadoReparacion
            INNER JOIN Usuario t
                ON t.idUsuario = r.idTecnico
            WHERE r.idActivo = :idActivo
            ORDER BY
                r.fechaInicio DESC,
                r.idReparacion DESC
            LIMIT 15
            '
        );

        $statement->execute([
            'idActivo' => $assetId,
        ]);

        return $statement->fetchAll();
    }
}
