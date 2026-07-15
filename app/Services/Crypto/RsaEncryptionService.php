<?php

declare(strict_types=1);

namespace App\Services\Crypto;

use App\Interfaces\CifradoReversibleInterface;
use OpenSSLAsymmetricKey;
use RuntimeException;

final class RsaEncryptionService implements CifradoReversibleInterface
{
    public function __construct(
        private readonly string $privateKeyPath,
        private readonly string $publicKeyPath,
        private readonly string $privateKeyPassphrase
    ) {
    }

    public function cifrar(string $dato): string
    {
        if ($dato === '') {
            throw new RuntimeException(
                'No se puede cifrar una clave vacía.'
            );
        }

        $publicKey = $this->loadPublicKey();
        $details = openssl_pkey_get_details($publicKey);

        if (!is_array($details) || !isset($details['bits'])) {
            throw new RuntimeException(
                'No fue posible determinar el tamaño de la llave RSA.'
            );
        }

        $maximumLength = intdiv((int) $details['bits'], 8) - 42;

        if (strlen($dato) > $maximumLength) {
            throw new RuntimeException(
                'La clave supera el tamaño permitido para el cifrado RSA.'
            );
        }

        $encrypted = '';

        if (!openssl_public_encrypt(
            $dato,
            $encrypted,
            $publicKey,
            OPENSSL_PKCS1_OAEP_PADDING
        )) {
            throw new RuntimeException(
                'No fue posible cifrar la clave de licencia.'
            );
        }

        return base64_encode($encrypted);
    }

    public function descifrar(string $datoCifrado): string
    {
        if ($datoCifrado === '') {
            throw new RuntimeException(
                'No existe una clave cifrada para mostrar.'
            );
        }

        $encrypted = base64_decode($datoCifrado, true);

        if ($encrypted === false) {
            throw new RuntimeException(
                'La clave cifrada no tiene un formato válido.'
            );
        }

        $privateKey = $this->loadPrivateKey();
        $decrypted = '';

        if (!openssl_private_decrypt(
            $encrypted,
            $decrypted,
            $privateKey,
            OPENSSL_PKCS1_OAEP_PADDING
        )) {
            throw new RuntimeException(
                'No fue posible descifrar la clave de licencia.'
            );
        }

        return $decrypted;
    }

    private function loadPublicKey(): OpenSSLAsymmetricKey
    {
        if (!is_file($this->publicKeyPath)) {
            throw new RuntimeException('No se encontró la llave pública.');
        }

        $content = file_get_contents($this->publicKeyPath);
        $key = is_string($content)
            ? openssl_pkey_get_public($content)
            : false;

        if (!$key instanceof OpenSSLAsymmetricKey) {
            throw new RuntimeException('La llave pública no es válida.');
        }

        return $key;
    }

    private function loadPrivateKey(): OpenSSLAsymmetricKey
    {
        if (!is_file($this->privateKeyPath)) {
            throw new RuntimeException('No se encontró la llave privada.');
        }

        $content = file_get_contents($this->privateKeyPath);
        $key = is_string($content)
            ? openssl_pkey_get_private(
                $content,
                $this->privateKeyPassphrase
            )
            : false;

        if (!$key instanceof OpenSSLAsymmetricKey) {
            throw new RuntimeException(
                'La llave privada o su contraseña no son válidas.'
            );
        }

        return $key;
    }
}
