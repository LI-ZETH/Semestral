<?php

declare(strict_types=1);

return [
    'private_key_path' => BASE_PATH
        . DIRECTORY_SEPARATOR
        . 'storage'
        . DIRECTORY_SEPARATOR
        . 'keys'
        . DIRECTORY_SEPARATOR
        . 'private.pem',

    'public_key_path' => BASE_PATH
        . DIRECTORY_SEPARATOR
        . 'storage'
        . DIRECTORY_SEPARATOR
        . 'keys'
        . DIRECTORY_SEPARATOR
        . 'public.pem',

    'private_key_passphrase' => 'CAMBIAR_ESTA_CONTRASENA',

    'openssl_config_path' => 'C:\\xampp\\apache\\conf\\openssl.cnf',

    'key_bits' => 2048,

    'digest_algorithm' => 'sha256',
];