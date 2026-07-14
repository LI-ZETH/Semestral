<?php

declare(strict_types=1);

namespace App\Core;

final class ClientInfo
{
    private function __construct()
    {
    }

    public static function ipAddress(): string
    {
        $address = trim(
            (string) (
                $_SERVER['REMOTE_ADDR']
                ?? '0.0.0.0'
            )
        );

        if (
            filter_var(
                $address,
                FILTER_VALIDATE_IP
            ) === false
        ) {
            return '0.0.0.0';
        }

        return substr($address, 0, 45);
    }

    public static function userAgent(): ?string
    {
        $userAgent = trim(
            (string) (
                $_SERVER['HTTP_USER_AGENT']
                ?? ''
            )
        );

        if ($userAgent === '') {
            return null;
        }

        return mb_substr(
            $userAgent,
            0,
            500
        );
    }
}