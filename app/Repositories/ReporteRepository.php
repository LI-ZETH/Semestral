<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\ReporteRepositoryInterface;
use PDO;

final class ReporteRepository implements ReporteRepositoryInterface
{
    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection
            ?? Database::getConnection();
    }

    public function getDashboardSummary(): array
    {
        $statement = $this->connection->query(
            '
            SELECT
                (
                    SELECT COUNT(*)
                    FROM Activo
                    WHERE activo = 1
                ) AS totalActivos,

                (
                    SELECT COUNT(*)
                    FROM Activo a
                    INNER JOIN EstadoActivo ea
                        ON ea.idEstadoActivo = a.idEstadoActivo
                    WHERE a.activo = 1
                      AND ea.codigoEstado = "EN_INVENTARIO"
                ) AS disponibles,

                (
                    SELECT COUNT(*)
                    FROM Activo a
                    INNER JOIN EstadoActivo ea
                        ON ea.idEstadoActivo = a.idEstadoActivo
                    WHERE a.activo = 1
                      AND ea.codigoEstado = "ASIGNADO"
                ) AS asignados,

                (
                    SELECT COUNT(*)
                    FROM Activo a
                    INNER JOIN EstadoActivo ea
                        ON ea.idEstadoActivo = a.idEstadoActivo
                    WHERE a.activo = 1
                      AND ea.codigoEstado IN (
                          "REVISION_TECNICA",
                          "EN_REPARACION"
                      )
                ) AS servicioTecnico,

                (
                    SELECT COUNT(*)
                    FROM Activo a
                    INNER JOIN EstadoActivo ea
                        ON ea.idEstadoActivo = a.idEstadoActivo
                    WHERE a.activo = 1
                      AND ea.codigoEstado IN (
                          "DESCARTE",
                          "DONADO"
                      )
                ) AS bajas,

                (
                    SELECT COALESCE(SUM(costo), 0)
                    FROM Activo
                    WHERE activo = 1
                ) AS valorInventario,

                (
                    SELECT COUNT(*)
                    FROM Usuario
                    WHERE activo = 1
                ) AS usuariosActivos,

                (
                    SELECT COUNT(*)
                    FROM Colaborador
                    WHERE activo = 1
                ) AS colaboradoresActivos,

                (
                    SELECT COUNT(*)
                    FROM SolicitudNecesidad sn
                    INNER JOIN EstadoSolicitud es
                        ON es.idEstadoSolicitud = sn.idEstadoSolicitud
                    WHERE es.nombreEstado IN (
                        "En espera",
                        "En trámite"
                    )
                ) AS solicitudesPendientes,

                (
                    SELECT COUNT(*)
                    FROM Reparacion r
                    INNER JOIN EstadoReparacion er
                        ON er.idEstadoReparacion = r.idEstadoReparacion
                    WHERE er.nombreEstado IN (
                        "Pendiente",
                        "En proceso"
                    )
                ) AS reparacionesAbiertas
            '
        );

        $summary = $statement->fetch();

        return is_array($summary)
            ? $summary
            : [];
    }

    public function getCategorySummary(): array
    {
        $statement = $this->connection->query(
            '
            SELECT
                c.idCategoria,
                c.nombreCategoria,
                COUNT(DISTINCT a.idActivo) AS totalActivos,
                COALESCE(SUM(
                    CASE
                        WHEN ea.codigoEstado = "EN_INVENTARIO"
                        THEN 1 ELSE 0
                    END
                ), 0) AS enInventario,
                COALESCE(SUM(
                    CASE
                        WHEN ea.codigoEstado = "ASIGNADO"
                        THEN 1 ELSE 0
                    END
                ), 0) AS asignados,
                COALESCE(SUM(
                    CASE
                        WHEN ea.codigoEstado = "REVISION_TECNICA"
                        THEN 1 ELSE 0
                    END
                ), 0) AS enRevision,
                COALESCE(SUM(
                    CASE
                        WHEN ea.codigoEstado = "EN_REPARACION"
                        THEN 1 ELSE 0
                    END
                ), 0) AS enReparacion,
                COALESCE(SUM(
                    CASE
                        WHEN ea.codigoEstado = "DESCARTE"
                        THEN 1 ELSE 0
                    END
                ), 0) AS enDescarte,
                COALESCE(SUM(
                    CASE
                        WHEN ea.codigoEstado = "DONADO"
                        THEN 1 ELSE 0
                    END
                ), 0) AS donados,
                COALESCE(SUM(a.costo), 0) AS valorCategoria
            FROM Categoria c
            LEFT JOIN Subcategoria s
                ON s.idCategoria = c.idCategoria
            LEFT JOIN Producto p
                ON p.idSubcategoria = s.idSubcategoria
            LEFT JOIN Activo a
                ON a.idProducto = p.idProducto
               AND a.activo = 1
            LEFT JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            GROUP BY
                c.idCategoria,
                c.nombreCategoria
            ORDER BY c.nombreCategoria ASC
            '
        );

        return $statement->fetchAll();
    }

    public function getActiveCategories(): array
    {
        $statement = $this->connection->query(
            '
            SELECT idCategoria, nombreCategoria
            FROM Categoria
            WHERE activo = 1
            ORDER BY nombreCategoria ASC
            '
        );

        return $statement->fetchAll();
    }

    public function getActiveStates(): array
    {
        $statement = $this->connection->query(
            '
            SELECT idEstadoActivo, codigoEstado, nombreEstado
            FROM EstadoActivo
            WHERE activo = 1
            ORDER BY idEstadoActivo ASC
            '
        );

        return $statement->fetchAll();
    }

    public function getInventory(array $filters = []): array
    {
        $sql = '
            SELECT
                a.idActivo,
                a.codigoActivo,
                a.numeroSerie,
                a.direccionIP,
                a.costo,
                a.valorResidual,
                a.fechaAdquisicion,
                a.fechaIngreso,
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
                p.tipoProducto,
                s.nombreSubcategoria,
                c.idCategoria,
                c.nombreCategoria,
                u.nombreUbicacion,
                va.nombreColaborador
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
            LEFT JOIN VistaAsignacionesActivas va
                ON va.idActivo = a.idActivo
            WHERE a.activo = 1
        ';

        $parameters = [];

        $categoryId = (int) ($filters['category'] ?? 0);
        $stateId = (int) ($filters['state'] ?? 0);
        $search = trim((string) ($filters['search'] ?? ''));

        if ($categoryId > 0) {
            $sql .= ' AND c.idCategoria = :categoryId ';
            $parameters['categoryId'] = $categoryId;
        }

        if ($stateId > 0) {
            $sql .= ' AND ea.idEstadoActivo = :stateId ';
            $parameters['stateId'] = $stateId;
        }

        if ($search !== '') {
            $pattern = '%' . $search . '%';

            $sql .= '
                AND (
                    a.codigoActivo LIKE :searchCode
                    OR COALESCE(a.numeroSerie, "")
                        LIKE :searchSerial
                    OR COALESCE(a.direccionIP, "")
                        LIKE :searchIp
                    OR p.nombreProducto LIKE :searchProduct
                    OR COALESCE(p.marca, "")
                        LIKE :searchBrand
                    OR COALESCE(p.modelo, "")
                        LIKE :searchModel
                    OR c.nombreCategoria LIKE :searchCategory
                    OR COALESCE(u.nombreUbicacion, "")
                        LIKE :searchLocation
                    OR COALESCE(va.nombreColaborador, "")
                        LIKE :searchCollaborator
                )
            ';

            $parameters['searchCode'] = $pattern;
            $parameters['searchSerial'] = $pattern;
            $parameters['searchIp'] = $pattern;
            $parameters['searchProduct'] = $pattern;
            $parameters['searchBrand'] = $pattern;
            $parameters['searchModel'] = $pattern;
            $parameters['searchCategory'] = $pattern;
            $parameters['searchLocation'] = $pattern;
            $parameters['searchCollaborator'] = $pattern;
        }

        $sql .= '
            ORDER BY
                c.nombreCategoria ASC,
                p.nombreProducto ASC,
                a.codigoActivo ASC
        ';

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function getDepreciation(int $maxDays): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                a.idActivo,
                a.codigoActivo,
                p.nombreProducto,
                p.marca,
                p.modelo,
                c.nombreCategoria,
                ea.nombreEstado,
                a.costo,
                a.valorResidual,
                a.fechaAdquisicion,
                COALESCE(
                    a.vidaUtilMeses,
                    p.vidaUtilMeses
                ) AS vidaUtilMesesAplicada,
                DATE_ADD(
                    a.fechaAdquisicion,
                    INTERVAL COALESCE(
                        a.vidaUtilMeses,
                        p.vidaUtilMeses
                    ) MONTH
                ) AS fechaFinVidaUtil,
                DATEDIFF(
                    DATE_ADD(
                        a.fechaAdquisicion,
                        INTERVAL COALESCE(
                            a.vidaUtilMeses,
                            p.vidaUtilMeses
                        ) MONTH
                    ),
                    CURDATE()
                ) AS diasRestantes
            FROM Activo a
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN Subcategoria s
                ON s.idSubcategoria = p.idSubcategoria
            INNER JOIN Categoria c
                ON c.idCategoria = s.idCategoria
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            WHERE a.activo = 1
              AND ea.codigoEstado <> "DONADO"
              AND COALESCE(
                    a.vidaUtilMeses,
                    p.vidaUtilMeses
                  ) IS NOT NULL
              AND DATEDIFF(
                    DATE_ADD(
                        a.fechaAdquisicion,
                        INTERVAL COALESCE(
                            a.vidaUtilMeses,
                            p.vidaUtilMeses
                        ) MONTH
                    ),
                    CURDATE()
                  ) <= :maxDays
            ORDER BY
                diasRestantes ASC,
                c.nombreCategoria ASC,
                a.codigoActivo ASC
            '
        );

        $statement->execute([
            'maxDays' => $maxDays,
        ]);

        return $statement->fetchAll();
    }

    public function getNeedYears(): array
    {
        $statement = $this->connection->query(
            '
            SELECT DISTINCT anioPresupuestado
            FROM SolicitudNecesidad
            WHERE anioPresupuestado IS NOT NULL
            ORDER BY anioPresupuestado DESC
            '
        );

        return array_map(
            static fn (array $row): int =>
                (int) $row['anioPresupuestado'],
            $statement->fetchAll()
        );
    }

    public function getNeeds(array $filters = []): array
    {
        $sql = '
            SELECT
                sn.idSolicitud,
                sn.tipoSolicitud,
                sn.titulo,
                sn.cantidad,
                sn.prioridad,
                sn.periodoNecesidad,
                sn.anioPresupuestado,
                sn.costoEstimado,
                sn.fechaSolicitud,
                es.nombreEstado,
                CONCAT(
                    c.nombre,
                    " ",
                    c.apellido
                ) AS nombreColaborador,
                c.departamento,
                sc.nombreSubcategoria,
                p.nombreProducto
            FROM SolicitudNecesidad sn
            INNER JOIN Colaborador c
                ON c.idColaborador = sn.idColaborador
            INNER JOIN EstadoSolicitud es
                ON es.idEstadoSolicitud = sn.idEstadoSolicitud
            LEFT JOIN Subcategoria sc
                ON sc.idSubcategoria = sn.idSubcategoria
            LEFT JOIN Producto p
                ON p.idProducto = sn.idProducto
            WHERE 1 = 1
        ';

        $parameters = [];

        $year = (int) ($filters['year'] ?? 0);
        $period = trim((string) ($filters['period'] ?? ''));
        $status = trim((string) ($filters['status'] ?? ''));

        if ($year > 0) {
            $sql .= ' AND sn.anioPresupuestado = :budgetYear ';
            $parameters['budgetYear'] = $year;
        }

        if (in_array(
            $period,
            ['INMEDIATA', 'ANUAL', 'QUINQUENAL'],
            true
        )) {
            $sql .= ' AND sn.periodoNecesidad = :needPeriod ';
            $parameters['needPeriod'] = $period;
        }

        if ($status !== '') {
            $sql .= ' AND es.nombreEstado = :requestStatus ';
            $parameters['requestStatus'] = $status;
        }

        $sql .= '
            ORDER BY
                COALESCE(
                    sn.anioPresupuestado,
                    YEAR(sn.fechaSolicitud)
                ) DESC,
                FIELD(
                    sn.prioridad,
                    "URGENTE",
                    "ALTA",
                    "MEDIA",
                    "BAJA"
                ),
                sn.fechaSolicitud DESC
        ';

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function getMovements(array $filters = []): array
    {
        $sql = '
            SELECT
                m.idMovimiento,
                m.tipoMovimiento,
                m.descripcion,
                m.fechaMovimiento,
                a.codigoActivo,
                p.nombreProducto,
                CONCAT(
                    usr.nombre,
                    " ",
                    usr.apellido
                ) AS nombreUsuario,
                ea.nombreEstado AS estadoAnterior,
                enu.nombreEstado AS estadoNuevo,
                ua.nombreUbicacion AS ubicacionAnterior,
                un.nombreUbicacion AS ubicacionNueva
            FROM MovimientoActivo m
            INNER JOIN Activo a
                ON a.idActivo = m.idActivo
            INNER JOIN Producto p
                ON p.idProducto = a.idProducto
            INNER JOIN Usuario usr
                ON usr.idUsuario = m.idUsuario
            LEFT JOIN EstadoActivo ea
                ON ea.idEstadoActivo = m.idEstadoAnterior
            LEFT JOIN EstadoActivo enu
                ON enu.idEstadoActivo = m.idEstadoNuevo
            LEFT JOIN Ubicacion ua
                ON ua.idUbicacion = m.idUbicacionAnterior
            LEFT JOIN Ubicacion un
                ON un.idUbicacion = m.idUbicacionNueva
            WHERE 1 = 1
        ';

        $parameters = [];
        $type = trim((string) ($filters['type'] ?? ''));
        $search = trim((string) ($filters['search'] ?? ''));

        if ($type !== '') {
            $sql .= ' AND m.tipoMovimiento = :movementType ';
            $parameters['movementType'] = $type;
        }

        if ($search !== '') {
            $pattern = '%' . $search . '%';

            $sql .= '
                AND (
                    a.codigoActivo LIKE :movementCode
                    OR p.nombreProducto LIKE :movementProduct
                    OR CONCAT(
                        usr.nombre,
                        " ",
                        usr.apellido
                    ) LIKE :movementUser
                    OR COALESCE(m.descripcion, "")
                        LIKE :movementDescription
                )
            ';

            $parameters['movementCode'] = $pattern;
            $parameters['movementProduct'] = $pattern;
            $parameters['movementUser'] = $pattern;
            $parameters['movementDescription'] = $pattern;
        }

        $sql .= '
            ORDER BY m.fechaMovimiento DESC
            LIMIT 500
        ';

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function getLoginHistory(array $filters = []): array
    {
        $sql = '
            SELECT
                hl.idHistorialLogin,
                hl.usuarioIngresado,
                hl.direccionIP,
                hl.userAgent,
                hl.exito,
                hl.descripcion,
                hl.fechaIntento,
                CONCAT(
                    u.nombre,
                    " ",
                    u.apellido
                ) AS nombreUsuario
            FROM Historial_Login hl
            LEFT JOIN Usuario u
                ON u.idUsuario = hl.idUsuario
            WHERE 1 = 1
        ';

        $parameters = [];
        $result = (string) ($filters['result'] ?? '');
        $search = trim((string) ($filters['search'] ?? ''));

        if ($result === '1' || $result === '0') {
            $sql .= ' AND hl.exito = :loginResult ';
            $parameters['loginResult'] = (int) $result;
        }

        if ($search !== '') {
            $pattern = '%' . $search . '%';

            $sql .= '
                AND (
                    hl.usuarioIngresado LIKE :loginIdentifier
                    OR hl.direccionIP LIKE :loginIp
                    OR COALESCE(
                        CONCAT(u.nombre, " ", u.apellido),
                        ""
                    ) LIKE :loginUser
                )
            ';

            $parameters['loginIdentifier'] = $pattern;
            $parameters['loginIp'] = $pattern;
            $parameters['loginUser'] = $pattern;
        }

        $sql .= '
            ORDER BY hl.fechaIntento DESC
            LIMIT 500
        ';

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function getAuditHistory(array $filters = []): array
    {
        $sql = '
            SELECT
                au.idAuditoria,
                au.modulo,
                au.accion,
                au.tablaAfectada,
                au.idRegistro,
                au.descripcion,
                au.direccionIP,
                au.hashAnterior,
                au.hashRegistro,
                au.firmaDigital,
                au.algoritmoFirma,
                au.fecha,
                CONCAT(
                    u.nombre,
                    " ",
                    u.apellido
                ) AS nombreUsuario,
                u.usuario,
                lp.huellaDigital
            FROM Auditoria au
            LEFT JOIN Usuario u
                ON u.idUsuario = au.idUsuario
            LEFT JOIN LlavePublicaUsuario lp
                ON lp.idLlavePublica = au.idLlavePublica
            WHERE 1 = 1
        ';

        $parameters = [];
        $module = trim((string) ($filters['module'] ?? ''));
        $search = trim((string) ($filters['search'] ?? ''));

        if ($module !== '') {
            $sql .= ' AND au.modulo = :auditModule ';
            $parameters['auditModule'] = $module;
        }

        if ($search !== '') {
            $pattern = '%' . $search . '%';

            $sql .= '
                AND (
                    au.accion LIKE :auditAction
                    OR COALESCE(au.tablaAfectada, "")
                        LIKE :auditTable
                    OR COALESCE(au.idRegistro, "")
                        LIKE :auditRecord
                    OR COALESCE(au.descripcion, "")
                        LIKE :auditDescription
                    OR COALESCE(u.usuario, "")
                        LIKE :auditUsername
                    OR COALESCE(
                        CONCAT(u.nombre, " ", u.apellido),
                        ""
                    ) LIKE :auditUser
                )
            ';

            $parameters['auditAction'] = $pattern;
            $parameters['auditTable'] = $pattern;
            $parameters['auditRecord'] = $pattern;
            $parameters['auditDescription'] = $pattern;
            $parameters['auditUsername'] = $pattern;
            $parameters['auditUser'] = $pattern;
        }

        $sql .= '
            ORDER BY au.idAuditoria DESC
            LIMIT 500
        ';

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }
}
