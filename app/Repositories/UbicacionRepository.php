<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\UbicacionRepositoryInterface;
use PDO;

final class UbicacionRepository implements UbicacionRepositoryInterface
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
                u.idUbicacion,
                u.nombreUbicacion,
                u.tipoUbicacion,
                u.edificio,
                u.piso,
                u.oficina,
                u.direccion,
                u.descripcion,
                u.activo,
                u.fechaRegistro,
                (
                    SELECT COUNT(*)
                    FROM Activo a
                    WHERE a.idUbicacion = u.idUbicacion
                      AND a.activo = 1
                ) AS totalActivos,
                (
                    SELECT COUNT(*)
                    FROM ColaboradorUbicacion cu
                    WHERE cu.idUbicacion = u.idUbicacion
                      AND cu.esActual = 1
                      AND cu.fechaFin IS NULL
                ) AS totalColaboradores
            FROM Ubicacion u
            WHERE 1 = 1
        ';

        $parameters = [];
        $search = trim((string) ($filters['search'] ?? ''));
        $type = trim((string) ($filters['type'] ?? ''));
        $active = (string) ($filters['active'] ?? '');

        if ($search !== '') {
            $pattern = '%' . $search . '%';

            $sql .= '
                AND (
                    u.nombreUbicacion LIKE :searchName
                    OR COALESCE(u.edificio, "") LIKE :searchBuilding
                    OR COALESCE(u.piso, "") LIKE :searchFloor
                    OR COALESCE(u.oficina, "") LIKE :searchOffice
                    OR COALESCE(u.direccion, "") LIKE :searchAddress
                )
            ';

            $parameters['searchName'] = $pattern;
            $parameters['searchBuilding'] = $pattern;
            $parameters['searchFloor'] = $pattern;
            $parameters['searchOffice'] = $pattern;
            $parameters['searchAddress'] = $pattern;
        }

        if (in_array($type, $this->validTypes(), true)) {
            $sql .= ' AND u.tipoUbicacion = :filterType ';
            $parameters['filterType'] = $type;
        }

        if ($active === '1' || $active === '0') {
            $sql .= ' AND u.activo = :filterActive ';
            $parameters['filterActive'] = (int) $active;
        }

        $sql .= '
            ORDER BY
                u.activo DESC,
                u.nombreUbicacion ASC
        ';

        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    public function findById(int $locationId): ?array
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
                direccion,
                descripcion,
                activo,
                fechaRegistro
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

    public function nameExists(
        string $name,
        ?int $excludeId = null
    ): bool {
        $sql = '
            SELECT 1
            FROM Ubicacion
            WHERE LOWER(TRIM(nombreUbicacion))
                = LOWER(TRIM(:locationName))
        ';

        $parameters = [
            'locationName' => $name,
        ];

        if ($excludeId !== null) {
            $sql .= ' AND idUbicacion <> :excludeId ';
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
            INSERT INTO Ubicacion (
                nombreUbicacion,
                tipoUbicacion,
                edificio,
                piso,
                oficina,
                direccion,
                descripcion,
                activo
            ) VALUES (
                :nombreUbicacion,
                :tipoUbicacion,
                :edificio,
                :piso,
                :oficina,
                :direccion,
                :descripcion,
                1
            )
            '
        );

        $statement->execute([
            'nombreUbicacion' => $data['nombreUbicacion'],
            'tipoUbicacion' => $data['tipoUbicacion'],
            'edificio' => $data['edificio'],
            'piso' => $data['piso'],
            'oficina' => $data['oficina'],
            'direccion' => $data['direccion'],
            'descripcion' => $data['descripcion'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function update(
        int $locationId,
        array $data
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE Ubicacion
            SET
                nombreUbicacion = :nombreUbicacion,
                tipoUbicacion = :tipoUbicacion,
                edificio = :edificio,
                piso = :piso,
                oficina = :oficina,
                direccion = :direccion,
                descripcion = :descripcion
            WHERE idUbicacion = :idUbicacion
            '
        );

        $statement->execute([
            'nombreUbicacion' => $data['nombreUbicacion'],
            'tipoUbicacion' => $data['tipoUbicacion'],
            'edificio' => $data['edificio'],
            'piso' => $data['piso'],
            'oficina' => $data['oficina'],
            'direccion' => $data['direccion'],
            'descripcion' => $data['descripcion'],
            'idUbicacion' => $locationId,
        ]);
    }

    public function countActiveUsage(int $locationId): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                (
                    SELECT COUNT(*)
                    FROM Activo a
                    WHERE a.idUbicacion = :assetLocationId
                      AND a.activo = 1
                ) AS totalActivos,
                (
                    SELECT COUNT(*)
                    FROM ColaboradorUbicacion cu
                    WHERE cu.idUbicacion = :collaboratorLocationId
                      AND cu.esActual = 1
                      AND cu.fechaFin IS NULL
                ) AS totalColaboradores
            '
        );

        $statement->execute([
            'assetLocationId' => $locationId,
            'collaboratorLocationId' => $locationId,
        ]);

        $usage = $statement->fetch();

        return is_array($usage)
            ? $usage
            : [
                'totalActivos' => 0,
                'totalColaboradores' => 0,
            ];
    }

    public function setActiveState(
        int $locationId,
        bool $active
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE Ubicacion
            SET activo = :activo
            WHERE idUbicacion = :idUbicacion
            '
        );

        $statement->execute([
            'activo' => $active ? 1 : 0,
            'idUbicacion' => $locationId,
        ]);
    }

    private function validTypes(): array
    {
        return [
            'EDIFICIO',
            'OFICINA',
            'CASA',
            'BODEGA',
            'OTRA',
        ];
    }
}
