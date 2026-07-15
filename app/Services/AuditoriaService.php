<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\AuditoriaRepositoryInterface;
use App\Repositories\AuditoriaRepository;
use App\Services\Crypto\CanonicalJson;
use App\Services\Crypto\RsaSignatureService;
use JsonException;
use RuntimeException;
use Throwable;

final class AuditoriaService
{
    private RsaSignatureService $rsaService;

    public function __construct(
        private readonly AuditoriaRepositoryInterface $repository
    ) {
        $configurationPath = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'config'
            . DIRECTORY_SEPARATOR
            . 'crypto.php';

        if (!is_file($configurationPath)) {
            throw new RuntimeException(
                'No existe config/crypto.php para firmar la auditoría.'
            );
        }

        $configuration = require $configurationPath;

        $this->rsaService = new RsaSignatureService(
            (string) $configuration['private_key_path'],
            (string) $configuration['public_key_path'],
            (string) $configuration['private_key_passphrase']
        );
    }

    public static function build(): self
    {
        return new self(
            new AuditoriaRepository()
        );
    }

    public function record(array $event): int
    {
        $connection = $this->repository->getConnection();

        try {
            $connection->beginTransaction();

            $previousHash = $this->repository
                ->getLastHashForUpdate();

            $keyId = $this->resolveSigningKeyId();
            $date = date('Y-m-d H:i:s');

            $oldData = $this->encodeNullableData(
                $event['datosAnteriores'] ?? null
            );

            $newData = $this->encodeNullableData(
                $event['datosNuevos'] ?? null
            );

            $payload = [
                'idUsuario' => isset($event['idUsuario'])
                    ? (int) $event['idUsuario']
                    : null,
                'idLlavePublica' => $keyId,
                'modulo' => (string) ($event['modulo'] ?? 'SISTEMA'),
                'accion' => (string) ($event['accion'] ?? 'ACCION'),
                'tablaAfectada' => $this->nullableString(
                    $event['tablaAfectada'] ?? null
                ),
                'idRegistro' => $this->nullableString(
                    $event['idRegistro'] ?? null
                ),
                'descripcion' => $this->nullableString(
                    $event['descripcion'] ?? null
                ),
                'datosAnteriores' => $oldData,
                'datosNuevos' => $newData,
                'direccionIP' => $this->nullableString(
                    $event['direccionIP'] ?? null
                ),
                'userAgent' => $this->nullableString(
                    $event['userAgent'] ?? null
                ),
                'hashAnterior' => $previousHash,
                'fecha' => $date,
            ];

            $hash = hash(
                'sha256',
                CanonicalJson::encode($payload)
            );

            $signature = $this->rsaService->transformar($hash);

            $auditId = $this->repository->insert([
                ...$payload,
                'hashRegistro' => $hash,
                'firmaDigital' => $signature,
                'algoritmoFirma' => 'RSA-2048-SHA256',
            ]);

            $connection->commit();

            return $auditId;
        } catch (Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $exception;
        }
    }

    public function verifyIntegrity(): array
    {
        $records = $this->repository
            ->getAllForVerification();

        $expectedPreviousHash = null;
        $verifiedRecords = 0;
        $firstInvalidId = null;
        $signatureErrors = 0;

        foreach ($records as $record) {
            $payload = [
                'idUsuario' => $record['idUsuario'] !== null
                    ? (int) $record['idUsuario']
                    : null,
                'idLlavePublica' => $record['idLlavePublica'] !== null
                    ? (int) $record['idLlavePublica']
                    : null,
                'modulo' => (string) $record['modulo'],
                'accion' => (string) $record['accion'],
                'tablaAfectada' => $record['tablaAfectada'],
                'idRegistro' => $record['idRegistro'],
                'descripcion' => $record['descripcion'],
                'datosAnteriores' => $record['datosAnteriores'],
                'datosNuevos' => $record['datosNuevos'],
                'direccionIP' => $record['direccionIP'],
                'userAgent' => $record['userAgent'],
                'hashAnterior' => $record['hashAnterior'],
                'fecha' => (string) $record['fecha'],
            ];

            $calculatedHash = hash(
                'sha256',
                CanonicalJson::encode($payload)
            );

            $chainIsValid = hash_equals(
                (string) ($record['hashAnterior'] ?? ''),
                (string) ($expectedPreviousHash ?? '')
            );

            $hashIsValid = hash_equals(
                (string) $record['hashRegistro'],
                $calculatedHash
            );

            $signatureIsValid = $this->verifyStoredSignature(
                $calculatedHash,
                (string) ($record['firmaDigital'] ?? ''),
                (string) ($record['llavePublica'] ?? '')
            );

            if (!$signatureIsValid) {
                $signatureErrors++;
            }

            if (!$chainIsValid || !$hashIsValid || !$signatureIsValid) {
                $firstInvalidId = (int) $record['idAuditoria'];
                break;
            }

            $verifiedRecords++;
            $expectedPreviousHash = (string) $record['hashRegistro'];
        }

        return [
            'valid' => $firstInvalidId === null,
            'total' => count($records),
            'verified' => $verifiedRecords,
            'firstInvalidId' => $firstInvalidId,
            'signatureErrors' => $signatureErrors,
        ];
    }

    private function resolveSigningKeyId(): int
    {
        $fingerprint = $this->rsaService
            ->getPublicKeyFingerprint();

        $existingKey = $this->repository
            ->findPublicKeyByFingerprint($fingerprint);

        if ($existingKey !== null) {
            return (int) $existingKey['idLlavePublica'];
        }

        $ownerId = $this->repository->findSigningOwnerId();

        if ($ownerId === null) {
            throw new RuntimeException(
                'No existe un administrador activo para registrar la llave de auditoría.'
            );
        }

        return $this->repository->createSystemPublicKey(
            $ownerId,
            $this->rsaService->getPublicKeyContent(),
            $fingerprint
        );
    }

    private function encodeNullableData(mixed $data): ?string
    {
        if ($data === null || $data === []) {
            return null;
        }

        if (is_array($data)) {
            return CanonicalJson::encode($data);
        }

        return CanonicalJson::encode([
            'valor' => $data,
        ]);
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $string = trim((string) $value);

        return $string !== ''
            ? $string
            : null;
    }

    private function verifyStoredSignature(
        string $hash,
        string $signature,
        string $publicKeyContent
    ): bool {
        if (
            $signature === ''
            || $publicKeyContent === ''
        ) {
            return false;
        }

        $decodedSignature = base64_decode(
            $signature,
            true
        );

        if ($decodedSignature === false) {
            return false;
        }

        $publicKey = openssl_pkey_get_public(
            $publicKeyContent
        );

        if ($publicKey === false) {
            return false;
        }

        return openssl_verify(
            $hash,
            $decodedSignature,
            $publicKey,
            OPENSSL_ALGO_SHA256
        ) === 1;
    }
}
