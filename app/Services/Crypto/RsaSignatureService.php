<?php

declare(strict_types=1);

namespace App\Services\Crypto;

use App\Interfaces\TransformacionCriptograficaInterface;
use OpenSSLAsymmetricKey;
use RuntimeException;

final class RsaSignatureService implements
    TransformacionCriptograficaInterface
{
    public function __construct(
        private readonly string $privateKeyPath,
        private readonly string $publicKeyPath,
        private readonly string $privateKeyPassphrase
    ) {
    }

    public function transformar(string $dato): string
    {
        if ($dato === '') {
            throw new RuntimeException(
                'No se puede firmar información vacía.'
            );
        }

        $privateKey = $this->loadPrivateKey();

        $signature = '';

        $signed = openssl_sign(
            $dato,
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA256
        );

        if (!$signed) {
            throw new RuntimeException(
                'No fue posible generar la firma digital.'
            );
        }

        return base64_encode(
            $signature
        );
    }

    public function verificar(
        string $dato,
        string $resultado
    ): bool {
        if ($dato === '' || $resultado === '') {
            return false;
        }

        $signature = base64_decode(
            $resultado,
            true
        );

        if ($signature === false) {
            return false;
        }

        $publicKey = $this->loadPublicKey();

        $verificationResult = openssl_verify(
            $dato,
            $signature,
            $publicKey,
            OPENSSL_ALGO_SHA256
        );

        if ($verificationResult === 1) {
            return true;
        }

        if ($verificationResult === 0) {
            return false;
        }

        throw new RuntimeException(
            'Ocurrió un error verificando la firma digital.'
        );
    }

    public function getPublicKeyContent(): string
    {
        $publicKey = file_get_contents(
            $this->publicKeyPath
        );

        if (!is_string($publicKey) || $publicKey === '') {
            throw new RuntimeException(
                'No fue posible leer la llave pública.'
            );
        }

        return $publicKey;
    }

    public function getPublicKeyFingerprint(): string
    {
        return hash(
            'sha256',
            $this->getPublicKeyContent()
        );
    }

    private function loadPrivateKey(): OpenSSLAsymmetricKey
    {
        if (!is_file($this->privateKeyPath)) {
            throw new RuntimeException(
                'No se encontró la llave privada.'
            );
        }

        $privateKeyContent = file_get_contents(
            $this->privateKeyPath
        );

        if (
            !is_string($privateKeyContent)
            || $privateKeyContent === ''
        ) {
            throw new RuntimeException(
                'No fue posible leer la llave privada.'
            );
        }

        $privateKey = openssl_pkey_get_private(
            $privateKeyContent,
            $this->privateKeyPassphrase
        );

        if (!$privateKey instanceof OpenSSLAsymmetricKey) {
            throw new RuntimeException(
                'La llave privada o su contraseña no son válidas.'
            );
        }

        return $privateKey;
    }

    private function loadPublicKey(): OpenSSLAsymmetricKey
    {
        if (!is_file($this->publicKeyPath)) {
            throw new RuntimeException(
                'No se encontró la llave pública.'
            );
        }

        $publicKeyContent = file_get_contents(
            $this->publicKeyPath
        );

        if (
            !is_string($publicKeyContent)
            || $publicKeyContent === ''
        ) {
            throw new RuntimeException(
                'No fue posible leer la llave pública.'
            );
        }

        $publicKey = openssl_pkey_get_public(
            $publicKeyContent
        );

        if (!$publicKey instanceof OpenSSLAsymmetricKey) {
            throw new RuntimeException(
                'La llave pública no es válida.'
            );
        }

        return $publicKey;
    }
}