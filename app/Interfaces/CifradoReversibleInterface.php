<?php

declare(strict_types=1);

namespace App\Interfaces;

interface CifradoReversibleInterface
{
    public function cifrar(string $dato): string;

    public function descifrar(string $datoCifrado): string;
}
