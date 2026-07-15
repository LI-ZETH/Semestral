<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\ValidationException;
use App\Interfaces\CategoriaRepositoryInterface;
use RuntimeException;
use Throwable;

final class CategoriaService
{
    private const IMAGE_FITS = [
        'cover',
        'contain',
    ];

    private const IMAGE_SIZES = [
        'compacta',
        'mediana',
        'amplia',
    ];

    public function __construct(
        private readonly CategoriaRepositoryInterface $repository,
        private readonly ImageUploadService $imageService
    ) {
    }

    public function listAll(): array
    {
        return $this->repository->listAll();
    }

    public function findById(
        int $categoryId
    ): ?array {
        return $this->repository->findById($categoryId);
    }

    public function create(
        array $input,
        array $files
    ): int {
        $data = $this->normalize($input);
        $errors = $this->validate($data);

        if (
            $this->repository->nameExists(
                $data['nombreCategoria']
            )
        ) {
            $errors['nombreCategoria'] =
                'Ya existe una categoría con ese nombre.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $imagePath = null;
        $imageFile = $files['imagen'] ?? null;

        try {
            if ($this->hasUploadedImage($imageFile)) {
                $imagePath = $this->storeImage($imageFile);
            }

            return $this->repository->create([
                'nombreCategoria' => $data['nombreCategoria'],
                'descripcion' => $data['descripcion'],
                'imagen' => $imagePath,
                'imagenAjuste' => $data['imagenAjuste'],
                'imagenTamano' => $data['imagenTamano'],
            ]);
        } catch (Throwable $exception) {
            if ($imagePath !== null) {
                $this->imageService->delete($imagePath);
            }

            throw $exception;
        }
    }

    public function update(
        int $categoryId,
        array $input,
        array $files
    ): void {
        $category = $this->repository->findById($categoryId);

        if ($category === null) {
            throw new ValidationException([
                'general' =>
                    'La categoría solicitada no existe.',
            ]);
        }

        $data = $this->normalize($input);
        $errors = $this->validate($data);

        if (
            $this->repository->nameExists(
                $data['nombreCategoria'],
                $categoryId
            )
        ) {
            $errors['nombreCategoria'] =
                'Ya existe una categoría con ese nombre.';
        }

        $imageFile = $files['imagen'] ?? null;
        $hasNewImage = $this->hasUploadedImage($imageFile);

        if ($data['eliminarImagen'] && $hasNewImage) {
            $errors['imagen'] =
                'Elige entre reemplazar la imagen o eliminarla, no ambas opciones.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $oldImagePath = !empty($category['imagen'])
            ? (string) $category['imagen']
            : null;

        $newImagePath = null;
        $finalImagePath = $oldImagePath;

        try {
            if ($hasNewImage) {
                $newImagePath = $this->storeImage($imageFile);
                $finalImagePath = $newImagePath;
            } elseif ($data['eliminarImagen']) {
                $finalImagePath = null;
            }

            $this->repository->update(
                $categoryId,
                [
                    'nombreCategoria' => $data['nombreCategoria'],
                    'descripcion' => $data['descripcion'],
                    'imagen' => $finalImagePath,
                    'imagenAjuste' => $data['imagenAjuste'],
                    'imagenTamano' => $data['imagenTamano'],
                ]
            );

            $imageWasReplaced = $newImagePath !== null;
            $imageWasRemoved = $data['eliminarImagen'];

            if (
                $oldImagePath !== null
                && ($imageWasReplaced || $imageWasRemoved)
            ) {
                $this->imageService->delete($oldImagePath);
            }
        } catch (Throwable $exception) {
            if ($newImagePath !== null) {
                $this->imageService->delete($newImagePath);
            }

            throw $exception;
        }
    }

    public function changeActiveState(
        int $categoryId,
        bool $active
    ): void {
        $category = $this->repository->findById($categoryId);

        if ($category === null) {
            throw new ValidationException([
                'general' =>
                    'La categoría solicitada no existe.',
            ]);
        }

        $this->repository->setActiveState(
            $categoryId,
            $active
        );
    }

    private function normalize(array $input): array
    {
        return [
            'nombreCategoria' => trim(
                (string) ($input['nombreCategoria'] ?? '')
            ),
            'descripcion' => trim(
                (string) ($input['descripcion'] ?? '')
            ),
            'imagenAjuste' => trim(
                (string) ($input['imagenAjuste'] ?? 'cover')
            ),
            'imagenTamano' => trim(
                (string) ($input['imagenTamano'] ?? 'mediana')
            ),
            'eliminarImagen' => isset($input['eliminarImagen'])
                && (string) $input['eliminarImagen'] === '1',
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (
            mb_strlen($data['nombreCategoria']) < 3
            || mb_strlen($data['nombreCategoria']) > 80
        ) {
            $errors['nombreCategoria'] =
                'El nombre debe contener entre 3 y 80 caracteres.';
        }

        if (
            $data['descripcion'] !== ''
            && mb_strlen($data['descripcion']) > 255
        ) {
            $errors['descripcion'] =
                'La descripción no puede superar 255 caracteres.';
        }

        if (!in_array(
            $data['imagenAjuste'],
            self::IMAGE_FITS,
            true
        )) {
            $errors['imagenAjuste'] =
                'Selecciona un ajuste de imagen válido.';
        }

        if (!in_array(
            $data['imagenTamano'],
            self::IMAGE_SIZES,
            true
        )) {
            $errors['imagenTamano'] =
                'Selecciona un tamaño de imagen válido.';
        }

        return $errors;
    }

    private function hasUploadedImage(mixed $file): bool
    {
        return is_array($file)
            && (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE)
                !== UPLOAD_ERR_NO_FILE;
    }

    private function storeImage(array $file): string
    {
        try {
            return $this->imageService->store(
                $file,
                'categorias'
            );
        } catch (RuntimeException $exception) {
            throw new ValidationException([
                'imagen' => $exception->getMessage(),
            ]);
        }
    }
}
