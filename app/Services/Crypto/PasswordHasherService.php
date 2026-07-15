<?php

declare(strict_types=1);

namespace App\Services\Crypto;

use App\Interfaces\TransformacionCriptograficaInterface;
use RuntimeException;

final class PasswordHasherService implements
    TransformacionCriptograficaInterface
{
    public function transformar(string $dato): string
    {
        if ($dato === '') {
            throw new RuntimeException(
                'La contraseña no puede estar vacía.'
            );
        }

        $hash = password_hash(
            $dato,
            PASSWORD_DEFAULT
        );

        if (!is_string($hash)) {
            throw new RuntimeException(
                'No fue posible proteger la contraseña.'
            );
        }

        return $hash;
    }

    public function verificar(
        string $dato,
        string $resultado
    ): bool {
        if ($dato === '' || $resultado === '') {
            return false;
        }

        return password_verify(
            $dato,
            $resultado
        );
    }

    public function necesitaActualizacion(
        string $hash
    ): bool {
        return password_needs_rehash(
            $hash,
            PASSWORD_DEFAULT
        );
    }
}