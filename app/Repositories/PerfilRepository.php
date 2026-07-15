<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\PerfilRepositoryInterface;
use PDO;
use Throwable;

final class PerfilRepository implements PerfilRepositoryInterface
{
    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection
            ?? Database::getConnection();
    }

    public function findByUserId(int $userId): ?array
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                u.idUsuario,
                u.cedula,
                u.nombre,
                u.apellido,
                u.usuario,
                u.correo,
                u.idRol,
                u.activo,
                u.ultimoAcceso,
                u.fechaRegistro,
                r.nombreRol,
                c.idColaborador,
                c.telefono,
                c.foto,
                c.cargo,
                c.departamento,
                c.fechaIngreso,
                c.fechaSalida,
                cu.idUbicacion AS idUbicacionActual,
                ub.nombreUbicacion,
                ub.tipoUbicacion,
                ub.edificio,
                ub.piso,
                ub.oficina,
                ub.direccion
            FROM Usuario u
            INNER JOIN Rol r
                ON r.idRol = u.idRol
            LEFT JOIN Colaborador c
                ON c.idUsuario = u.idUsuario
            LEFT JOIN ColaboradorUbicacion cu
                ON cu.idColaborador = c.idColaborador
               AND cu.esActual = 1
               AND cu.fechaFin IS NULL
            LEFT JOIN Ubicacion ub
                ON ub.idUbicacion = cu.idUbicacion
            WHERE u.idUsuario = :idUsuario
            LIMIT 1
            '
        );

        $statement->execute([
            'idUsuario' => $userId,
        ]);

        $profile = $statement->fetch();

        return is_array($profile)
            ? $profile
            : null;
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
            ORDER BY
                nombreUbicacion ASC
            '
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function getLocationHistory(
        int $collaboratorId
    ): array {
        $statement = $this->connection->prepare(
            '
            SELECT
                cu.idColaboradorUbicacion,
                cu.fechaInicio,
                cu.fechaFin,
                cu.esActual,
                cu.observaciones,
                u.nombreUbicacion,
                u.tipoUbicacion,
                u.edificio,
                u.piso,
                u.oficina,
                u.direccion
            FROM ColaboradorUbicacion cu
            INNER JOIN Ubicacion u
                ON u.idUbicacion = cu.idUbicacion
            WHERE cu.idColaborador = :idColaborador
            ORDER BY
                cu.esActual DESC,
                cu.fechaInicio DESC,
                cu.idColaboradorUbicacion DESC
            LIMIT 10
            '
        );

        $statement->execute([
            'idColaborador' => $collaboratorId,
        ]);

        return $statement->fetchAll();
    }

    public function emailExistsForAnotherUser(
        int $userId,
        string $email
    ): bool {
        $userStatement = $this->connection->prepare(
            '
            SELECT 1
            FROM Usuario
            WHERE idUsuario <> :idUsuario
              AND LOWER(TRIM(correo)) = LOWER(TRIM(:correo))
            LIMIT 1
            '
        );

        $userStatement->execute([
            'idUsuario' => $userId,
            'correo' => $email,
        ]);

        if ($userStatement->fetchColumn() !== false) {
            return true;
        }

        $collaboratorStatement = $this->connection->prepare(
            '
            SELECT 1
            FROM Colaborador
            WHERE COALESCE(idUsuario, 0) <> :idUsuario
              AND LOWER(TRIM(correo)) = LOWER(TRIM(:correo))
            LIMIT 1
            '
        );

        $collaboratorStatement->execute([
            'idUsuario' => $userId,
            'correo' => $email,
        ]);

        return $collaboratorStatement->fetchColumn() !== false;
    }

    public function identificationExistsForAnotherUser(
        int $userId,
        string $identification
    ): bool {
        $userStatement = $this->connection->prepare(
            '
            SELECT 1
            FROM Usuario
            WHERE idUsuario <> :idUsuario
              AND TRIM(COALESCE(cedula, "")) = TRIM(:cedula)
            LIMIT 1
            '
        );

        $userStatement->execute([
            'idUsuario' => $userId,
            'cedula' => $identification,
        ]);

        if ($userStatement->fetchColumn() !== false) {
            return true;
        }

        $collaboratorStatement = $this->connection->prepare(
            '
            SELECT 1
            FROM Colaborador
            WHERE COALESCE(idUsuario, 0) <> :idUsuario
              AND TRIM(identificacion) = TRIM(:identificacion)
            LIMIT 1
            '
        );

        $collaboratorStatement->execute([
            'idUsuario' => $userId,
            'identificacion' => $identification,
        ]);

        return $collaboratorStatement->fetchColumn() !== false;
    }

    public function findActiveLocationById(
        int $locationId
    ): ?array {
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
            WHERE idUbicacion = :idUbicacion
              AND activo = 1
            LIMIT 1
            '
        );

        $statement->execute([
            'idUbicacion' => $locationId,
        ]);

        $location = $statement->fetch();

        return is_array($location)
            ? $location
            : null;
    }

    public function updateProfile(
        int $userId,
        array $data
    ): void {
        try {
            $this->connection->beginTransaction();

            $userStatement = $this->connection->prepare(
                '
                UPDATE Usuario
                SET
                    cedula = :cedula,
                    nombre = :nombre,
                    apellido = :apellido,
                    correo = :correo
                WHERE idUsuario = :idUsuario
                '
            );

            $userStatement->execute([
                'cedula' => $data['cedula'],
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'correo' => $data['correo'],
                'idUsuario' => $userId,
            ]);

            $collaboratorId = $data['idColaborador'] ?? null;

            if (is_int($collaboratorId) && $collaboratorId > 0) {
                $collaboratorStatement = $this->connection->prepare(
                    '
                    UPDATE Colaborador
                    SET
                        identificacion = :identificacion,
                        nombre = :nombre,
                        apellido = :apellido,
                        correo = :correo,
                        telefono = :telefono,
                        cargo = :cargo,
                        departamento = :departamento
                    WHERE idColaborador = :idColaborador
                      AND idUsuario = :idUsuario
                    '
                );

                $collaboratorStatement->execute([
                    'identificacion' => $data['cedula'],
                    'nombre' => $data['nombre'],
                    'apellido' => $data['apellido'],
                    'correo' => $data['correo'],
                    'telefono' => $data['telefono'],
                    'cargo' => $data['cargo'],
                    'departamento' => $data['departamento'],
                    'idColaborador' => $collaboratorId,
                    'idUsuario' => $userId,
                ]);

                $newLocationId = $data['idUbicacion'] ?? null;
                $currentLocationId = $this->findCurrentLocationIdForUpdate(
                    $collaboratorId
                );

                if (
                    is_int($newLocationId)
                    && $newLocationId > 0
                    && $newLocationId !== $currentLocationId
                ) {
                    $closeStatement = $this->connection->prepare(
                        '
                        UPDATE ColaboradorUbicacion
                        SET
                            fechaFin = CURRENT_TIMESTAMP,
                            esActual = 0
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
                            observaciones,
                            esActual
                        ) VALUES (
                            :idColaborador,
                            :idUbicacion,
                            :observaciones,
                            1
                        )
                        '
                    );

                    $insertStatement->execute([
                        'idColaborador' => $collaboratorId,
                        'idUbicacion' => $newLocationId,
                        'observaciones' => $data['observacionesUbicacion'],
                    ]);
                }
            }

            $this->connection->commit();
        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw $exception;
        }
    }

    public function findPasswordHash(
        int $userId
    ): ?string {
        $statement = $this->connection->prepare(
            '
            SELECT passwordHash
            FROM Usuario
            WHERE idUsuario = :idUsuario
              AND activo = 1
            LIMIT 1
            '
        );

        $statement->execute([
            'idUsuario' => $userId,
        ]);

        $hash = $statement->fetchColumn();

        return is_string($hash)
            ? $hash
            : null;
    }

    public function updatePassword(
        int $userId,
        string $passwordHash
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE Usuario
            SET
                passwordHash = :passwordHash,
                intentosFallidos = 0,
                bloqueado = 0,
                fechaBloqueo = NULL
            WHERE idUsuario = :idUsuario
            '
        );

        $statement->execute([
            'passwordHash' => $passwordHash,
            'idUsuario' => $userId,
        ]);
    }

    private function findCurrentLocationIdForUpdate(
        int $collaboratorId
    ): ?int {
        $statement = $this->connection->prepare(
            '
            SELECT idUbicacion
            FROM ColaboradorUbicacion
            WHERE idColaborador = :idColaborador
              AND esActual = 1
              AND fechaFin IS NULL
            ORDER BY idColaboradorUbicacion DESC
            LIMIT 1
            FOR UPDATE
            '
        );

        $statement->execute([
            'idColaborador' => $collaboratorId,
        ]);

        $locationId = $statement->fetchColumn();

        return $locationId === false
            ? null
            : (int) $locationId;
    }
}
