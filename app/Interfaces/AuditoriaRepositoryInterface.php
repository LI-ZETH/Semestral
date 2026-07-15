<?php

declare(strict_types=1);

namespace App\Interfaces;

interface AuditoriaRepositoryInterface
{
    public function getConnection(): \PDO;

    public function getLastHashForUpdate(): ?string;

    public function findPublicKeyByFingerprint(
        string $fingerprint
    ): ?array;

    public function findSigningOwnerId(): ?int;

    public function createSystemPublicKey(
        int $userId,
        string $publicKey,
        string $fingerprint
    ): int;

    public function insert(array $data): int;

    public function getAllForVerification(): array;
}
