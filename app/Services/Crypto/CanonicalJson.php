<?php

declare(strict_types=1);

namespace App\Services\Crypto;

use JsonException;

final class CanonicalJson
{
    private function __construct()
    {
    }

    /**
     * Convierte un arreglo en JSON ordenando sus claves.
     *
     * @throws JsonException
     */
    public static function encode(array $data): string
    {
        $normalizedData = self::normalize(
            $data
        );

        return json_encode(
            $normalizedData,
            JSON_THROW_ON_ERROR
            | JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_PRESERVE_ZERO_FRACTION
        );
    }

    private static function normalize(
        mixed $value
    ): mixed {
        if (!is_array($value)) {
            return $value;
        }

        if (array_is_list($value)) {
            return array_map(
                [self::class, 'normalize'],
                $value
            );
        }

        ksort($value);

        foreach ($value as $key => $item) {
            $value[$key] = self::normalize(
                $item
            );
        }

        return $value;
    }
}