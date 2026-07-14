<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\UsuarioRepositoryInterface;
use PDO;
use RuntimeException;
use Throwable;

final class UsuarioRepository implements
    UsuarioRepositoryInterface
{
    private const MAX_LOGIN_ATTEMPTS = 3;

    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection
            ?? Database::getConnection();
    }

    public function administratorExists(): bool
    {
        $statement = $this->connection->prepare(
            '
            SELECT 1
            FROM Usuario u
            INNER JOIN Rol r
                ON r.idRol = u.idRol
            WHERE r.nombreRol = :roleName
              AND u.activo = 1
            LIMIT 1
            '
        );

        $statement->execute([
            'roleName' => 'Administrador',
        ]);

        return $statement->fetchColumn() !== false;
    }

    public function findConflicts(
        string $cedula,
        string $usuario,
        string $correo
    ): array {
        $statement = $this->connection->prepare(
            '
            SELECT cedula, usuario, correo
            FROM Usuario
            WHERE cedula = :cedula
               OR usuario = :usuario
               OR correo = :correo
            '
        );

        $statement->execute([
            'cedula' => $cedula,
            'usuario' => $usuario,
            'correo' => $correo,
        ]);

        $conflicts = [];

        foreach ($statement->fetchAll() as $record) {
            if ($record['cedula'] === $cedula) {
                $conflicts['cedula'] =
                    'La cédula ya está registrada.';
            }

            if ($record['usuario'] === $usuario) {
                $conflicts['usuario'] =
                    'El nombre de usuario ya está registrado.';
            }

            if ($record['correo'] === $correo) {
                $conflicts['correo'] =
                    'El correo ya está registrado.';
            }
        }

        return $conflicts;
    }

    public function createAdministrator(
        array $userData,
        string $publicKey,
        string $publicKeyFingerprint
    ): int {
        try {
            $this->connection->beginTransaction();

            if ($this->administratorExists()) {
                throw new RuntimeException(
                    'Ya existe un administrador activo.'
                );
            }

            $roleId = $this->findAdministratorRoleId();

            $userStatement = $this->connection->prepare(
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

            $userStatement->execute([
                'cedula' => $userData['cedula'],
                'nombre' => $userData['nombre'],
                'apellido' => $userData['apellido'],
                'usuario' => $userData['usuario'],
                'correo' => $userData['correo'],
                'passwordHash' => $userData['passwordHash'],
                'idRol' => $roleId,
            ]);

            $userId = (int) $this->connection->lastInsertId();

            $keyStatement = $this->connection->prepare(
                '
                INSERT INTO LlavePublicaUsuario (
                    idUsuario,
                    llavePublica,
                    huellaDigital,
                    algoritmo,
                    versionLlave,
                    activa
                ) VALUES (
                    :idUsuario,
                    :llavePublica,
                    :huellaDigital,
                    :algoritmo,
                    1,
                    1
                )
                '
            );

            $keyStatement->execute([
                'idUsuario' => $userId,
                'llavePublica' => $publicKey,
                'huellaDigital' => $publicKeyFingerprint,
                'algoritmo' => 'RSA-2048-SHA256',
            ]);

            $this->connection->commit();

            return $userId;
        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw $exception;
        }
    }

    public function findForAuthentication(
        string $identifier
    ): ?array {
        $statement = $this->connection->prepare(
            '
            SELECT
                u.idUsuario,
                u.cedula,
                u.nombre,
                u.apellido,
                u.usuario,
                u.correo,
                u.passwordHash,
                u.activo,
                u.intentosFallidos,
                u.bloqueado,
                u.fechaBloqueo,
                u.ultimoAcceso,
                r.idRol,
                r.nombreRol
            FROM Usuario u
            INNER JOIN Rol r
                ON r.idRol = u.idRol
            WHERE u.usuario = :identifierUser
               OR u.correo = :identifierEmail
            LIMIT 1
            '
        );

        $statement->execute([
            'identifierUser' => $identifier,
            'identifierEmail' => $identifier,
        ]);

        $user = $statement->fetch();

        return is_array($user)
            ? $user
            : null;
    }

    public function recordFailedLogin(
        ?int $userId,
        string $identifier,
        string $ipAddress,
        ?string $userAgent,
        string $description,
        bool $increaseAttempts = true
    ): array {
        try {
            $this->connection->beginTransaction();

            $attempts = 0;
            $blocked = false;

            if ($userId !== null) {
                $statement = $this->connection->prepare(
                    '
                    SELECT
                        intentosFallidos,
                        bloqueado
                    FROM Usuario
                    WHERE idUsuario = :idUsuario
                    FOR UPDATE
                    '
                );

                $statement->execute([
                    'idUsuario' => $userId,
                ]);

                $userState = $statement->fetch();

                if (is_array($userState)) {
                    $attempts = (int) $userState[
                        'intentosFallidos'
                    ];

                    $blocked = (bool) $userState[
                        'bloqueado'
                    ];

                    if ($increaseAttempts && !$blocked) {
                        $attempts = min(
                            self::MAX_LOGIN_ATTEMPTS,
                            $attempts + 1
                        );

                        $blocked = $attempts
                            >= self::MAX_LOGIN_ATTEMPTS;

                        $updateStatement =
                            $this->connection->prepare(
                                '
                                UPDATE Usuario
                                SET
                                    intentosFallidos = :attempts,
                                    bloqueado = :blocked,
                                    fechaBloqueo = :blockedAt
                                WHERE idUsuario = :idUsuario
                                '
                            );

                        $updateStatement->execute([
                            'attempts' => $attempts,
                            'blocked' => $blocked ? 1 : 0,
                            'blockedAt' => $blocked
                                ? date('Y-m-d H:i:s')
                                : null,
                            'idUsuario' => $userId,
                        ]);
                    }
                }
            }

            $this->insertLoginHistory(
                $userId,
                $identifier,
                $ipAddress,
                $userAgent,
                false,
                $description
            );

            $this->connection->commit();

            return [
                'attempts' => $attempts,
                'remainingAttempts' => max(
                    0,
                    self::MAX_LOGIN_ATTEMPTS - $attempts
                ),
                'blocked' => $blocked,
            ];
        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw $exception;
        }
    }

    public function recordSuccessfulLogin(
        int $userId,
        string $identifier,
        string $ipAddress,
        ?string $userAgent
    ): void {
        try {
            $this->connection->beginTransaction();

            $statement = $this->connection->prepare(
                '
                UPDATE Usuario
                SET
                    intentosFallidos = 0,
                    bloqueado = 0,
                    fechaBloqueo = NULL,
                    ultimoAcceso = CURRENT_TIMESTAMP
                WHERE idUsuario = :idUsuario
                '
            );

            $statement->execute([
                'idUsuario' => $userId,
            ]);

            $this->insertLoginHistory(
                $userId,
                $identifier,
                $ipAddress,
                $userAgent,
                true,
                'Inicio de sesión correcto.'
            );

            $this->connection->commit();
        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw $exception;
        }
    }

    public function updatePasswordHash(
        int $userId,
        string $passwordHash
    ): void {
        $statement = $this->connection->prepare(
            '
            UPDATE Usuario
            SET passwordHash = :passwordHash
            WHERE idUsuario = :idUsuario
            '
        );

        $statement->execute([
            'passwordHash' => $passwordHash,
            'idUsuario' => $userId,
        ]);
    }

    private function insertLoginHistory(
        ?int $userId,
        string $identifier,
        string $ipAddress,
        ?string $userAgent,
        bool $success,
        string $description
    ): void {
        $statement = $this->connection->prepare(
            '
            INSERT INTO Historial_Login (
                idUsuario,
                usuarioIngresado,
                direccionIP,
                userAgent,
                exito,
                descripcion
            ) VALUES (
                :idUsuario,
                :usuarioIngresado,
                :direccionIP,
                :userAgent,
                :exito,
                :descripcion
            )
            '
        );

        $statement->execute([
            'idUsuario' => $userId,
            'usuarioIngresado' => $identifier,
            'direccionIP' => $ipAddress,
            'userAgent' => $userAgent,
            'exito' => $success ? 1 : 0,
            'descripcion' => $description,
        ]);
    }

    private function findAdministratorRoleId(): int
    {
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
            'roleName' => 'Administrador',
        ]);

        $roleId = $statement->fetchColumn();

        if ($roleId === false) {
            throw new RuntimeException(
                'No existe el rol Administrador.'
            );
        }

        return (int) $roleId;
    }
}