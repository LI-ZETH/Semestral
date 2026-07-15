<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Interfaces\AuditoriaRepositoryInterface;
use PDO;

final class AuditoriaRepository implements AuditoriaRepositoryInterface
{
    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $this->connection = $connection
            ?? Database::getConnection();
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function getLastHashForUpdate(): ?string
    {
        $statement = $this->connection->query(
            '
            SELECT hashRegistro
            FROM Auditoria
            ORDER BY idAuditoria DESC
            LIMIT 1
            FOR UPDATE
            '
        );

        $hash = $statement->fetchColumn();

        return is_string($hash) && $hash !== ''
            ? $hash
            : null;
    }

    public function findPublicKeyByFingerprint(
        string $fingerprint
    ): ?array {
        $statement = $this->connection->prepare(
            '
            SELECT
                idLlavePublica,
                idUsuario,
                llavePublica,
                huellaDigital,
                algoritmo,
                versionLlave,
                activa
            FROM LlavePublicaUsuario
            WHERE huellaDigital = :fingerprint
              AND activa = 1
            LIMIT 1
            '
        );

        $statement->execute([
            'fingerprint' => $fingerprint,
        ]);

        $key = $statement->fetch();

        return is_array($key)
            ? $key
            : null;
    }

    public function findSigningOwnerId(): ?int
    {
        $statement = $this->connection->query(
            '
            SELECT u.idUsuario
            FROM Usuario u
            INNER JOIN Rol r
                ON r.idRol = u.idRol
            WHERE u.activo = 1
              AND r.nombreRol = "Administrador"
            ORDER BY u.idUsuario ASC
            LIMIT 1
            '
        );

        $userId = $statement->fetchColumn();

        return $userId !== false
            ? (int) $userId
            : null;
    }

    public function createSystemPublicKey(
        int $userId,
        string $publicKey,
        string $fingerprint
    ): int {
        $versionStatement = $this->connection->prepare(
            '
            SELECT COALESCE(MAX(versionLlave), 0) + 1
            FROM LlavePublicaUsuario
            WHERE idUsuario = :userId
            '
        );

        $versionStatement->execute([
            'userId' => $userId,
        ]);

        $version = (int) $versionStatement->fetchColumn();

        $statement = $this->connection->prepare(
            '
            INSERT INTO LlavePublicaUsuario (
                idUsuario,
                llavePublica,
                huellaDigital,
                algoritmo,
                versionLlave,
                activa
            ) VALUES (
                :userId,
                :publicKey,
                :fingerprint,
                "RSA-2048-SHA256",
                :version,
                1
            )
            '
        );

        $statement->execute([
            'userId' => $userId,
            'publicKey' => $publicKey,
            'fingerprint' => $fingerprint,
            'version' => $version,
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function insert(array $data): int
    {
        $statement = $this->connection->prepare(
            '
            INSERT INTO Auditoria (
                idUsuario,
                idLlavePublica,
                modulo,
                accion,
                tablaAfectada,
                idRegistro,
                descripcion,
                datosAnteriores,
                datosNuevos,
                direccionIP,
                userAgent,
                hashAnterior,
                hashRegistro,
                firmaDigital,
                algoritmoFirma,
                fecha
            ) VALUES (
                :idUsuario,
                :idLlavePublica,
                :modulo,
                :accion,
                :tablaAfectada,
                :idRegistro,
                :descripcion,
                :datosAnteriores,
                :datosNuevos,
                :direccionIP,
                :userAgent,
                :hashAnterior,
                :hashRegistro,
                :firmaDigital,
                :algoritmoFirma,
                :fecha
            )
            '
        );

        $statement->execute([
            'idUsuario' => $data['idUsuario'],
            'idLlavePublica' => $data['idLlavePublica'],
            'modulo' => $data['modulo'],
            'accion' => $data['accion'],
            'tablaAfectada' => $data['tablaAfectada'],
            'idRegistro' => $data['idRegistro'],
            'descripcion' => $data['descripcion'],
            'datosAnteriores' => $data['datosAnteriores'],
            'datosNuevos' => $data['datosNuevos'],
            'direccionIP' => $data['direccionIP'],
            'userAgent' => $data['userAgent'],
            'hashAnterior' => $data['hashAnterior'],
            'hashRegistro' => $data['hashRegistro'],
            'firmaDigital' => $data['firmaDigital'],
            'algoritmoFirma' => $data['algoritmoFirma'],
            'fecha' => $data['fecha'],
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function getAllForVerification(): array
    {
        $statement = $this->connection->query(
            '
            SELECT
                au.idAuditoria,
                au.idUsuario,
                au.idLlavePublica,
                au.modulo,
                au.accion,
                au.tablaAfectada,
                au.idRegistro,
                au.descripcion,
                au.datosAnteriores,
                au.datosNuevos,
                au.direccionIP,
                au.userAgent,
                au.hashAnterior,
                au.hashRegistro,
                au.firmaDigital,
                au.algoritmoFirma,
                au.fecha,
                lp.llavePublica
            FROM Auditoria au
            LEFT JOIN LlavePublicaUsuario lp
                ON lp.idLlavePublica = au.idLlavePublica
            ORDER BY au.idAuditoria ASC
            '
        );

        return $statement->fetchAll();
    }
}
