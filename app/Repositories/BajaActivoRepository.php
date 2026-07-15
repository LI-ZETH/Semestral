<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\BajaActivoRepositoryInterface;
use PDO;

final class BajaActivoRepository implements
    BajaActivoRepositoryInterface
{
    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection
            ?? Database::getConnection();
    }

    public function listAll(array $filters = []): array
    {
        $sql = '
            SELECT
                b.idBaja,
                b.idActivo,
                b.idTipoBaja,
                b.idUsuario,
                b.motivo,
                b.opinionTecnica,
                b.responsableDonacion,
                b.entidadBeneficiaria,
                b.documentoReferencia,
                b.fechaBaja,
                tb.codigoTipo,
                tb.nombreTipo,
                a.codigoActivo,
                a.numeroSerie,
                a.costo,
                a.valorResidual,
                ea.codigoEstado,
                ea.nombreEstado,
                p.nombreProducto,
                p.marca,
                p.modelo,
                s.nombreSubcategoria,
                c.nombreCategoria,
                CONCAT_WS(
                    " ",
                    usr.nombre,
                    usr.apellido
                ) AS registradoPor,
                (
                    SELECT ia.rutaImagen
                    FROM ImagenActivo ia
                    WHERE ia.idActivo = a.idActivo
                      AND ia.activo = 1
                    ORDER BY
                        ia.esPrincipal DESC,
                        ia.ordenVisual ASC,
                        ia.idImagenActivo ASC
                    LIMIT 1
                ) AS imagenPrincipal
            FROM BajaActivo b
            INNER JOIN TipoBaja tb
                ON tb.idTipoBaja = b.idTipoBaja
            INNER JOIN Activo a
                ON a.idActivo = b.idActivo
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN Subcategoria s
                ON s.idSubcategoria = p.idSubcategoria
            INNER JOIN Categoria c
                ON c.idCategoria = s.idCategoria
            INNER JOIN Usuario usr
                ON usr.idUsuario = b.idUsuario
            WHERE 1 = 1
        ';

        $parameters = [];
        $search = trim((string) ($filters['search'] ?? ''));
        $typeId = (int) ($filters['type'] ?? 0);

        if ($search !== '') {
            $pattern = '%' . $search . '%';

            $sql .= '
                AND (
                    a.codigoActivo LIKE :searchCode
                    OR COALESCE(a.numeroSerie, "")
                        LIKE :searchSerial
                    OR p.nombreProducto LIKE :searchProduct
                    OR COALESCE(p.marca, "")
                        LIKE :searchBrand
                    OR COALESCE(p.modelo, "")
                        LIKE :searchModel
                    OR COALESCE(b.entidadBeneficiaria, "")
                        LIKE :searchEntity
                    OR COALESCE(b.documentoReferencia, "")
                        LIKE :searchDocument
                    OR CONCAT_WS(
                        " ",
                        usr.nombre,
                        usr.apellido
                    ) LIKE :searchUser
                )
            ';

            $parameters['searchCode'] = $pattern;
            $parameters['searchSerial'] = $pattern;
            $parameters['searchProduct'] = $pattern;
            $parameters['searchBrand'] = $pattern;
            $parameters['searchModel'] = $pattern;
            $parameters['searchEntity'] = $pattern;
            $parameters['searchDocument'] = $pattern;
            $parameters['searchUser'] = $pattern;
        }

        if ($typeId > 0) {
            $sql .= ' AND b.idTipoBaja = :typeId ';
            $parameters['typeId'] = $typeId;
        }

        $sql .= '
            ORDER BY
                b.fechaBaja DESC,
                b.idBaja DESC
        ';

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function listEligibleAssets(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                a.idActivo,
                a.codigoActivo,
                a.numeroSerie,
                a.costo,
                a.valorResidual,
                a.fechaIngreso,
                ea.codigoEstado,
                ea.nombreEstado,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.tipoProducto,
                s.nombreSubcategoria,
                c.nombreCategoria
            FROM Activo a
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN Subcategoria s
                ON s.idSubcategoria = p.idSubcategoria
            INNER JOIN Categoria c
                ON c.idCategoria = s.idCategoria
            WHERE a.activo = 1
              AND p.activo = 1
              AND s.activo = 1
              AND c.activo = 1
              AND ea.codigoEstado IN (
                    "EN_INVENTARIO",
                    "REVISION_TECNICA"
              )
              AND NOT EXISTS (
                    SELECT 1
                    FROM BajaActivo b
                    WHERE b.idActivo = a.idActivo
              )
              AND NOT EXISTS (
                    SELECT 1
                    FROM AsignacionActivo aa
                    WHERE aa.idActivo = a.idActivo
                      AND aa.estadoAsignacion = "ACTIVA"
                      AND aa.fechaDevolucion IS NULL
              )
              AND NOT EXISTS (
                    SELECT 1
                    FROM Reparacion r
                    INNER JOIN EstadoReparacion er
                        ON er.idEstadoReparacion = r.idEstadoReparacion
                    WHERE r.idActivo = a.idActivo
                      AND r.fechaFin IS NULL
                      AND er.nombreEstado IN (
                            "Pendiente",
                            "En proceso"
                      )
              )
              AND NOT EXISTS (
                    SELECT 1
                    FROM SolicitudReparacion sr
                    WHERE sr.idActivo = a.idActivo
                      AND sr.estadoSolicitud IN (
                            "EN_ESPERA",
                            "ASIGNADA",
                            "EN_PROCESO"
                      )
              )
              AND NOT EXISTS (
                    SELECT 1
                    FROM LicenciaSoftware ls
                    INNER JOIN AsignacionLicencia al
                        ON al.idLicencia = ls.idLicencia
                    WHERE ls.idActivo = a.idActivo
                      AND al.estadoAsignacion = "ACTIVA"
                      AND al.fechaRevocacion IS NULL
              )
            ORDER BY
                c.nombreCategoria ASC,
                s.nombreSubcategoria ASC,
                p.nombreProducto ASC,
                a.codigoActivo ASC
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function listTypes(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                idTipoBaja,
                codigoTipo,
                nombreTipo
            FROM TipoBaja
            ORDER BY idTipoBaja ASC
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function findById(int $disposalId): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                b.idBaja,
                b.idActivo,
                b.idTipoBaja,
                b.idUsuario,
                b.motivo,
                b.opinionTecnica,
                b.responsableDonacion,
                b.entidadBeneficiaria,
                b.documentoReferencia,
                b.fechaBaja,
                tb.codigoTipo,
                tb.nombreTipo,
                a.idProducto,
                a.codigoActivo,
                a.numeroSerie,
                a.direccionIP,
                a.costo,
                a.valorResidual,
                a.fechaAdquisicion,
                a.fechaIngreso,
                a.idEstadoActivo,
                a.idUbicacion,
                a.qrToken,
                a.activo,
                ea.codigoEstado,
                ea.nombreEstado,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.tipoProducto,
                p.idSubcategoria,
                s.nombreSubcategoria,
                s.idCategoria,
                c.nombreCategoria,
                u.nombreUbicacion,
                CONCAT_WS(
                    " ",
                    usr.nombre,
                    usr.apellido
                ) AS registradoPor,
                usr.correo AS correoRegistrador,
                (
                    SELECT ia.rutaImagen
                    FROM ImagenActivo ia
                    WHERE ia.idActivo = a.idActivo
                      AND ia.activo = 1
                    ORDER BY
                        ia.esPrincipal DESC,
                        ia.ordenVisual ASC,
                        ia.idImagenActivo ASC
                    LIMIT 1
                ) AS imagenPrincipal
            FROM BajaActivo b
            INNER JOIN TipoBaja tb
                ON tb.idTipoBaja = b.idTipoBaja
            INNER JOIN Activo a
                ON a.idActivo = b.idActivo
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN Subcategoria s
                ON s.idSubcategoria = p.idSubcategoria
            INNER JOIN Categoria c
                ON c.idCategoria = s.idCategoria
            LEFT JOIN Ubicacion u
                ON u.idUbicacion = a.idUbicacion
            INNER JOIN Usuario usr
                ON usr.idUsuario = b.idUsuario
            WHERE b.idBaja = :idBaja
            LIMIT 1
            '
        );

        $statement->execute([
            'idBaja' => $disposalId,
        ]);

        $disposal = $statement->fetch();

        return is_array($disposal)
            ? $disposal
            : null;
    }

    public function findAssetById(
        int $assetId,
        bool $forUpdate = false
    ): ?array {
        $sql = '
            SELECT
                a.idActivo,
                a.idProducto,
                a.codigoActivo,
                a.numeroSerie,
                a.costo,
                a.valorResidual,
                a.fechaIngreso,
                a.idEstadoActivo,
                a.idUbicacion,
                a.activo,
                ea.codigoEstado,
                ea.nombreEstado,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.tipoProducto,
                p.activo AS productoActivo,
                s.nombreSubcategoria,
                s.activo AS subcategoriaActiva,
                c.nombreCategoria,
                c.activo AS categoriaActiva
            FROM Activo a
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN Subcategoria s
                ON s.idSubcategoria = p.idSubcategoria
            INNER JOIN Categoria c
                ON c.idCategoria = s.idCategoria
            WHERE a.idActivo = :idActivo
            LIMIT 1
        ';

        if ($forUpdate) {
            $sql .= ' FOR UPDATE ';
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            'idActivo' => $assetId,
        ]);

        $asset = $statement->fetch();

        return is_array($asset)
            ? $asset
            : null;
    }

    public function findTypeById(int $typeId): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                idTipoBaja,
                codigoTipo,
                nombreTipo
            FROM TipoBaja
            WHERE idTipoBaja = :idTipoBaja
            LIMIT 1
            '
        );

        $statement->execute([
            'idTipoBaja' => $typeId,
        ]);

        $type = $statement->fetch();

        return is_array($type)
            ? $type
            : null;
    }

    public function findStateByCode(string $stateCode): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                idEstadoActivo,
                codigoEstado,
                nombreEstado,
                activo
            FROM EstadoActivo
            WHERE codigoEstado = :codigoEstado
            LIMIT 1
            '
        );

        $statement->execute([
            'codigoEstado' => $stateCode,
        ]);

        $state = $statement->fetch();

        return is_array($state)
            ? $state
            : null;
    }

    public function hasRegisteredDisposal(int $assetId): bool
    {
        $statement = $this->connection->prepare(
            '
            SELECT 1
            FROM BajaActivo
            WHERE idActivo = :idActivo
            LIMIT 1
            '
        );

        $statement->execute([
            'idActivo' => $assetId,
        ]);

        return $statement->fetchColumn() !== false;
    }

    public function hasActiveAssignment(int $assetId): bool
    {
        $statement = $this->connection->prepare(
            '
            SELECT 1
            FROM AsignacionActivo
            WHERE idActivo = :idActivo
              AND estadoAsignacion = "ACTIVA"
              AND fechaDevolucion IS NULL
            LIMIT 1
            '
        );

        $statement->execute([
            'idActivo' => $assetId,
        ]);

        return $statement->fetchColumn() !== false;
    }

    public function hasOpenRepair(int $assetId): bool
    {
        $statement = $this->connection->prepare(
            '
            SELECT 1
            FROM Reparacion r
            INNER JOIN EstadoReparacion er
                ON er.idEstadoReparacion = r.idEstadoReparacion
            WHERE r.idActivo = :idActivo
              AND r.fechaFin IS NULL
              AND er.nombreEstado IN (
                    "Pendiente",
                    "En proceso"
              )
            LIMIT 1
            '
        );

        $statement->execute([
            'idActivo' => $assetId,
        ]);

        return $statement->fetchColumn() !== false;
    }

    public function hasOpenRepairRequest(int $assetId): bool
    {
        $statement = $this->connection->prepare(
            '
            SELECT 1
            FROM SolicitudReparacion
            WHERE idActivo = :idActivo
              AND estadoSolicitud IN (
                    "EN_ESPERA",
                    "ASIGNADA",
                    "EN_PROCESO"
              )
            LIMIT 1
            '
        );

        $statement->execute([
            'idActivo' => $assetId,
        ]);

        return $statement->fetchColumn() !== false;
    }

    public function hasActiveLicenseAssignments(int $assetId): bool
    {
        $statement = $this->connection->prepare(
            '
            SELECT 1
            FROM LicenciaSoftware ls
            INNER JOIN AsignacionLicencia al
                ON al.idLicencia = ls.idLicencia
            WHERE ls.idActivo = :idActivo
              AND al.estadoAsignacion = "ACTIVA"
              AND al.fechaRevocacion IS NULL
            LIMIT 1
            '
        );

        $statement->execute([
            'idActivo' => $assetId,
        ]);

        return $statement->fetchColumn() !== false;
    }

    public function create(array $data): int
    {
        $statement = $this->connection->prepare(
            '
            INSERT INTO BajaActivo (
                idActivo,
                idTipoBaja,
                idUsuario,
                motivo,
                opinionTecnica,
                responsableDonacion,
                entidadBeneficiaria,
                documentoReferencia,
                fechaBaja
            ) VALUES (
                :idActivo,
                :idTipoBaja,
                :idUsuario,
                :motivo,
                :opinionTecnica,
                :responsableDonacion,
                :entidadBeneficiaria,
                :documentoReferencia,
                :fechaBaja
            )
            '
        );

        $statement->execute([
            'idActivo' => $data['idActivo'],
            'idTipoBaja' => $data['idTipoBaja'],
            'idUsuario' => $data['idUsuario'],
            'motivo' => $data['motivo'],
            'opinionTecnica' => $data['opinionTecnica'],
            'responsableDonacion' =>
                $data['responsableDonacion'],
            'entidadBeneficiaria' =>
                $data['entidadBeneficiaria'],
            'documentoReferencia' =>
                $data['documentoReferencia'],
            'fechaBaja' => $data['fechaBaja'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function updateAssetState(
        int $assetId,
        int $stateId
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE Activo
            SET idEstadoActivo = :idEstadoActivo
            WHERE idActivo = :idActivo
            '
        );

        $statement->execute([
            'idEstadoActivo' => $stateId,
            'idActivo' => $assetId,
        ]);
    }

    public function insertMovement(array $data): void
    {
        $statement = $this->connection->prepare(
            '
            INSERT INTO MovimientoActivo (
                idActivo,
                idUsuario,
                tipoMovimiento,
                idEstadoAnterior,
                idEstadoNuevo,
                idUbicacionAnterior,
                idUbicacionNueva,
                descripcion
            ) VALUES (
                :idActivo,
                :idUsuario,
                :tipoMovimiento,
                :idEstadoAnterior,
                :idEstadoNuevo,
                :idUbicacionAnterior,
                :idUbicacionNueva,
                :descripcion
            )
            '
        );

        $statement->execute([
            'idActivo' => $data['idActivo'],
            'idUsuario' => $data['idUsuario'],
            'tipoMovimiento' => $data['tipoMovimiento'],
            'idEstadoAnterior' => $data['idEstadoAnterior'],
            'idEstadoNuevo' => $data['idEstadoNuevo'],
            'idUbicacionAnterior' => $data['idUbicacionAnterior'],
            'idUbicacionNueva' => $data['idUbicacionNueva'],
            'descripcion' => $data['descripcion'],
        ]);
    }

    public function beginTransaction(): void
    {
        if (!$this->connection->inTransaction()) {
            $this->connection->beginTransaction();
        }
    }

    public function commit(): void
    {
        if ($this->connection->inTransaction()) {
            $this->connection->commit();
        }
    }

    public function rollBack(): void
    {
        if ($this->connection->inTransaction()) {
            $this->connection->rollBack();
        }
    }
}
