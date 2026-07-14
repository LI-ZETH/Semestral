<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class ValidationException extends RuntimeException
{
    public function __construct(
        private readonly array $errors
    ) {
        parent::__construct('Los datos enviados no son válidos.');
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}