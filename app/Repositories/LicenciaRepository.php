<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\LicenciaRepositoryInterface;
use PDO;
use Throwable;

final class LicenciaRepository implements LicenciaRepositoryInterface
{
    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection ?? Database::getConnection();
    }

    public function listAll(array $filters = []): array
    {
        $sql = '
            SELECT
                l.idLicencia,
                l.idActivo,
                l.proveedor,
                l.tipoLicencia,
                l.urlAcceso,
                l.claveCifrada,
                l.cantidadPuestos,
                l.fechaInicio,
                l.fechaExpiracion,
                l.renovacionAutomatica,
                l.observaciones,
                a.codigoActivo,
                a.activo,
                ea.codigoEstado,
                ea.nombreEstado,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.imagen,
                s.nombreSubcategoria,
                c.nombreCategoria,
                DATEDIFF(l.fechaExpiracion, CURDATE()) AS diasRestantes,
                COUNT(
                    DISTINCT CASE
                        WHEN al.estadoAsignacion = "ACTIVA"
                         AND al.fechaRevocacion IS NULL
                        THEN al.idAsignacionLicencia
                    END
                ) AS puestosAsignados,
                GREATEST(
                    l.cantidadPuestos - COUNT(
                        DISTINCT CASE
                            WHEN al.estadoAsignacion = "ACTIVA"
                             AND al.fechaRevocacion IS NULL
                            THEN al.idAsignacionLicencia
                        END
                    ),
                    0
                ) AS puestosDisponibles
            FROM LicenciaSoftware l
            INNER JOIN Activo a ON a.idActivo = l.idActivo
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            INNER JOIN Producto p ON p.idProducto = a.idProducto
            INNER JOIN Subcategoria s
                ON s.idSubcategoria = p.idSubcategoria
            INNER JOIN Categoria c
                ON c.idCategoria = s.idCategoria
            LEFT JOIN AsignacionLicencia al
                ON al.idLicencia = l.idLicencia
            WHERE p.tipoProducto = "LICENCIA"
        ';

        $parameters = [];
        $search = trim((string) ($filters['search'] ?? ''));
        $expiration = trim((string) ($filters['expiration'] ?? ''));

        if ($search !== '') {
            $pattern = '%' . $search . '%';
            $sql .= '
                AND (
                    a.codigoActivo LIKE :searchCode
                    OR p.nombreProducto LIKE :searchProduct
                    OR p.marca LIKE :searchBrand
                    OR p.modelo LIKE :searchModel
                    OR l.proveedor LIKE :searchProvider
                    OR l.tipoLicencia LIKE :searchType
                )
            ';
            $parameters = [
                'searchCode' => $pattern,
                'searchProduct' => $pattern,
                'searchBrand' => $pattern,
                'searchModel' => $pattern,
                'searchProvider' => $pattern,
                'searchType' => $pattern,
            ];
        }

        if ($expiration === 'expired') {
            $sql .= ' AND l.fechaExpiracion < CURDATE() ';
        } elseif ($expiration === '30') {
            $sql .= '
                AND l.fechaExpiracion BETWEEN CURDATE()
                    AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
            ';
        } elseif ($expiration === '90') {
            $sql .= '
                AND l.fechaExpiracion BETWEEN CURDATE()
                    AND DATE_ADD(CURDATE(), INTERVAL 90 DAY)
            ';
        } elseif ($expiration === 'none') {
            $sql .= ' AND l.fechaExpiracion IS NULL ';
        }

        $sql .= '
            GROUP BY
                l.idLicencia,
                l.idActivo,
                l.proveedor,
                l.tipoLicencia,
                l.urlAcceso,
                l.claveCifrada,
                l.cantidadPuestos,
                l.fechaInicio,
                l.fechaExpiracion,
                l.renovacionAutomatica,
                l.observaciones,
                a.codigoActivo,
                a.activo,
                ea.codigoEstado,
                ea.nombreEstado,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.imagen,
                s.nombreSubcategoria,
                c.nombreCategoria
            ORDER BY
                CASE
                    WHEN l.fechaExpiracion IS NULL THEN 2
                    WHEN l.fechaExpiracion < CURDATE() THEN 0
                    ELSE 1
                END,
                l.fechaExpiracion ASC,
                p.nombreProducto ASC
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
                p.nombreProducto,
                p.marca,
                p.modelo,
                ea.nombreEstado
            FROM Activo a
            INNER JOIN Producto p ON p.idProducto = a.idProducto
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            WHERE a.activo = 1
              AND p.activo = 1
              AND p.tipoProducto = "LICENCIA"
              AND ea.codigoEstado NOT IN ("DESCARTE", "DONADO")
              AND NOT EXISTS (
                    SELECT 1
                    FROM LicenciaSoftware l
                    WHERE l.idActivo = a.idActivo
              )
            ORDER BY p.nombreProducto, a.codigoActivo
            '
        );
        $statement->execute();

        return $statement->fetchAll();
    }


    public function findEligibleAsset(int $assetId): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                a.idActivo,
                a.codigoActivo,
                a.activo,
                ea.codigoEstado,
                p.tipoProducto,
                p.activo AS productoActivo
            FROM Activo a
            INNER JOIN Producto p ON p.idProducto = a.idProducto
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            WHERE a.idActivo = :idActivo
              AND a.activo = 1
              AND p.activo = 1
              AND p.tipoProducto = "LICENCIA"
              AND ea.codigoEstado NOT IN ("DESCARTE", "DONADO")
              AND NOT EXISTS (
                    SELECT 1
                    FROM LicenciaSoftware l
                    WHERE l.idActivo = a.idActivo
              )
            LIMIT 1
            '
        );
        $statement->execute(['idActivo' => $assetId]);
        $asset = $statement->fetch();

        return is_array($asset) ? $asset : null;
    }

    public function findById(int $licenseId, bool $lock = false): ?array
    {
        $sql = '
            SELECT
                l.*,
                a.codigoActivo,
                a.numeroSerie,
                a.costo,
                a.activo,
                ea.codigoEstado,
                ea.nombreEstado,
                p.idProducto,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.imagen,
                p.tipoProducto,
                s.nombreSubcategoria,
                c.nombreCategoria,
                DATEDIFF(l.fechaExpiracion, CURDATE()) AS diasRestantes
            FROM LicenciaSoftware l
            INNER JOIN Activo a ON a.idActivo = l.idActivo
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            INNER JOIN Producto p ON p.idProducto = a.idProducto
            INNER JOIN Subcategoria s
                ON s.idSubcategoria = p.idSubcategoria
            INNER JOIN Categoria c
                ON c.idCategoria = s.idCategoria
            WHERE l.idLicencia = :idLicencia
            LIMIT 1
        ';

        if ($lock) {
            $sql .= ' FOR UPDATE ';
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute(['idLicencia' => $licenseId]);
        $license = $statement->fetch();

        return is_array($license) ? $license : null;
    }

    public function findByAssetId(int $assetId): ?array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM LicenciaSoftware WHERE idActivo = :idActivo LIMIT 1'
        );
        $statement->execute(['idActivo' => $assetId]);
        $license = $statement->fetch();

        return is_array($license) ? $license : null;
    }

    public function create(array $data): int
    {
        $statement = $this->connection->prepare(
            '
            INSERT INTO LicenciaSoftware (
                idActivo,
                proveedor,
                tipoLicencia,
                urlAcceso,
                claveCifrada,
                cantidadPuestos,
                fechaInicio,
                fechaExpiracion,
                renovacionAutomatica,
                observaciones
            ) VALUES (
                :idActivo,
                :proveedor,
                :tipoLicencia,
                :urlAcceso,
                :claveCifrada,
                :cantidadPuestos,
                :fechaInicio,
                :fechaExpiracion,
                :renovacionAutomatica,
                :observaciones
            )
            '
        );
        $statement->execute($data);

        return (int) $this->connection->lastInsertId();
    }

    public function update(int $licenseId, array $data): void
    {
        $statement = $this->connection->prepare(
            '
            UPDATE LicenciaSoftware
            SET
                proveedor = :proveedor,
                tipoLicencia = :tipoLicencia,
                urlAcceso = :urlAcceso,
                claveCifrada = :claveCifrada,
                cantidadPuestos = :cantidadPuestos,
                fechaInicio = :fechaInicio,
                fechaExpiracion = :fechaExpiracion,
                renovacionAutomatica = :renovacionAutomatica,
                observaciones = :observaciones
            WHERE idLicencia = :idLicencia
            '
        );
        $statement->execute([
            ...$data,
            'idLicencia' => $licenseId,
        ]);
    }

    public function listActiveCollaborators(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                c.idColaborador,
                c.nombre,
                c.apellido,
                c.identificacion,
                c.correo,
                c.cargo,
                c.departamento
            FROM Colaborador c
            INNER JOIN Usuario u ON u.idUsuario = c.idUsuario
            WHERE c.activo = 1
              AND u.activo = 1
            ORDER BY c.apellido, c.nombre
            '
        );
        $statement->execute();

        return $statement->fetchAll();
    }


    public function findActiveCollaborator(int $collaboratorId): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                c.idColaborador,
                c.nombre,
                c.apellido,
                c.correo,
                c.activo,
                u.activo AS usuarioActivo
            FROM Colaborador c
            INNER JOIN Usuario u ON u.idUsuario = c.idUsuario
            WHERE c.idColaborador = :idColaborador
              AND c.activo = 1
              AND u.activo = 1
            LIMIT 1
            '
        );
        $statement->execute(['idColaborador' => $collaboratorId]);
        $collaborator = $statement->fetch();

        return is_array($collaborator) ? $collaborator : null;
    }

    public function listAssignments(int $licenseId): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                al.idAsignacionLicencia,
                al.idLicencia,
                al.idColaborador,
                al.idUsuarioAsigna,
                al.correoAsignado,
                al.fechaAsignacion,
                al.fechaRevocacion,
                al.estadoAsignacion,
                al.observaciones,
                CONCAT(c.nombre, " ", c.apellido) AS nombreColaborador,
                c.identificacion,
                c.correo,
                c.cargo,
                c.departamento,
                CONCAT(u.nombre, " ", u.apellido) AS nombreUsuarioAsigna
            FROM AsignacionLicencia al
            INNER JOIN Colaborador c
                ON c.idColaborador = al.idColaborador
            INNER JOIN Usuario u
                ON u.idUsuario = al.idUsuarioAsigna
            WHERE al.idLicencia = :idLicencia
            ORDER BY
                al.estadoAsignacion = "ACTIVA" DESC,
                al.fechaAsignacion DESC
            '
        );
        $statement->execute(['idLicencia' => $licenseId]);

        return $statement->fetchAll();
    }

    public function countActiveAssignments(int $licenseId): int
    {
        $statement = $this->connection->prepare(
            '
            SELECT COUNT(*)
            FROM AsignacionLicencia
            WHERE idLicencia = :idLicencia
              AND estadoAsignacion = "ACTIVA"
              AND fechaRevocacion IS NULL
            '
        );
        $statement->execute(['idLicencia' => $licenseId]);

        return (int) $statement->fetchColumn();
    }

    public function hasActiveAssignment(
        int $licenseId,
        int $collaboratorId
    ): bool {
        $statement = $this->connection->prepare(
            '
            SELECT 1
            FROM AsignacionLicencia
            WHERE idLicencia = :idLicencia
              AND idColaborador = :idColaborador
              AND estadoAsignacion = "ACTIVA"
              AND fechaRevocacion IS NULL
            LIMIT 1
            '
        );
        $statement->execute([
            'idLicencia' => $licenseId,
            'idColaborador' => $collaboratorId,
        ]);

        return $statement->fetchColumn() !== false;
    }

    public function createAssignment(array $data): int
    {
        $statement = $this->connection->prepare(
            '
            INSERT INTO AsignacionLicencia (
                idLicencia,
                idColaborador,
                idUsuarioAsigna,
                correoAsignado,
                observaciones
            ) VALUES (
                :idLicencia,
                :idColaborador,
                :idUsuarioAsigna,
                :correoAsignado,
                :observaciones
            )
            '
        );
        $statement->execute($data);

        return (int) $this->connection->lastInsertId();
    }

    public function findAssignmentById(
        int $assignmentId,
        bool $lock = false
    ): ?array {
        $sql = '
            SELECT
                al.*,
                l.cantidadPuestos,
                p.nombreProducto,
                a.codigoActivo,
                CONCAT(c.nombre, " ", c.apellido) AS nombreColaborador
            FROM AsignacionLicencia al
            INNER JOIN LicenciaSoftware l
                ON l.idLicencia = al.idLicencia
            INNER JOIN Activo a ON a.idActivo = l.idActivo
            INNER JOIN Producto p ON p.idProducto = a.idProducto
            INNER JOIN Colaborador c
                ON c.idColaborador = al.idColaborador
            WHERE al.idAsignacionLicencia = :idAsignacionLicencia
            LIMIT 1
        ';

        if ($lock) {
            $sql .= ' FOR UPDATE ';
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            'idAsignacionLicencia' => $assignmentId,
        ]);
        $assignment = $statement->fetch();

        return is_array($assignment) ? $assignment : null;
    }

    public function revokeAssignment(int $assignmentId): void
    {
        $statement = $this->connection->prepare(
            '
            UPDATE AsignacionLicencia
            SET
                estadoAsignacion = "REVOCADA",
                fechaRevocacion = CURRENT_TIMESTAMP
            WHERE idAsignacionLicencia = :idAsignacionLicencia
              AND estadoAsignacion = "ACTIVA"
            '
        );
        $statement->execute([
            'idAsignacionLicencia' => $assignmentId,
        ]);
    }

    public function listMyLicenses(int $userId): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                al.idAsignacionLicencia,
                al.correoAsignado,
                al.fechaAsignacion,
                al.observaciones AS observacionesAsignacion,
                l.idLicencia,
                l.proveedor,
                l.tipoLicencia,
                l.urlAcceso,
                l.fechaInicio,
                l.fechaExpiracion,
                l.renovacionAutomatica,
                DATEDIFF(l.fechaExpiracion, CURDATE()) AS diasRestantes,
                a.codigoActivo,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.imagen
            FROM AsignacionLicencia al
            INNER JOIN LicenciaSoftware l
                ON l.idLicencia = al.idLicencia
            INNER JOIN Activo a ON a.idActivo = l.idActivo
            INNER JOIN Producto p ON p.idProducto = a.idProducto
            INNER JOIN Colaborador c
                ON c.idColaborador = al.idColaborador
            WHERE c.idUsuario = :idUsuario
              AND c.activo = 1
              AND al.estadoAsignacion = "ACTIVA"
              AND al.fechaRevocacion IS NULL
            ORDER BY p.nombreProducto ASC
            '
        );
        $statement->execute(['idUsuario' => $userId]);

        return $statement->fetchAll();
    }

    public function getUserPasswordHash(int $userId): ?string
    {
        $statement = $this->connection->prepare(
            'SELECT passwordHash FROM Usuario WHERE idUsuario = :idUsuario LIMIT 1'
        );
        $statement->execute(['idUsuario' => $userId]);
        $hash = $statement->fetchColumn();

        return is_string($hash) ? $hash : null;
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
