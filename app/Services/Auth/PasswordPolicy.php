<?php

declare(strict_types=1);

namespace App\Services\Auth;

final class PasswordPolicy
{
    public function validate(string $password): array
    {
        $errors = [];

        if (strlen($password) < 10) {
            $errors[] =
                'Debe contener al menos 10 caracteres.';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] =
                'Debe contener una letra mayúscula.';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] =
                'Debe contener una letra minúscula.';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] =
                'Debe contener al menos un número.';
        }

        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] =
                'Debe contener un carácter especial.';
        }

        return $errors;
    }
}