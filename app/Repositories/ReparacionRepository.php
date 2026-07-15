<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\ReparacionRepositoryInterface;
use PDO;

final class ReparacionRepository implements
    ReparacionRepositoryInterface
{
    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection
            ?? Database::getConnection();
    }

    public function listTasks(
        int $userId,
        bool $administrator,
        array $filters = []
    ): array {
        $sql = '
            SELECT
                sr.idSolicitudReparacion,
                sr.estadoSolicitud,
                sr.titulo,
                sr.descripcionFalla,
                sr.prioridad,
                sr.observacionRevision,
                sr.fechaSolicitud,
                sr.fechaAsignacion,
                sr.fechaCierre,
                r.idReparacion,
                r.idEstadoReparacion,
                r.diagnostico,
                r.trabajoRealizado,
                r.costoReparacion,
                r.fechaInicio,
                r.fechaFin,
                r.observaciones,
                er.nombreEstado AS estadoReparacion,
                a.idActivo,
                a.codigoActivo,
                a.numeroSerie,
                a.idEstadoActivo,
                a.idUbicacion,
                p.nombreProducto,
                p.marca,
                p.modelo,
                c.nombre AS colaboradorNombre,
                c.apellido AS colaboradorApellido,
                c.correo AS colaboradorCorreo,
                c.telefono AS colaboradorTelefono,
                c.cargo,
                c.departamento,
                u.nombreUbicacion,
                u.tipoUbicacion,
                u.edificio,
                u.piso,
                u.oficina,
                u.direccion,
                CONCAT_WS(" ", t.nombre, t.apellido)
                    AS tecnicoAsignado
            FROM SolicitudReparacion sr
            INNER JOIN Reparacion r
                ON r.idReparacion = sr.idReparacion
            INNER JOIN EstadoReparacion er
                ON er.idEstadoReparacion = r.idEstadoReparacion
            INNER JOIN Activo a
                ON a.idActivo = sr.idActivo
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN Colaborador c
                ON c.idColaborador = sr.idColaborador
            INNER JOIN Usuario t
                ON t.idUsuario = sr.idTecnico
            LEFT JOIN Ubicacion u
                ON u.idUbicacion = sr.idUbicacionSolicitud
            WHERE sr.idReparacion IS NOT NULL
        ';

        $parameters = [];

        if (!$administrator) {
            $sql .= ' AND sr.idTecnico = :idTecnico ';
            $parameters['idTecnico'] = $userId;
        }

        $search = trim((string) ($filters['search'] ?? ''));
        $status = trim((string) ($filters['status'] ?? ''));

        if ($search !== '') {
            $pattern = '%' . $search . '%';
            $sql .= '
                AND (
                    a.codigoActivo LIKE :searchCode
                    OR a.numeroSerie LIKE :searchSerial
                    OR p.nombreProducto LIKE :searchProduct
                    OR sr.titulo LIKE :searchTitle
                    OR c.nombre LIKE :searchName
                    OR c.apellido LIKE :searchLastName
                    OR CONCAT(c.nombre, " ", c.apellido)
                        LIKE :searchFullName
                )
            ';
            $parameters['searchCode'] = $pattern;
            $parameters['searchSerial'] = $pattern;
            $parameters['searchProduct'] = $pattern;
            $parameters['searchTitle'] = $pattern;
            $parameters['searchName'] = $pattern;
            $parameters['searchLastName'] = $pattern;
            $parameters['searchFullName'] = $pattern;
        }

        if ($status !== '') {
            $sql .= ' AND er.nombreEstado = :filterStatus ';
            $parameters['filterStatus'] = $status;
        }

        $sql .= '
            ORDER BY
                CASE sr.prioridad
                    WHEN "URGENTE" THEN 1
                    WHEN "ALTA" THEN 2
                    WHEN "MEDIA" THEN 3
                    ELSE 4
                END,
                CASE er.nombreEstado
                    WHEN "Pendiente" THEN 1
                    WHEN "En proceso" THEN 2
                    WHEN "No reparable" THEN 3
                    WHEN "Finalizada" THEN 4
                    ELSE 5
                END,
                sr.fechaSolicitud ASC
        ';

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function findTask(
        int $repairRequestId,
        int $userId,
        bool $administrator,
        bool $lock = false
    ): ?array {
        $sql = '
            SELECT
                sr.idSolicitudReparacion,
                sr.idActivo,
                sr.idColaborador,
                sr.idTecnico,
                sr.idReparacion,
                sr.idUbicacionSolicitud,
                sr.estadoSolicitud,
                sr.titulo,
                sr.descripcionFalla,
                sr.prioridad,
                sr.observacionRevision,
                sr.fechaSolicitud,
                sr.fechaAsignacion,
                r.idEstadoReparacion,
                r.diagnostico,
                r.trabajoRealizado,
                r.costoReparacion,
                r.fechaInicio,
                r.fechaFin,
                r.observaciones,
                er.nombreEstado AS estadoReparacion,
                a.codigoActivo,
                a.numeroSerie,
                a.idEstadoActivo,
                a.idUbicacion,
                p.nombreProducto,
                p.marca,
                p.modelo,
                c.nombre AS colaboradorNombre,
                c.apellido AS colaboradorApellido,
                c.correo AS colaboradorCorreo,
                c.telefono AS colaboradorTelefono,
                c.cargo,
                c.departamento,
                u.nombreUbicacion,
                u.tipoUbicacion,
                u.edificio,
                u.piso,
                u.oficina,
                u.direccion
            FROM SolicitudReparacion sr
            INNER JOIN Reparacion r
                ON r.idReparacion = sr.idReparacion
            INNER JOIN EstadoReparacion er
                ON er.idEstadoReparacion = r.idEstadoReparacion
            INNER JOIN Activo a
                ON a.idActivo = sr.idActivo
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN Colaborador c
                ON c.idColaborador = sr.idColaborador
            LEFT JOIN Ubicacion u
                ON u.idUbicacion = sr.idUbicacionSolicitud
            WHERE sr.idSolicitudReparacion = :idSolicitudReparacion
              AND sr.idReparacion IS NOT NULL
        ';

        $parameters = [
            'idSolicitudReparacion' => $repairRequestId,
        ];

        if (!$administrator) {
            $sql .= ' AND sr.idTecnico = :idTecnico ';
            $parameters['idTecnico'] = $userId;
        }

        $sql .= ' LIMIT 1 ';

        if ($lock) {
            $sql .= ' FOR UPDATE ';
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        $task = $statement->fetch();

        return is_array($task) ? $task : null;
    }

    public function listWorkStates(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT idEstadoReparacion, nombreEstado
            FROM EstadoReparacion
            WHERE activo = 1
              AND nombreEstado IN (
                    "Pendiente",
                    "En proceso",
                    "Finalizada",
                    "No reparable"
              )
            ORDER BY FIELD(
                nombreEstado,
                "Pendiente",
                "En proceso",
                "Finalizada",
                "No reparable"
            )
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function findRepairStateById(
        int $stateId
    ): ?array {
        $statement = $this->connection->prepare(
            '
            SELECT
                idEstadoReparacion,
                nombreEstado,
                activo
            FROM EstadoReparacion
            WHERE idEstadoReparacion = :idEstadoReparacion
            LIMIT 1
            '
        );

        $statement->execute([
            'idEstadoReparacion' => $stateId,
        ]);

        $state = $statement->fetch();

        return is_array($state) ? $state : null;
    }

    public function findAssetStateByCode(
        string $code
    ): ?array {
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
            'codigoEstado' => $code,
        ]);

        $state = $statement->fetch();

        return is_array($state) ? $state : null;
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

    public function updateRepair(
        int $repairId,
        array $data
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE Reparacion
            SET
                idEstadoReparacion = :idEstadoReparacion,
                diagnostico = :diagnostico,
                trabajoRealizado = :trabajoRealizado,
                costoReparacion = :costoReparacion,
                fechaFin = :fechaFin,
                observaciones = :observaciones
            WHERE idReparacion = :idReparacion
            '
        );

        $statement->execute([
            'idEstadoReparacion' =>
                $data['idEstadoReparacion'],
            'diagnostico' => $data['diagnostico'],
            'trabajoRealizado' => $data['trabajoRealizado'],
            'costoReparacion' => $data['costoReparacion'],
            'fechaFin' => $data['fechaFin'],
            'observaciones' => $data['observaciones'],
            'idReparacion' => $repairId,
        ]);
    }

    public function updateRepairRequest(
        int $repairRequestId,
        array $data
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE SolicitudReparacion
            SET
                estadoSolicitud = :estadoSolicitud,
                fechaCierre = :fechaCierre
            WHERE idSolicitudReparacion = :idSolicitudReparacion
            '
        );

        $statement->execute([
            'estadoSolicitud' => $data['estadoSolicitud'],
            'fechaCierre' => $data['fechaCierre'],
            'idSolicitudReparacion' => $repairRequestId,
        ]);
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
