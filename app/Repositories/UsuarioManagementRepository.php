<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Core\Roles;
use App\Interfaces\UsuarioManagementRepositoryInterface;
use PDO;
use RuntimeException;
use Throwable;

final class UsuarioManagementRepository implements
    UsuarioManagementRepositoryInterface
{
    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection
            ?? Database::getConnection();
    }

    public function listUsers(array $filters = []): array
    {
        $sql = '
            SELECT
                u.idUsuario,
                u.cedula,
                u.nombre,
                u.apellido,
                u.usuario,
                u.correo,
                u.activo,
                u.intentosFallidos,
                u.bloqueado,
                u.fechaBloqueo,
                u.ultimoAcceso,
                u.fechaRegistro,
                r.idRol,
                r.nombreRol,
                c.idColaborador
            FROM Usuario u
            INNER JOIN Rol r
                ON r.idRol = u.idRol
            LEFT JOIN Colaborador c
                ON c.idUsuario = u.idUsuario
            WHERE r.nombreRol IN (
                :roleAdministrator,
                :roleTechnician,
                :roleCollaborator
            )
        ';

        $parameters = [
            'roleAdministrator' => Roles::ADMINISTRADOR,
            'roleTechnician' => Roles::TECNICO,
            'roleCollaborator' => Roles::COLABORADOR,
        ];

        $search = trim(
            (string) ($filters['search'] ?? '')
        );

        $role = trim(
            (string) ($filters['role'] ?? '')
        );

        $status = (string) (
            $filters['status'] ?? ''
        );

        if ($search !== '') {
            $searchPattern = '%' . $search . '%';

            $sql .= '
                AND (
                    u.cedula LIKE :searchCedula
                    OR u.nombre LIKE :searchName
                    OR u.apellido LIKE :searchLastName
                    OR CONCAT(
                        u.nombre,
                        " ",
                        u.apellido
                    ) LIKE :searchFullName
                    OR u.usuario LIKE :searchUsername
                    OR u.correo LIKE :searchEmail
                )
            ';

            $parameters['searchCedula'] =
                $searchPattern;

            $parameters['searchName'] =
                $searchPattern;

            $parameters['searchLastName'] =
                $searchPattern;

            $parameters['searchFullName'] =
                $searchPattern;

            $parameters['searchUsername'] =
                $searchPattern;

            $parameters['searchEmail'] =
                $searchPattern;
        }

        if (Roles::isValid($role)) {
            $sql .= '
                AND r.nombreRol = :selectedRole
            ';

            $parameters['selectedRole'] = $role;
        }

        if ($status === '1' || $status === '0') {
            $sql .= '
                AND u.activo = :selectedStatus
            ';

            $parameters['selectedStatus'] =
                (int) $status;
        }

        $sql .= '
            ORDER BY
                u.activo DESC,
                u.apellido ASC,
                u.nombre ASC
        ';

        $statement = $this->connection->prepare(
            $sql
        );

        $statement->execute(
            $parameters
        );

        return $statement->fetchAll();
    }

    public function listActiveRoles(): array
    {
        $statement = $this->connection->prepare(
            '
            SELECT idRol, nombreRol
            FROM Rol
            WHERE activo = 1
              AND nombreRol IN (
                  :administrador,
                  :tecnico,
                  :colaborador
              )
            ORDER BY
                CASE nombreRol
                    WHEN :administradorOrder THEN 1
                    WHEN :tecnicoOrder THEN 2
                    WHEN :colaboradorOrder THEN 3
                    ELSE 4
                END
            '
        );

        $statement->execute([
            'administrador' => Roles::ADMINISTRADOR,
            'tecnico' => Roles::TECNICO,
            'colaborador' => Roles::COLABORADOR,
            'administradorOrder' => Roles::ADMINISTRADOR,
            'tecnicoOrder' => Roles::TECNICO,
            'colaboradorOrder' => Roles::COLABORADOR,
        ]);

        return $statement->fetchAll();
    }

    public function findById(int $userId): ?array
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
                u.activo,
                u.intentosFallidos,
                u.bloqueado,
                u.fechaBloqueo,
                u.ultimoAcceso,
                u.fechaRegistro,
                r.idRol,
                r.nombreRol,
                c.idColaborador,
                c.telefono,
                c.cargo,
                c.departamento,
                c.fechaIngreso,
                c.fechaSalida,
                c.activo AS colaboradorActivo
            FROM Usuario u
            INNER JOIN Rol r
                ON r.idRol = u.idRol
            LEFT JOIN Colaborador c
                ON c.idUsuario = u.idUsuario
            WHERE u.idUsuario = :idUsuario
            LIMIT 1
            '
        );

        $statement->execute([
            'idUsuario' => $userId,
        ]);

        $user = $statement->fetch();

        return is_array($user)
            ? $user
            : null;
    }

    public function findConflictsExcluding(
        int $userId,
        string $cedula,
        string $usuario,
        string $correo
    ): array {
        $statement = $this->connection->prepare(
            '
            SELECT cedula, usuario, correo
            FROM Usuario
            WHERE idUsuario <> :idUsuario
              AND (
                  cedula = :cedula
                  OR usuario = :usuario
                  OR correo = :correo
              )
            '
        );

        $statement->execute([
            'idUsuario' => $userId,
            'cedula' => $cedula,
            'usuario' => $usuario,
            'correo' => $correo,
        ]);

        return $this->buildConflictErrors(
            $statement->fetchAll(),
            $cedula,
            $usuario,
            $correo
        );
    }

    public function createUser(
        array $userData,
        ?array $collaboratorData
    ): int {
        try {
            $this->connection->beginTransaction();

            $roleId = $this->findRoleId(
                $userData['nombreRol']
            );

            $statement = $this->connection->prepare(
                '
                INSERT INTO Usuario (
                    cedula,
                    nombre,
                    apellido,
                    usuario,
                    correo,
                    passwordHash,
                    idRol,
                    activo,
                    intentosFallidos,
                    bloqueado
                ) VALUES (
                    :cedula,
                    :nombre,
                    :apellido,
                    :usuario,
                    :correo,
                    :passwordHash,
                    :idRol,
                    1,
                    0,
                    0
                )
                '
            );

            $statement->execute([
                'cedula' => $userData['cedula'],
                'nombre' => $userData['nombre'],
                'apellido' => $userData['apellido'],
                'usuario' => $userData['usuario'],
                'correo' => $userData['correo'],
                'passwordHash' => $userData['passwordHash'],
                'idRol' => $roleId,
            ]);

            $userId = (int) $this->connection->lastInsertId();

            if (
                $userData['nombreRol'] === Roles::COLABORADOR
                && $collaboratorData !== null
            ) {
                $this->upsertCollaborator(
                    $userId,
                    $userData,
                    $collaboratorData
                );
            }

            $this->connection->commit();

            return $userId;
        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw $exception;
        }
    }

    public function updateUser(
        int $userId,
        array $userData,
        ?array $collaboratorData
    ): void {
        try {
            $this->connection->beginTransaction();

            $roleId = $this->findRoleId(
                $userData['nombreRol']
            );

            $sql = '
                UPDATE Usuario
                SET
                    cedula = :cedula,
                    nombre = :nombre,
                    apellido = :apellido,
                    usuario = :usuario,
                    correo = :correo,
                    idRol = :idRol
            ';

            $parameters = [
                'cedula' => $userData['cedula'],
                'nombre' => $userData['nombre'],
                'apellido' => $userData['apellido'],
                'usuario' => $userData['usuario'],
                'correo' => $userData['correo'],
                'idRol' => $roleId,
                'idUsuario' => $userId,
            ];

            if (!empty($userData['passwordHash'])) {
                $sql .= ',
                    passwordHash = :passwordHash
                ';

                $parameters['passwordHash'] =
                    $userData['passwordHash'];
            }

            $sql .= ' WHERE idUsuario = :idUsuario ';

            $statement = $this->connection->prepare($sql);
            $statement->execute($parameters);

            if (
                $userData['nombreRol'] === Roles::COLABORADOR
                && $collaboratorData !== null
            ) {
                $this->upsertCollaborator(
                    $userId,
                    $userData,
                    $collaboratorData
                );
            } else {
                $this->deactivateCollaboratorProfile($userId);
            }

            $this->connection->commit();
        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw $exception;
        }
    }

    public function setActiveState(
        int $userId,
        bool $active
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE Usuario
            SET activo = :activo
            WHERE idUsuario = :idUsuario
            '
        );

        $statement->execute([
            'activo' => $active ? 1 : 0,
            'idUsuario' => $userId,
        ]);
    }

    public function unlock(int $userId): void
    {
        $statement = $this->connection->prepare(
            '
            UPDATE Usuario
            SET
                intentosFallidos = 0,
                bloqueado = 0,
                fechaBloqueo = NULL
            WHERE idUsuario = :idUsuario
            '
        );

        $statement->execute([
            'idUsuario' => $userId,
        ]);
    }

    public function countActiveAdministrators(): int
    {
        $statement = $this->connection->prepare(
            '
            SELECT COUNT(*)
            FROM Usuario u
            INNER JOIN Rol r
                ON r.idRol = u.idRol
            WHERE r.nombreRol = :roleName
              AND u.activo = 1
            '
        );

        $statement->execute([
            'roleName' => Roles::ADMINISTRADOR,
        ]);

        return (int) $statement->fetchColumn();
    }

    private function findRoleId(string $roleName): int
    {
        if (!Roles::isValid($roleName)) {
            throw new RuntimeException(
                'El rol seleccionado no es válido.'
            );
        }

        $statement = $this->connection->prepare(
            '
            SELECT idRol
            FROM Rol
            WHERE nombreRol = :roleName
              AND activo = 1
            LIMIT 1
            '
        );

        $statement->execute([
            'roleName' => $roleName,
        ]);

        $roleId = $statement->fetchColumn();

        if ($roleId === false) {
            throw new RuntimeException(
                'El rol seleccionado no está disponible.'
            );
        }

        return (int) $roleId;
    }

    private function upsertCollaborator(
        int $userId,
        array $userData,
        array $collaboratorData
    ): void {
        $statement = $this->connection->prepare(
            '
            INSERT INTO Colaborador (
                idUsuario,
                identificacion,
                nombre,
                apellido,
                correo,
                telefono,
                cargo,
                departamento,
                activo,
                fechaIngreso,
                fechaSalida
            ) VALUES (
                :idUsuario,
                :identificacion,
                :nombre,
                :apellido,
                :correo,
                :telefono,
                :cargo,
                :departamento,
                1,
                :fechaIngreso,
                NULL
            )
            ON DUPLICATE KEY UPDATE
                identificacion = VALUES(identificacion),
                nombre = VALUES(nombre),
                apellido = VALUES(apellido),
                correo = VALUES(correo),
                telefono = VALUES(telefono),
                cargo = VALUES(cargo),
                departamento = VALUES(departamento),
                activo = 1,
                fechaIngreso = VALUES(fechaIngreso),
                fechaSalida = NULL
            '
        );

        $statement->execute([
            'idUsuario' => $userId,
            'identificacion' => $userData['cedula'],
            'nombre' => $userData['nombre'],
            'apellido' => $userData['apellido'],
            'correo' => $userData['correo'],
            'telefono' => $collaboratorData['telefono'],
            'cargo' => $collaboratorData['cargo'],
            'departamento' => $collaboratorData['departamento'],
            'fechaIngreso' => $collaboratorData['fechaIngreso'],
        ]);
    }

    private function deactivateCollaboratorProfile(
        int $userId
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE Colaborador
            SET
                activo = 0,
                fechaSalida = COALESCE(
                    fechaSalida,
                    CURRENT_DATE
                )
            WHERE idUsuario = :idUsuario
            '
        );

        $statement->execute([
            'idUsuario' => $userId,
        ]);
    }

    private function buildConflictErrors(
        array $records,
        string $cedula,
        string $usuario,
        string $correo
    ): array {
        $errors = [];

        foreach ($records as $record) {
            if ($record['cedula'] === $cedula) {
                $errors['cedula'] =
                    'La cédula ya está registrada.';
            }

            if ($record['usuario'] === $usuario) {
                $errors['usuario'] =
                    'El nombre de usuario ya está registrado.';
            }

            if ($record['correo'] === $correo) {
                $errors['correo'] =
                    'El correo ya está registrado.';
            }
        }

        return $errors;
    }
}