<?php

declare(strict_types=1);

namespace App\Services\Crypto;

use RuntimeException;

final class KeyPairGenerator
{
        public function __construct(
        private readonly int $keyBits = 2048,
        private readonly string $digestAlgorithm = 'sha256',
        private readonly string $opensslConfigPath = ''
    ) {
        if ($this->keyBits < 2048) {
            throw new RuntimeException(
                'La llave RSA debe tener al menos 2048 bits.'
            );
        }

        if (
            $this->opensslConfigPath === ''
            || !is_file($this->opensslConfigPath)
        ) {
            throw new RuntimeException(
                'No se encontró un archivo openssl.cnf válido.'
            );
        }
    }

    public function generate(
        string $privateKeyPath,
        string $publicKeyPath,
        string $passphrase
    ): void {
        $this->validateOpenSsl();

        $configuration = [
            'config' => $this->opensslConfigPath,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => $this->keyBits,
            'digest_alg' => $this->digestAlgorithm,
        ];

        $keyResource = openssl_pkey_new(
            $configuration
        );

        if ($keyResource === false) {
            $errors = [];

            while ($openSslError = openssl_error_string()) {
                $errors[] = $openSslError;
            }

            $details = $errors !== []
                ? implode(' | ', $errors)
                : 'OpenSSL no devolvió detalles adicionales.';

            throw new RuntimeException(
                'No fue posible generar el par de llaves RSA. '
                . $details
            );
        }

        $privateKey = '';

        $privateExported = openssl_pkey_export(
            $keyResource,
            $privateKey,
            $passphrase,
            $configuration
        );

        if (!$privateExported) {
            throw new RuntimeException(
                'No fue posible exportar la llave privada.'
            );
        }

        $keyDetails = openssl_pkey_get_details(
            $keyResource
        );

        if (
            !is_array($keyDetails)
            || !isset($keyDetails['key'])
            || !is_string($keyDetails['key'])
        ) {
            throw new RuntimeException(
                'No fue posible obtener la llave pública.'
            );
        }

        $this->ensureDirectoryExists(
            dirname($privateKeyPath)
        );

        $this->ensureDirectoryExists(
            dirname($publicKeyPath)
        );

        $privateResult = file_put_contents(
            $privateKeyPath,
            $privateKey,
            LOCK_EX
        );

        if ($privateResult === false) {
            throw new RuntimeException(
                'No fue posible guardar la llave privada.'
            );
        }

        $publicResult = file_put_contents(
            $publicKeyPath,
            $keyDetails['key'],
            LOCK_EX
        );

        if ($publicResult === false) {
            throw new RuntimeException(
                'No fue posible guardar la llave pública.'
            );
        }

        /*
         * En Linux restringe los permisos.
         * En Windows puede no tener el mismo efecto.
         */
        @chmod($privateKeyPath, 0600);
        @chmod($publicKeyPath, 0644);
    }

    private function ensureDirectoryExists(
        string $directory
    ): void {
        if (is_dir($directory)) {
            return;
        }

        if (
            !mkdir($directory, 0775, true)
            && !is_dir($directory)
        ) {
            throw new RuntimeException(
                "No fue posible crear el directorio {$directory}."
            );
        }
    }

    private function validateOpenSsl(): void
    {
        if (!extension_loaded('openssl')) {
            throw new RuntimeException(
                'La extensión OpenSSL no está habilitada.'
            );
        }
    }
}