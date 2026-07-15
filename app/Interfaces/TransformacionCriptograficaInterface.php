<?php

declare(strict_types=1);

namespace App\Interfaces;

interface TransformacionCriptograficaInterface
{
    /**
     * Transforma un dato utilizando el mecanismo criptográfico.
     */
    public function transformar(string $dato): string;

    /**
     * Verifica que un dato corresponda con el resultado transformado.
     */
    public function verificar(
        string $dato,
        string $resultado
    ): bool;
}