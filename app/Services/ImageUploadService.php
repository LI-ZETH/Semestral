<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class ImageUploadService
{
    private const MAX_FILE_SIZE = 2 * 1024 * 1024;

    private const ALLOWED_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public function store(
        array $file,
        string $folder
    ): string {
        $this->validateUpload($file);

        $temporaryPath = (string) $file['tmp_name'];

        $fileInfo = new \finfo(FILEINFO_MIME_TYPE);

        $mimeType = $fileInfo->file($temporaryPath);

        if (
            !is_string($mimeType)
            || !array_key_exists(
                $mimeType,
                self::ALLOWED_MIME_TYPES
            )
        ) {
            throw new RuntimeException(
                'La imagen debe ser JPG, PNG o WEBP.'
            );
        }

        $extension = self::ALLOWED_MIME_TYPES[
            $mimeType
        ];

        $safeFolder = trim(
            str_replace(['\\', '..'], ['/', ''], $folder),
            '/'
        );

        if ($safeFolder === '') {
            throw new RuntimeException(
                'La carpeta de imágenes no es válida.'
            );
        }

        $directory = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'public'
            . DIRECTORY_SEPARATOR
            . 'uploads'
            . DIRECTORY_SEPARATOR
            . str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                $safeFolder
            );

        if (
            !is_dir($directory)
            && !mkdir($directory, 0775, true)
            && !is_dir($directory)
        ) {
            throw new RuntimeException(
                'No fue posible crear la carpeta de imágenes.'
            );
        }

        $filename = bin2hex(random_bytes(16))
            . '.'
            . $extension;

        $destination = $directory
            . DIRECTORY_SEPARATOR
            . $filename;

        if (
            !move_uploaded_file(
                $temporaryPath,
                $destination
            )
        ) {
            throw new RuntimeException(
                'No fue posible guardar la imagen.'
            );
        }

        return 'uploads/'
            . $safeFolder
            . '/'
            . $filename;
    }

    public function delete(?string $relativePath): void
    {
        if (
            $relativePath === null
            || trim($relativePath) === ''
        ) {
            return;
        }

        $normalizedPath = str_replace(
            ['\\', '..'],
            ['/', ''],
            $relativePath
        );

        if (
            !str_starts_with(
                $normalizedPath,
                'uploads/'
            )
        ) {
            return;
        }

        $absolutePath = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'public'
            . DIRECTORY_SEPARATOR
            . str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                $normalizedPath
            );

        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }

    private function validateUpload(array $file): void
    {
        if (
            !isset(
                $file['error'],
                $file['size'],
                $file['tmp_name']
            )
        ) {
            throw new RuntimeException(
                'No se recibió una imagen válida.'
            );
        }

        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException(
                'Ocurrió un error al subir la imagen.'
            );
        }

        if ((int) $file['size'] <= 0) {
            throw new RuntimeException(
                'La imagen está vacía.'
            );
        }

        if (
            (int) $file['size']
            > self::MAX_FILE_SIZE
        ) {
            throw new RuntimeException(
                'La imagen no puede superar 2 MB.'
            );
        }

        if (
            !is_uploaded_file(
                (string) $file['tmp_name']
            )
        ) {
            throw new RuntimeException(
                'El archivo recibido no es válido.'
            );
        }
    }
}