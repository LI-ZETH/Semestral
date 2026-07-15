<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\AsignacionRepositoryInterface;
use PDO;

final class AsignacionRepository implements
    AsignacionRepositoryInterface
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
                aa.idAsignacion,
                aa.idActivo,
                aa.idColaborador,
                aa.fechaEntrega,
                aa.fechaDevolucion,
                aa.estadoAsignacion,
                aa.observacionesEntrega,
                a.codigoActivo,
                a.numeroSerie,
                a.idProducto,
                p.nombreProducto,
                p.marca,
                p.modelo,
                ea.codigoEstado,
                ea.nombreEstado,
                c.nombre AS nombreColaborador,
                c.apellido AS apellidoColaborador,
                c.correo AS correoColaborador,
                c.departamento,
                c.cargo,
                u.nombreUbicacion,
                ue.nombre AS nombreUsuarioEntrega,
                ue.apellido AS apellidoUsuarioEntrega,
                d.condicionRecepcion,
                d.fechaRecepcion,
                md.nombreMotivo,
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
            FROM AsignacionActivo aa
            INNER JOIN Activo a
                ON a.idActivo = aa.idActivo
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            INNER JOIN Colaborador c
                ON c.idColaborador = aa.idColaborador
            INNER JOIN Usuario ue
                ON ue.idUsuario = aa.usuarioEntrega
            LEFT JOIN Ubicacion u
                ON u.idUbicacion = a.idUbicacion
            LEFT JOIN DevolucionActivo d
                ON d.idAsignacion = aa.idAsignacion
            LEFT JOIN MotivoDevolucion md
                ON md.idMotivoDevolucion = d.idMotivoDevolucion
            WHERE 1 = 1
        ';

        $parameters = [];
        $search = trim((string) ($filters['search'] ?? ''));
        $status = trim((string) ($filters['status'] ?? ''));

        if ($search !== '') {
            $pattern = '%' . $search . '%';

            $sql .= '
                AND (
                    a.codigoActivo LIKE :searchAssetCode
                    OR COALESCE(a.numeroSerie, "") LIKE :searchSerial
                    OR p.nombreProducto LIKE :searchProduct
                    OR c.nombre LIKE :searchCollaboratorName
                    OR c.apellido LIKE :searchCollaboratorLastName
                    OR CONCAT(c.nombre, " ", c.apellido)
                        LIKE :searchCollaboratorFullName
                    OR c.correo LIKE :searchCollaboratorEmail
                )
            ';

            $parameters['searchAssetCode'] = $pattern;
            $parameters['searchSerial'] = $pattern;
            $parameters['searchProduct'] = $pattern;
            $parameters['searchCollaboratorName'] = $pattern;
            $parameters['searchCollaboratorLastName'] = $pattern;
            $parameters['searchCollaboratorFullName'] = $pattern;
            $parameters['searchCollaboratorEmail'] = $pattern;
        }

        if (
            in_array(
                $status,
                ['ACTIVA', 'DEVUELTA', 'CANCELADA'],
                true
            )
        ) {
            $sql .= '
                AND aa.estadoAsignacion = :filterStatus
            ';
            $parameters['filterStatus'] = $status;
        }

        $sql .= '
            ORDER BY
                CASE aa.estadoAsignacion
                    WHEN "ACTIVA" THEN 1
                    WHEN "DEVUELTA" THEN 2
                    ELSE 3
                END,
                aa.fechaEntrega DESC,
                aa.idAsignacion DESC
        ';

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function listAvailableAssets(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                a.idActivo,
                a.codigoActivo,
                a.numeroSerie,
                a.idUbicacion,
                p.nombreProducto,
                p.marca,
                p.modelo,
                ea.nombreEstado,
                u.nombreUbicacion
            FROM Activo a
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN Subcategoria s
                ON s.idSubcategoria = p.idSubcategoria
            INNER JOIN Categoria c
                ON c.idCategoria = s.idCategoria
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            LEFT JOIN Ubicacion u
                ON u.idUbicacion = a.idUbicacion
            WHERE a.activo = 1
              AND p.activo = 1
              AND s.activo = 1
              AND c.activo = 1
              AND ea.activo = 1
              AND ea.permiteAsignacion = 1
              AND NOT EXISTS (
                    SELECT 1
                    FROM AsignacionActivo aa
                    WHERE aa.idActivo = a.idActivo
                      AND aa.estadoAsignacion = "ACTIVA"
                      AND aa.fechaDevolucion IS NULL
              )
            ORDER BY
                p.nombreProducto ASC,
                a.codigoActivo ASC
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function listActiveCollaborators(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                c.idColaborador,
                c.idUsuario,
                c.identificacion,
                c.nombre,
                c.apellido,
                c.correo,
                c.cargo,
                c.departamento,
                cu.idUbicacion AS idUbicacionActual,
                u.nombreUbicacion AS ubicacionActual
            FROM Colaborador c
            INNER JOIN Usuario us
                ON us.idUsuario = c.idUsuario
            LEFT JOIN ColaboradorUbicacion cu
                ON cu.idColaborador = c.idColaborador
               AND cu.esActual = 1
               AND cu.fechaFin IS NULL
            LEFT JOIN Ubicacion u
                ON u.idUbicacion = cu.idUbicacion
            WHERE c.activo = 1
              AND us.activo = 1
            ORDER BY
                c.apellido ASC,
                c.nombre ASC
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function listActiveLocations(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                idUbicacion,
                nombreUbicacion,
                tipoUbicacion,
                edificio,
                piso,
                oficina,
                direccion
            FROM Ubicacion
            WHERE activo = 1
            ORDER BY nombreUbicacion ASC
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function listReturnReasons(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                idMotivoDevolucion,
                nombreMotivo
            FROM MotivoDevolucion
            WHERE activo = 1
            ORDER BY nombreMotivo ASC
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function listReturnStates(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                idEstadoActivo,
                codigoEstado,
                nombreEstado
            FROM EstadoActivo
            WHERE activo = 1
              AND codigoEstado IN (
                    "EN_INVENTARIO",
                    "REVISION_TECNICA",
                    "EN_REPARACION"
              )
            ORDER BY
                CASE codigoEstado
                    WHEN "EN_INVENTARIO" THEN 1
                    WHEN "REVISION_TECNICA" THEN 2
                    WHEN "EN_REPARACION" THEN 3
                    ELSE 4
                END
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function findAssetForAssignment(
        int $assetId,
        bool $lock = false
    ): ?array {
        $sql = '
            SELECT
                a.idActivo,
                a.idProducto,
                a.codigoActivo,
                a.numeroSerie,
                a.idEstadoActivo,
                a.idUbicacion,
                a.activo,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.activo AS productoActivo,
                s.activo AS subcategoriaActiva,
                c.activo AS categoriaActiva,
                ea.codigoEstado,
                ea.nombreEstado,
                ea.permiteAsignacion,
                ea.activo AS estadoActivo,
                u.nombreUbicacion
            FROM Activo a
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN Subcategoria s
                ON s.idSubcategoria = p.idSubcategoria
            INNER JOIN Categoria c
                ON c.idCategoria = s.idCategoria
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            LEFT JOIN Ubicacion u
                ON u.idUbicacion = a.idUbicacion
            WHERE a.idActivo = :idActivo
            LIMIT 1
        ';

        if ($lock) {
            $sql .= ' FOR UPDATE ';
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            'idActivo' => $assetId,
        ]);

        $asset = $statement->fetch();

        return is_array($asset) ? $asset : null;
    }

    public function findCollaborator(int $collaboratorId): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                c.idColaborador,
                c.idUsuario,
                c.identificacion,
                c.nombre,
                c.apellido,
                c.correo,
                c.cargo,
                c.departamento,
                c.activo,
                us.activo AS usuarioActivo,
                cu.idUbicacion AS idUbicacionActual,
                u.nombreUbicacion AS ubicacionActual
            FROM Colaborador c
            INNER JOIN Usuario us
                ON us.idUsuario = c.idUsuario
            LEFT JOIN ColaboradorUbicacion cu
                ON cu.idColaborador = c.idColaborador
               AND cu.esActual = 1
               AND cu.fechaFin IS NULL
            LEFT JOIN Ubicacion u
                ON u.idUbicacion = cu.idUbicacion
            WHERE c.idColaborador = :idColaborador
            LIMIT 1
            '
        );

        $statement->execute([
            'idColaborador' => $collaboratorId,
        ]);

        $collaborator = $statement->fetch();

        return is_array($collaborator) ? $collaborator : null;
    }

    public function findLocation(int $locationId): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                idUbicacion,
                nombreUbicacion,
                activo
            FROM Ubicacion
            WHERE idUbicacion = :idUbicacion
            LIMIT 1
            '
        );

        $statement->execute([
            'idUbicacion' => $locationId,
        ]);

        $location = $statement->fetch();

        return is_array($location) ? $location : null;
    }

    public function findStateByCode(string $code): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                idEstadoActivo,
                codigoEstado,
                nombreEstado,
                permiteAsignacion,
                activo
            FROM EstadoActivo
            WHERE codigoEstado = :codigoEstado
            LIMIT 1
            '
        );

        $statement->execute([
            'codigoEstado' => $code,
        ]);

        $state = $statement->fetch();

        return is_array($state) ? $state : null;
    }

    public function findStateById(int $stateId): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                idEstadoActivo,
                codigoEstado,
                nombreEstado,
                permiteAsignacion,
                activo
            FROM EstadoActivo
            WHERE idEstadoActivo = :idEstadoActivo
            LIMIT 1
            '
        );

        $statement->execute([
            'idEstadoActivo' => $stateId,
        ]);

        $state = $statement->fetch();

        return is_array($state) ? $state : null;
    }

    public function findReturnReason(int $reasonId): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                idMotivoDevolucion,
                nombreMotivo,
                activo
            FROM MotivoDevolucion
            WHERE idMotivoDevolucion = :idMotivoDevolucion
            LIMIT 1
            '
        );

        $statement->execute([
            'idMotivoDevolucion' => $reasonId,
        ]);

        $reason = $statement->fetch();

        return is_array($reason) ? $reason : null;
    }

    public function findActiveAssignment(
        int $assignmentId,
        bool $lock = false
    ): ?array {
        $sql = '
            SELECT
                aa.idAsignacion,
                aa.idActivo,
                aa.idColaborador,
                aa.fechaEntrega,
                aa.estadoAsignacion,
                aa.observacionesEntrega,
                a.codigoActivo,
                a.numeroSerie,
                a.idEstadoActivo,
                a.idUbicacion,
                a.activo AS activoHabilitado,
                p.nombreProducto,
                p.marca,
                p.modelo,
                ea.codigoEstado,
                ea.nombreEstado,
                c.nombre AS nombreColaborador,
                c.apellido AS apellidoColaborador,
                c.correo AS correoColaborador,
                u.nombreUbicacion
            FROM AsignacionActivo aa
            INNER JOIN Activo a
                ON a.idActivo = aa.idActivo
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            INNER JOIN Colaborador c
                ON c.idColaborador = aa.idColaborador
            LEFT JOIN Ubicacion u
                ON u.idUbicacion = a.idUbicacion
            WHERE aa.idAsignacion = :idAsignacion
              AND aa.estadoAsignacion = "ACTIVA"
              AND aa.fechaDevolucion IS NULL
            LIMIT 1
        ';

        if ($lock) {
            $sql .= ' FOR UPDATE ';
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            'idAsignacion' => $assignmentId,
        ]);

        $assignment = $statement->fetch();

        return is_array($assignment) ? $assignment : null;
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

    public function createAssignment(array $data): int
    {
        $statement = $this->connection->prepare(
            '
            INSERT INTO AsignacionActivo (
                idActivo,
                idColaborador,
                usuarioEntrega,
                estadoAsignacion,
                observacionesEntrega
            ) VALUES (
                :idActivo,
                :idColaborador,
                :usuarioEntrega,
                "ACTIVA",
                :observacionesEntrega
            )
            '
        );

        $statement->execute([
            'idActivo' => $data['idActivo'],
            'idColaborador' => $data['idColaborador'],
            'usuarioEntrega' => $data['usuarioEntrega'],
            'observacionesEntrega' =>
                $data['observacionesEntrega'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function updateAssetStateAndLocation(
        int $assetId,
        int $stateId,
        int $locationId
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE Activo
            SET
                idEstadoActivo = :idEstadoActivo,
                idUbicacion = :idUbicacion
            WHERE idActivo = :idActivo
            '
        );

        $statement->execute([
            'idEstadoActivo' => $stateId,
            'idUbicacion' => $locationId,
            'idActivo' => $assetId,
        ]);
    }

    public function setCollaboratorCurrentLocation(
        int $collaboratorId,
        int $locationId,
        ?string $observations = null
    ): void {
        $closeStatement = $this->connection->prepare(
            '
            UPDATE ColaboradorUbicacion
            SET
                esActual = 0,
                fechaFin = CURRENT_TIMESTAMP
            WHERE idColaborador = :idColaborador
              AND esActual = 1
              AND fechaFin IS NULL
            '
        );

        $closeStatement->execute([
            'idColaborador' => $collaboratorId,
        ]);

        $insertStatement = $this->connection->prepare(
            '
            INSERT INTO ColaboradorUbicacion (
                idColaborador,
                idUbicacion,
                esActual,
                observaciones
            ) VALUES (
                :idColaborador,
                :idUbicacion,
                1,
                :observaciones
            )
            '
        );

        $insertStatement->execute([
            'idColaborador' => $collaboratorId,
            'idUbicacion' => $locationId,
            'observaciones' => $observations,
        ]);
    }

    public function createReturn(array $data): int
    {
        $statement = $this->connection->prepare(
            '
            INSERT INTO DevolucionActivo (
                idAsignacion,
                usuarioRecibe,
                idMotivoDevolucion,
                condicionRecepcion,
                observaciones
            ) VALUES (
                :idAsignacion,
                :usuarioRecibe,
                :idMotivoDevolucion,
                :condicionRecepcion,
                :observaciones
            )
            '
        );

        $statement->execute([
            'idAsignacion' => $data['idAsignacion'],
            'usuarioRecibe' => $data['usuarioRecibe'],
            'idMotivoDevolucion' =>
                $data['idMotivoDevolucion'],
            'condicionRecepcion' =>
                $data['condicionRecepcion'],
            'observaciones' => $data['observaciones'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function completeAssignment(int $assignmentId): void
    {
        $statement = $this->connection->prepare(
            '
            UPDATE AsignacionActivo
            SET
                estadoAsignacion = "DEVUELTA",
                fechaDevolucion = CURRENT_TIMESTAMP
            WHERE idAsignacion = :idAsignacion
              AND estadoAsignacion = "ACTIVA"
            '
        );

        $statement->execute([
            'idAsignacion' => $assignmentId,
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

    public function listMyActiveAssignments(int $userId): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                aa.idAsignacion,
                aa.fechaEntrega,
                aa.observacionesEntrega,
                a.idActivo,
                a.codigoActivo,
                a.numeroSerie,
                a.direccionIP,
                a.qrToken,
                p.nombreProducto,
                p.marca,
                p.modelo,
                ea.nombreEstado,
                u.nombreUbicacion,
                u.edificio,
                u.piso,
                u.oficina,
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
            FROM Colaborador c
            INNER JOIN AsignacionActivo aa
                ON aa.idColaborador = c.idColaborador
            INNER JOIN Activo a
                ON a.idActivo = aa.idActivo
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            LEFT JOIN Ubicacion u
                ON u.idUbicacion = a.idUbicacion
            WHERE c.idUsuario = :idUsuario
              AND c.activo = 1
              AND aa.estadoAsignacion = "ACTIVA"
              AND aa.fechaDevolucion IS NULL
            ORDER BY aa.fechaEntrega DESC
            '
        );

        $statement->execute([
            'idUsuario' => $userId,
        ]);

        return $statement->fetchAll();
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
