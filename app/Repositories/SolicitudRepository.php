<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\SolicitudRepositoryInterface;
use PDO;

final class SolicitudRepository implements
    SolicitudRepositoryInterface
{
    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection
            ?? Database::getConnection();
    }

    public function listMyNeedRequests(int $userId): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                sn.idSolicitud,
                sn.tipoSolicitud,
                sn.titulo,
                sn.descripcionNecesidad,
                sn.justificacion,
                sn.cantidad,
                sn.prioridad,
                sn.periodoNecesidad,
                sn.anioPresupuestado,
                sn.costoEstimado,
                sn.observacionRevision,
                sn.fechaSolicitud,
                sn.fechaRevision,
                es.nombreEstado,
                s.nombreSubcategoria,
                p.nombreProducto,
                p.marca,
                p.modelo
            FROM SolicitudNecesidad sn
            INNER JOIN Colaborador c
                ON c.idColaborador = sn.idColaborador
            INNER JOIN EstadoSolicitud es
                ON es.idEstadoSolicitud = sn.idEstadoSolicitud
            LEFT JOIN Subcategoria s
                ON s.idSubcategoria = sn.idSubcategoria
            LEFT JOIN Producto p
                ON p.idProducto = sn.idProducto
            WHERE c.idUsuario = :idUsuario
            ORDER BY sn.fechaSolicitud DESC, sn.idSolicitud DESC
            '
        );

        $statement->execute([
            'idUsuario' => $userId,
        ]);

        return $statement->fetchAll();
    }

    public function listMyRepairRequests(int $userId): array
    {
        $statement = $this->connection->prepare(
            '
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
                a.idActivo,
                a.codigoActivo,
                a.numeroSerie,
                p.nombreProducto,
                p.marca,
                p.modelo,
                u.nombreUbicacion,
                u.edificio,
                u.piso,
                u.oficina,
                CONCAT_WS(" ", t.nombre, t.apellido) AS tecnicoAsignado,
                er.nombreEstado AS estadoReparacion
            FROM SolicitudReparacion sr
            INNER JOIN Colaborador c
                ON c.idColaborador = sr.idColaborador
            INNER JOIN Activo a
                ON a.idActivo = sr.idActivo
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            LEFT JOIN Ubicacion u
                ON u.idUbicacion = sr.idUbicacionSolicitud
            LEFT JOIN Usuario t
                ON t.idUsuario = sr.idTecnico
            LEFT JOIN Reparacion r
                ON r.idReparacion = sr.idReparacion
            LEFT JOIN EstadoReparacion er
                ON er.idEstadoReparacion = r.idEstadoReparacion
            WHERE c.idUsuario = :idUsuario
            ORDER BY sr.fechaSolicitud DESC,
                sr.idSolicitudReparacion DESC
            '
        );

        $statement->execute([
            'idUsuario' => $userId,
        ]);

        return $statement->fetchAll();
    }

    public function listActiveSubcategories(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                s.idSubcategoria,
                s.nombreSubcategoria,
                c.nombreCategoria
            FROM Subcategoria s
            INNER JOIN Categoria c
                ON c.idCategoria = s.idCategoria
            WHERE s.activo = 1
              AND c.activo = 1
            ORDER BY c.nombreCategoria, s.nombreSubcategoria
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function listActiveProducts(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                p.idProducto,
                p.idSubcategoria,
                p.nombreProducto,
                p.marca,
                p.modelo,
                s.nombreSubcategoria,
                c.nombreCategoria
            FROM Producto p
            INNER JOIN Subcategoria s
                ON s.idSubcategoria = p.idSubcategoria
            INNER JOIN Categoria c
                ON c.idCategoria = s.idCategoria
            WHERE p.activo = 1
              AND s.activo = 1
              AND c.activo = 1
            ORDER BY
                c.nombreCategoria,
                s.nombreSubcategoria,
                p.nombreProducto,
                p.marca,
                p.modelo
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function listMyAssignedAssets(int $userId): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                a.idActivo,
                a.codigoActivo,
                a.numeroSerie,
                p.nombreProducto,
                p.marca,
                p.modelo,
                ea.codigoEstado,
                ea.nombreEstado,
                u.nombreUbicacion
            FROM AsignacionActivo aa
            INNER JOIN Colaborador c
                ON c.idColaborador = aa.idColaborador
            INNER JOIN Activo a
                ON a.idActivo = aa.idActivo
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            LEFT JOIN Ubicacion u
                ON u.idUbicacion = a.idUbicacion
            WHERE c.idUsuario = :idUsuario
              AND aa.estadoAsignacion = "ACTIVA"
              AND aa.fechaDevolucion IS NULL
              AND a.activo = 1
            ORDER BY p.nombreProducto, a.codigoActivo
            '
        );

        $statement->execute([
            'idUsuario' => $userId,
        ]);

        return $statement->fetchAll();
    }

    public function findCollaboratorByUserId(
        int $userId
    ): ?array {
        $statement = $this->connection->prepare(
            '
            SELECT
                c.idColaborador,
                c.idUsuario,
                c.nombre,
                c.apellido,
                c.correo,
                c.activo,
                u.activo AS usuarioActivo
            FROM Colaborador c
            INNER JOIN Usuario u
                ON u.idUsuario = c.idUsuario
            WHERE c.idUsuario = :idUsuario
            LIMIT 1
            '
        );

        $statement->execute([
            'idUsuario' => $userId,
        ]);

        $collaborator = $statement->fetch();

        return is_array($collaborator)
            ? $collaborator
            : null;
    }

    public function findNeedStateByName(
        string $name
    ): ?array {
        $statement = $this->connection->prepare(
            '
            SELECT
                idEstadoSolicitud,
                nombreEstado,
                activo
            FROM EstadoSolicitud
            WHERE nombreEstado = :nombreEstado
            LIMIT 1
            '
        );

        $statement->execute([
            'nombreEstado' => $name,
        ]);

        $state = $statement->fetch();

        return is_array($state) ? $state : null;
    }

    public function createNeedRequest(array $data): int
    {
        $statement = $this->connection->prepare(
            '
            INSERT INTO SolicitudNecesidad (
                idColaborador,
                idSubcategoria,
                idProducto,
                idEstadoSolicitud,
                tipoSolicitud,
                titulo,
                descripcionNecesidad,
                justificacion,
                cantidad,
                prioridad,
                periodoNecesidad,
                anioPresupuestado
            ) VALUES (
                :idColaborador,
                :idSubcategoria,
                :idProducto,
                :idEstadoSolicitud,
                :tipoSolicitud,
                :titulo,
                :descripcionNecesidad,
                :justificacion,
                :cantidad,
                :prioridad,
                :periodoNecesidad,
                :anioPresupuestado
            )
            '
        );

        $statement->execute([
            'idColaborador' => $data['idColaborador'],
            'idSubcategoria' => $data['idSubcategoria'],
            'idProducto' => $data['idProducto'],
            'idEstadoSolicitud' => $data['idEstadoSolicitud'],
            'tipoSolicitud' => $data['tipoSolicitud'],
            'titulo' => $data['titulo'],
            'descripcionNecesidad' =>
                $data['descripcionNecesidad'],
            'justificacion' => $data['justificacion'],
            'cantidad' => $data['cantidad'],
            'prioridad' => $data['prioridad'],
            'periodoNecesidad' => $data['periodoNecesidad'],
            'anioPresupuestado' => $data['anioPresupuestado'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function findAssignedAssetForUser(
        int $assetId,
        int $userId
    ): ?array {
        $statement = $this->connection->prepare(
            '
            SELECT
                a.idActivo,
                a.codigoActivo,
                a.numeroSerie,
                a.idEstadoActivo,
                a.idUbicacion,
                a.activo,
                p.nombreProducto,
                p.marca,
                p.modelo,
                c.idColaborador,
                c.nombre,
                c.apellido,
                c.activo AS colaboradorActivo,
                aa.idAsignacion
            FROM AsignacionActivo aa
            INNER JOIN Colaborador c
                ON c.idColaborador = aa.idColaborador
            INNER JOIN Activo a
                ON a.idActivo = aa.idActivo
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            WHERE a.idActivo = :idActivo
              AND c.idUsuario = :idUsuario
              AND aa.estadoAsignacion = "ACTIVA"
              AND aa.fechaDevolucion IS NULL
            LIMIT 1
            '
        );

        $statement->execute([
            'idActivo' => $assetId,
            'idUsuario' => $userId,
        ]);

        $asset = $statement->fetch();

        return is_array($asset) ? $asset : null;
    }

    public function findCurrentLocationForCollaborator(
        int $collaboratorId
    ): ?array {
        $statement = $this->connection->prepare(
            '
            SELECT
                u.idUbicacion,
                u.nombreUbicacion,
                u.edificio,
                u.piso,
                u.oficina,
                u.direccion
            FROM ColaboradorUbicacion cu
            INNER JOIN Ubicacion u
                ON u.idUbicacion = cu.idUbicacion
            WHERE cu.idColaborador = :idColaborador
              AND cu.esActual = 1
              AND cu.fechaFin IS NULL
              AND u.activo = 1
            ORDER BY cu.fechaInicio DESC
            LIMIT 1
            '
        );

        $statement->execute([
            'idColaborador' => $collaboratorId,
        ]);

        $location = $statement->fetch();

        return is_array($location) ? $location : null;
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

    public function createRepairRequest(array $data): int
    {
        $statement = $this->connection->prepare(
            '
            INSERT INTO SolicitudReparacion (
                idActivo,
                idColaborador,
                idUbicacionSolicitud,
                estadoSolicitud,
                titulo,
                descripcionFalla,
                prioridad
            ) VALUES (
                :idActivo,
                :idColaborador,
                :idUbicacionSolicitud,
                "EN_ESPERA",
                :titulo,
                :descripcionFalla,
                :prioridad
            )
            '
        );

        $statement->execute([
            'idActivo' => $data['idActivo'],
            'idColaborador' => $data['idColaborador'],
            'idUbicacionSolicitud' =>
                $data['idUbicacionSolicitud'],
            'titulo' => $data['titulo'],
            'descripcionFalla' => $data['descripcionFalla'],
            'prioridad' => $data['prioridad'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function cancelOwnNeedRequest(
        int $requestId,
        int $userId
    ): bool {
        $cancelState = $this->findNeedStateByName(
            'Cancelada'
        );

        if ($cancelState === null) {
            return false;
        }

        $statement = $this->connection->prepare(
            '
            UPDATE SolicitudNecesidad sn
            INNER JOIN Colaborador c
                ON c.idColaborador = sn.idColaborador
            INNER JOIN EstadoSolicitud currentState
                ON currentState.idEstadoSolicitud = sn.idEstadoSolicitud
            SET
                sn.idEstadoSolicitud = :cancelStateId,
                sn.fechaRevision = CURRENT_TIMESTAMP
            WHERE sn.idSolicitud = :idSolicitud
              AND c.idUsuario = :idUsuario
              AND currentState.nombreEstado = "En espera"
            '
        );

        $statement->execute([
            'cancelStateId' =>
                (int) $cancelState['idEstadoSolicitud'],
            'idSolicitud' => $requestId,
            'idUsuario' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }

    public function cancelOwnRepairRequest(
        int $requestId,
        int $userId
    ): bool {
        $statement = $this->connection->prepare(
            '
            UPDATE SolicitudReparacion sr
            INNER JOIN Colaborador c
                ON c.idColaborador = sr.idColaborador
            SET
                sr.estadoSolicitud = "CANCELADA",
                sr.fechaCierre = CURRENT_TIMESTAMP
            WHERE sr.idSolicitudReparacion = :idSolicitudReparacion
              AND c.idUsuario = :idUsuario
              AND sr.estadoSolicitud = "EN_ESPERA"
            '
        );

        $statement->execute([
            'idSolicitudReparacion' => $requestId,
            'idUsuario' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }

    public function listAllNeedRequests(
        array $filters = []
    ): array {
        $sql = '
            SELECT
                sn.idSolicitud,
                sn.tipoSolicitud,
                sn.titulo,
                sn.cantidad,
                sn.prioridad,
                sn.periodoNecesidad,
                sn.costoEstimado,
                sn.fechaSolicitud,
                sn.fechaRevision,
                es.nombreEstado,
                c.nombre,
                c.apellido,
                c.correo,
                s.nombreSubcategoria,
                p.nombreProducto,
                p.marca,
                p.modelo
            FROM SolicitudNecesidad sn
            INNER JOIN Colaborador c
                ON c.idColaborador = sn.idColaborador
            INNER JOIN EstadoSolicitud es
                ON es.idEstadoSolicitud = sn.idEstadoSolicitud
            LEFT JOIN Subcategoria s
                ON s.idSubcategoria = sn.idSubcategoria
            LEFT JOIN Producto p
                ON p.idProducto = sn.idProducto
            WHERE 1 = 1
        ';

        $parameters = [];
        $search = trim((string) ($filters['search'] ?? ''));
        $status = trim((string) ($filters['status'] ?? ''));

        if ($search !== '') {
            $pattern = '%' . $search . '%';
            $sql .= '
                AND (
                    sn.titulo LIKE :searchTitle
                    OR c.nombre LIKE :searchName
                    OR c.apellido LIKE :searchLastName
                    OR CONCAT(c.nombre, " ", c.apellido)
                        LIKE :searchFullName
                    OR c.correo LIKE :searchEmail
                )
            ';
            $parameters['searchTitle'] = $pattern;
            $parameters['searchName'] = $pattern;
            $parameters['searchLastName'] = $pattern;
            $parameters['searchFullName'] = $pattern;
            $parameters['searchEmail'] = $pattern;
        }

        if ($status !== '') {
            $sql .= ' AND es.nombreEstado = :filterStatus ';
            $parameters['filterStatus'] = $status;
        }

        $sql .= '
            ORDER BY
                CASE sn.prioridad
                    WHEN "URGENTE" THEN 1
                    WHEN "ALTA" THEN 2
                    WHEN "MEDIA" THEN 3
                    ELSE 4
                END,
                sn.fechaSolicitud DESC
        ';

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function listAllRepairRequests(
        array $filters = []
    ): array {
        $sql = '
            SELECT
                sr.idSolicitudReparacion,
                sr.estadoSolicitud,
                sr.titulo,
                sr.prioridad,
                sr.fechaSolicitud,
                sr.fechaAsignacion,
                sr.fechaCierre,
                a.codigoActivo,
                a.numeroSerie,
                p.nombreProducto,
                p.marca,
                p.modelo,
                c.nombre,
                c.apellido,
                c.correo,
                u.nombreUbicacion,
                u.edificio,
                u.piso,
                u.oficina,
                CONCAT_WS(" ", t.nombre, t.apellido)
                    AS tecnicoAsignado,
                er.nombreEstado AS estadoReparacion
            FROM SolicitudReparacion sr
            INNER JOIN Activo a
                ON a.idActivo = sr.idActivo
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN Colaborador c
                ON c.idColaborador = sr.idColaborador
            LEFT JOIN Ubicacion u
                ON u.idUbicacion = sr.idUbicacionSolicitud
            LEFT JOIN Usuario t
                ON t.idUsuario = sr.idTecnico
            LEFT JOIN Reparacion r
                ON r.idReparacion = sr.idReparacion
            LEFT JOIN EstadoReparacion er
                ON er.idEstadoReparacion = r.idEstadoReparacion
            WHERE 1 = 1
        ';

        $parameters = [];
        $search = trim((string) ($filters['repairSearch'] ?? ''));
        $status = trim((string) ($filters['repairStatus'] ?? ''));

        if ($search !== '') {
            $pattern = '%' . $search . '%';
            $sql .= '
                AND (
                    sr.titulo LIKE :repairSearchTitle
                    OR a.codigoActivo LIKE :repairSearchCode
                    OR p.nombreProducto LIKE :repairSearchProduct
                    OR c.nombre LIKE :repairSearchName
                    OR c.apellido LIKE :repairSearchLastName
                    OR CONCAT(c.nombre, " ", c.apellido)
                        LIKE :repairSearchFullName
                )
            ';
            $parameters['repairSearchTitle'] = $pattern;
            $parameters['repairSearchCode'] = $pattern;
            $parameters['repairSearchProduct'] = $pattern;
            $parameters['repairSearchName'] = $pattern;
            $parameters['repairSearchLastName'] = $pattern;
            $parameters['repairSearchFullName'] = $pattern;
        }

        if ($status !== '') {
            $sql .= '
                AND sr.estadoSolicitud = :repairFilterStatus
            ';
            $parameters['repairFilterStatus'] = $status;
        }

        $sql .= '
            ORDER BY
                CASE sr.prioridad
                    WHEN "URGENTE" THEN 1
                    WHEN "ALTA" THEN 2
                    WHEN "MEDIA" THEN 3
                    ELSE 4
                END,
                sr.fechaSolicitud DESC
        ';

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function findNeedRequest(
        int $requestId
    ): ?array {
        $statement = $this->connection->prepare(
            '
            SELECT
                sn.*,
                es.nombreEstado,
                c.nombre,
                c.apellido,
                c.correo,
                c.cargo,
                c.departamento,
                s.nombreSubcategoria,
                p.nombreProducto,
                p.marca,
                p.modelo
            FROM SolicitudNecesidad sn
            INNER JOIN EstadoSolicitud es
                ON es.idEstadoSolicitud = sn.idEstadoSolicitud
            INNER JOIN Colaborador c
                ON c.idColaborador = sn.idColaborador
            LEFT JOIN Subcategoria s
                ON s.idSubcategoria = sn.idSubcategoria
            LEFT JOIN Producto p
                ON p.idProducto = sn.idProducto
            WHERE sn.idSolicitud = :idSolicitud
            LIMIT 1
            '
        );

        $statement->execute([
            'idSolicitud' => $requestId,
        ]);

        $request = $statement->fetch();

        return is_array($request) ? $request : null;
    }

    public function listNeedReviewStates(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT idEstadoSolicitud, nombreEstado
            FROM EstadoSolicitud
            WHERE activo = 1
              AND nombreEstado IN (
                    "En trámite",
                    "Aprobada",
                    "Rechazada",
                    "Atendida"
              )
            ORDER BY FIELD(
                nombreEstado,
                "En trámite",
                "Aprobada",
                "Rechazada",
                "Atendida"
            )
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function updateNeedReview(
        int $requestId,
        array $data
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE SolicitudNecesidad
            SET
                idEstadoSolicitud = :idEstadoSolicitud,
                costoEstimado = :costoEstimado,
                usuarioRevisa = :usuarioRevisa,
                observacionRevision = :observacionRevision,
                fechaRevision = CURRENT_TIMESTAMP
            WHERE idSolicitud = :idSolicitud
            '
        );

        $statement->execute([
            'idEstadoSolicitud' => $data['idEstadoSolicitud'],
            'costoEstimado' => $data['costoEstimado'],
            'usuarioRevisa' => $data['usuarioRevisa'],
            'observacionRevision' =>
                $data['observacionRevision'],
            'idSolicitud' => $requestId,
        ]);
    }

    public function listActiveTechnicians(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                u.idUsuario,
                u.nombre,
                u.apellido,
                u.correo
            FROM Usuario u
            INNER JOIN Rol r
                ON r.idRol = u.idRol
            WHERE u.activo = 1
              AND u.bloqueado = 0
              AND r.activo = 1
              AND r.nombreRol = "Técnico"
            ORDER BY u.apellido, u.nombre
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function findRepairRequestForAssignment(
        int $requestId,
        bool $lock = false
    ): ?array {
        $sql = '
            SELECT
                sr.*,
                a.codigoActivo,
                a.numeroSerie,
                a.idEstadoActivo,
                a.idUbicacion,
                p.nombreProducto,
                p.marca,
                p.modelo,
                c.nombre,
                c.apellido,
                c.correo,
                u.nombreUbicacion,
                u.edificio,
                u.piso,
                u.oficina
            FROM SolicitudReparacion sr
            INNER JOIN Activo a
                ON a.idActivo = sr.idActivo
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN Colaborador c
                ON c.idColaborador = sr.idColaborador
            LEFT JOIN Ubicacion u
                ON u.idUbicacion = sr.idUbicacionSolicitud
            WHERE sr.idSolicitudReparacion = :idSolicitudReparacion
            LIMIT 1
        ';

        if ($lock) {
            $sql .= ' FOR UPDATE ';
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            'idSolicitudReparacion' => $requestId,
        ]);

        $request = $statement->fetch();

        return is_array($request) ? $request : null;
    }

    public function findTechnician(int $userId): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                u.idUsuario,
                u.nombre,
                u.apellido,
                u.correo,
                u.activo,
                u.bloqueado,
                r.nombreRol,
                r.activo AS rolActivo
            FROM Usuario u
            INNER JOIN Rol r
                ON r.idRol = u.idRol
            WHERE u.idUsuario = :idUsuario
              AND r.nombreRol = "Técnico"
            LIMIT 1
            '
        );

        $statement->execute([
            'idUsuario' => $userId,
        ]);

        $technician = $statement->fetch();

        return is_array($technician)
            ? $technician
            : null;
    }

    public function findRepairStateByName(
        string $name
    ): ?array {
        $statement = $this->connection->prepare(
            '
            SELECT
                idEstadoReparacion,
                nombreEstado,
                activo
            FROM EstadoReparacion
            WHERE nombreEstado = :nombreEstado
            LIMIT 1
            '
        );

        $statement->execute([
            'nombreEstado' => $name,
        ]);

        $state = $statement->fetch();

        return is_array($state) ? $state : null;
    }

    public function createRepair(array $data): int
    {
        $statement = $this->connection->prepare(
            '
            INSERT INTO Reparacion (
                idActivo,
                idTecnico,
                idEstadoReparacion,
                descripcionFalla,
                observaciones
            ) VALUES (
                :idActivo,
                :idTecnico,
                :idEstadoReparacion,
                :descripcionFalla,
                :observaciones
            )
            '
        );

        $statement->execute([
            'idActivo' => $data['idActivo'],
            'idTecnico' => $data['idTecnico'],
            'idEstadoReparacion' =>
                $data['idEstadoReparacion'],
            'descripcionFalla' => $data['descripcionFalla'],
            'observaciones' => $data['observaciones'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function assignRepairRequest(
        int $requestId,
        array $data
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE SolicitudReparacion
            SET
                idTecnico = :idTecnico,
                idReparacion = :idReparacion,
                usuarioRevisa = :usuarioRevisa,
                estadoSolicitud = "ASIGNADA",
                observacionRevision = :observacionRevision,
                fechaAsignacion = CURRENT_TIMESTAMP
            WHERE idSolicitudReparacion = :idSolicitudReparacion
            '
        );

        $statement->execute([
            'idTecnico' => $data['idTecnico'],
            'idReparacion' => $data['idReparacion'],
            'usuarioRevisa' => $data['usuarioRevisa'],
            'observacionRevision' =>
                $data['observacionRevision'],
            'idSolicitudReparacion' => $requestId,
        ]);
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
