<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\ActivoRepositoryInterface;
use PDO;

final class ActivoRepository implements ActivoRepositoryInterface
{
    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection
            ?? Database::getConnection();
    }

    public function listByProduct(
        int $productId,
        array $filters = []
    ): ?array {
        $product = $this->findProductById($productId);

        if ($product === null) {
            return null;
        }

        $sql = '
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
                ea.codigoEstado,
                ea.nombreEstado,
                u.nombreUbicacion,
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
                ) AS imagenPrincipal,
                (
                    SELECT COUNT(*)
                    FROM ImagenActivo ia2
                    WHERE ia2.idActivo = a.idActivo
                      AND ia2.activo = 1
                ) AS cantidadImagenes
            FROM Activo a
            INNER JOIN EstadoActivo ea
                ON ea.idEstadoActivo = a.idEstadoActivo
            LEFT JOIN Ubicacion u
                ON u.idUbicacion = a.idUbicacion
            WHERE a.idProducto = :idProducto
        ';

        $parameters = [
            'idProducto' => $productId,
        ];

        $search = trim((string) ($filters['search'] ?? ''));
        $stateId = (int) ($filters['state'] ?? 0);
        $active = (string) ($filters['active'] ?? '');

        if ($search !== '') {
            $pattern = '%' . $search . '%';

            $sql .= '
                AND (
                    a.codigoActivo LIKE :searchCode
                    OR COALESCE(a.numeroSerie, "") LIKE :searchSerial
                    OR COALESCE(a.direccionIP, "") LIKE :searchIp
                    OR COALESCE(u.nombreUbicacion, "") LIKE :searchLocation
                )
            ';

            $parameters['searchCode'] = $pattern;
            $parameters['searchSerial'] = $pattern;
            $parameters['searchIp'] = $pattern;
            $parameters['searchLocation'] = $pattern;
        }

        if ($stateId > 0) {
            $sql .= ' AND a.idEstadoActivo = :filterStateId ';
            $parameters['filterStateId'] = $stateId;
        }

        if ($active === '1' || $active === '0') {
            $sql .= ' AND a.activo = :filterActive ';
            $parameters['filterActive'] = (int) $active;
        }

        $sql .= '
            ORDER BY
                a.activo DESC,
                a.codigoActivo ASC
        ';

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        return [
            'product' => $product,
            'assets' => $statement->fetchAll(),
        ];
    }

    public function listActiveProducts(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                p.idProducto,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.vidaUtilMeses,
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
                c.nombreCategoria ASC,
                s.nombreSubcategoria ASC,
                p.nombreProducto ASC,
                p.marca ASC,
                p.modelo ASC
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function listAvailableStates(
        ?int $currentStateId = null
    ): array {
        $sql = '
            SELECT
                idEstadoActivo,
                codigoEstado,
                nombreEstado,
                permiteAsignacion,
                cuentaComoInventario,
                activo
            FROM EstadoActivo
            WHERE activo = 1
        ';

        $parameters = [];

        if ($currentStateId !== null && $currentStateId > 0) {
            $sql .= ' OR idEstadoActivo = :currentStateId ';
            $parameters['currentStateId'] = $currentStateId;
        }

        $sql .= '
            ORDER BY
                CASE codigoEstado
                    WHEN "EN_INVENTARIO" THEN 1
                    WHEN "REVISION_TECNICA" THEN 2
                    WHEN "EN_REPARACION" THEN 3
                    WHEN "DESCARTE" THEN 4
                    WHEN "DONADO" THEN 5
                    WHEN "ASIGNADO" THEN 6
                    ELSE 7
                END,
                nombreEstado ASC
        ';

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

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
                oficina
            FROM Ubicacion
            WHERE activo = 1
            ORDER BY nombreUbicacion ASC
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function findProductById(int $productId): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                p.idProducto,
                p.idSubcategoria,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.tipoProducto,
                p.vidaUtilMeses,
                p.imagen,
                p.activo,
                s.idCategoria,
                s.nombreSubcategoria,
                s.activo AS subcategoriaActiva,
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

    public function findStateById(int $stateId): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                idEstadoActivo,
                codigoEstado,
                nombreEstado,
                permiteAsignacion,
                cuentaComoInventario,
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

    public function findLocationById(int $locationId): ?array
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

    public function findById(int $assetId): ?array
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
                ea.codigoEstado,
                ea.nombreEstado,
                u.nombreUbicacion,
                p.nombreProducto,
                p.marca,
                p.modelo,
                p.tipoProducto,
                p.activo AS productoActivo,
                p.idSubcategoria,
                s.nombreSubcategoria,
                s.idCategoria,
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
            LEFT JOIN Ubicacion u
                ON u.idUbicacion = a.idUbicacion
            WHERE a.idActivo = :idActivo
            LIMIT 1
            '
        );

        $statement->execute([
            'idActivo' => $assetId,
        ]);

        $asset = $statement->fetch();

        return is_array($asset) ? $asset : null;
    }

    public function findImages(int $assetId): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                idImagenActivo,
                idActivo,
                rutaImagen,
                nombreOriginal,
                mimeType,
                tamanoBytes,
                esPrincipal,
                ordenVisual,
                activo,
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

    public function findConflicts(
        string $assetCode,
        ?string $serialNumber,
        ?int $excludeId = null
    ): array {
        $sql = '
            SELECT
                idActivo,
                codigoActivo,
                numeroSerie
            FROM Activo
            WHERE (
                LOWER(TRIM(codigoActivo))
                    = LOWER(TRIM(:assetCode))
        ';

        $parameters = [
            'assetCode' => $assetCode,
        ];

        if ($serialNumber !== null && $serialNumber !== '') {
            $sql .= '
                OR LOWER(TRIM(COALESCE(numeroSerie, "")))
                    = LOWER(TRIM(:serialNumber))
            ';
            $parameters['serialNumber'] = $serialNumber;
        }

        $sql .= ' ) ';

        if ($excludeId !== null) {
            $sql .= ' AND idActivo <> :excludeId ';
            $parameters['excludeId'] = $excludeId;
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function create(array $data): int
    {
        $statement = $this->connection->prepare(
            '
            INSERT INTO Activo (
                idProducto,
                codigoActivo,
                numeroSerie,
                direccionIP,
                costo,
                fechaAdquisicion,
                fechaIngreso,
                vidaUtilMeses,
                valorResidual,
                idEstadoActivo,
                idUbicacion,
                qrToken,
                observaciones,
                activo
            ) VALUES (
                :idProducto,
                :codigoActivo,
                :numeroSerie,
                :direccionIP,
                :costo,
                :fechaAdquisicion,
                :fechaIngreso,
                :vidaUtilMeses,
                :valorResidual,
                :idEstadoActivo,
                :idUbicacion,
                :qrToken,
                :observaciones,
                1
            )
            '
        );

        $statement->execute([
            'idProducto' => $data['idProducto'],
            'codigoActivo' => $data['codigoActivo'],
            'numeroSerie' => $data['numeroSerie'],
            'direccionIP' => $data['direccionIP'],
            'costo' => $data['costo'],
            'fechaAdquisicion' => $data['fechaAdquisicion'],
            'fechaIngreso' => $data['fechaIngreso'],
            'vidaUtilMeses' => $data['vidaUtilMeses'],
            'valorResidual' => $data['valorResidual'],
            'idEstadoActivo' => $data['idEstadoActivo'],
            'idUbicacion' => $data['idUbicacion'],
            'qrToken' => $data['qrToken'],
            'observaciones' => $data['observaciones'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function update(
        int $assetId,
        array $data
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE Activo
            SET
                idProducto = :idProducto,
                codigoActivo = :codigoActivo,
                numeroSerie = :numeroSerie,
                direccionIP = :direccionIP,
                costo = :costo,
                fechaAdquisicion = :fechaAdquisicion,
                fechaIngreso = :fechaIngreso,
                vidaUtilMeses = :vidaUtilMeses,
                valorResidual = :valorResidual,
                idEstadoActivo = :idEstadoActivo,
                idUbicacion = :idUbicacion,
                observaciones = :observaciones
            WHERE idActivo = :idActivo
            '
        );

        $statement->execute([
            'idProducto' => $data['idProducto'],
            'codigoActivo' => $data['codigoActivo'],
            'numeroSerie' => $data['numeroSerie'],
            'direccionIP' => $data['direccionIP'],
            'costo' => $data['costo'],
            'fechaAdquisicion' => $data['fechaAdquisicion'],
            'fechaIngreso' => $data['fechaIngreso'],
            'vidaUtilMeses' => $data['vidaUtilMeses'],
            'valorResidual' => $data['valorResidual'],
            'idEstadoActivo' => $data['idEstadoActivo'],
            'idUbicacion' => $data['idUbicacion'],
            'observaciones' => $data['observaciones'],
            'idActivo' => $assetId,
        ]);
    }

    public function setActiveState(
        int $assetId,
        bool $active
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE Activo
            SET activo = :activo
            WHERE idActivo = :idActivo
            '
        );

        $statement->execute([
            'activo' => $active ? 1 : 0,
            'idActivo' => $assetId,
        ]);
    }

    public function insertImage(
        int $assetId,
        array $data
    ): int {
        $statement = $this->connection->prepare(
            '
            INSERT INTO ImagenActivo (
                idActivo,
                rutaImagen,
                nombreOriginal,
                mimeType,
                tamanoBytes,
                esPrincipal,
                ordenVisual,
                activo
            ) VALUES (
                :idActivo,
                :rutaImagen,
                :nombreOriginal,
                :mimeType,
                :tamanoBytes,
                :esPrincipal,
                :ordenVisual,
                1
            )
            '
        );

        $statement->execute([
            'idActivo' => $assetId,
            'rutaImagen' => $data['rutaImagen'],
            'nombreOriginal' => $data['nombreOriginal'],
            'mimeType' => $data['mimeType'],
            'tamanoBytes' => $data['tamanoBytes'],
            'esPrincipal' => $data['esPrincipal'] ? 1 : 0,
            'ordenVisual' => $data['ordenVisual'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function deactivateImages(
        int $assetId,
        array $imageIds
    ): void {
        if ($imageIds === []) {
            return;
        }

        $placeholders = [];
        $parameters = [
            'idActivo' => $assetId,
        ];

        foreach (array_values($imageIds) as $index => $imageId) {
            $name = 'imageId' . $index;
            $placeholders[] = ':' . $name;
            $parameters[$name] = (int) $imageId;
        }

        $statement = $this->connection->prepare(
            '
            UPDATE ImagenActivo
            SET
                activo = 0,
                esPrincipal = 0
            WHERE idActivo = :idActivo
              AND idImagenActivo IN ('
                . implode(', ', $placeholders)
                . ')
            '
        );

        $statement->execute($parameters);
    }

    public function clearPrincipalImage(int $assetId): void
    {
        $statement = $this->connection->prepare(
            '
            UPDATE ImagenActivo
            SET esPrincipal = 0
            WHERE idActivo = :idActivo
              AND activo = 1
            '
        );

        $statement->execute([
            'idActivo' => $assetId,
        ]);
    }

    public function setPrincipalImage(
        int $assetId,
        int $imageId
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE ImagenActivo
            SET esPrincipal = 1
            WHERE idActivo = :idActivo
              AND idImagenActivo = :idImagenActivo
              AND activo = 1
            '
        );

        $statement->execute([
            'idActivo' => $assetId,
            'idImagenActivo' => $imageId,
        ]);
    }

    public function getNextImageOrder(int $assetId): int
    {
        $statement = $this->connection->prepare(
            '
            SELECT COALESCE(MAX(ordenVisual), 0) + 1
            FROM ImagenActivo
            WHERE idActivo = :idActivo
            '
        );

        $statement->execute([
            'idActivo' => $assetId,
        ]);

        return max(1, (int) $statement->fetchColumn());
    }

    public function countActiveImages(int $assetId): int
    {
        $statement = $this->connection->prepare(
            '
            SELECT COUNT(*)
            FROM ImagenActivo
            WHERE idActivo = :idActivo
              AND activo = 1
            '
        );

        $statement->execute([
            'idActivo' => $assetId,
        ]);

        return (int) $statement->fetchColumn();
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
