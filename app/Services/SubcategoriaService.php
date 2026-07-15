<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\ValidationException;
use App\Interfaces\SubcategoriaRepositoryInterface;
use RuntimeException;
use Throwable;

final class SubcategoriaService
{
    public function __construct(
        private readonly SubcategoriaRepositoryInterface $repository,
        private readonly ImageUploadService $imageService
    ) {
    }

    public function listByCategory(
        int $categoryId
    ): ?array {
        return $this->repository->listByCategory(
            $categoryId
        );
    }

    public function listCategories(): array
    {
        return $this->repository
            ->listActiveCategories();
    }

    public function findById(
        int $subcategoryId
    ): ?array {
        return $this->repository->findById(
            $subcategoryId
        );
    }

    public function create(
        array $input,
        array $files
    ): int {
        $data = $this->normalize($input);
        $errors = $this->validate($data);

        $category = $this->repository
            ->findCategoryById($data['idCategoria']);

        if (
            $category === null
            || !(bool) $category['activo']
        ) {
            $errors['idCategoria'] =
                'Selecciona una categoría activa.';
        }

        if (
            $data['idCategoria'] > 0
            && $this->repository->nameExists(
                $data['idCategoria'],
                $data['nombreSubcategoria']
            )
        ) {
            $errors['nombreSubcategoria'] =
                'Ya existe una subcategoría con ese nombre.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $imagePath = $this->storeOptionalImage(
            $files['imagen'] ?? null
        );

        try {
            return $this->repository->create([
                'idCategoria' => $data['idCategoria'],
                'nombreSubcategoria' =>
                    $data['nombreSubcategoria'],
                'descripcion' => $data['descripcion'],
                'imagen' => $imagePath,
            ]);
        } catch (Throwable $exception) {
            if ($imagePath !== null) {
                $this->imageService->delete(
                    $imagePath
                );
            }

            throw $exception;
        }
    }

    public function update(
        int $subcategoryId,
        array $input,
        array $files
    ): void {
        $subcategory = $this->repository
            ->findById($subcategoryId);

        if ($subcategory === null) {
            throw new ValidationException([
                'general' =>
                    'La subcategoría solicitada no existe.',
            ]);
        }

        $data = $this->normalize($input);
        $errors = $this->validate($data);

        $category = $this->repository
            ->findCategoryById($data['idCategoria']);

        if (
            $category === null
            || !(bool) $category['activo']
        ) {
            $errors['idCategoria'] =
                'Selecciona una categoría activa.';
        }

        if (
            $data['idCategoria'] > 0
            && $this->repository->nameExists(
                $data['idCategoria'],
                $data['nombreSubcategoria'],
                $subcategoryId
            )
        ) {
            $errors['nombreSubcategoria'] =
                'Ya existe una subcategoría con ese nombre.';
        }

        $removeImage = (
            (string) (
                $input['eliminarImagen']
                ?? '0'
            )
        ) === '1';

        $imageFile = $files['imagen'] ?? null;
        $hasNewImage = $this->hasUploadedFile(
            $imageFile
        );

        if ($removeImage && $hasNewImage) {
            $errors['imagen'] =
                'Elige entre reemplazar o eliminar la imagen.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $oldImage = !empty($subcategory['imagen'])
            ? (string) $subcategory['imagen']
            : null;

        $newImage = null;
        $imagePath = $oldImage;

        try {
            if ($removeImage) {
                $imagePath = null;
            } elseif ($hasNewImage) {
                $newImage = $this->storeOptionalImage(
                    $imageFile
                );

                $imagePath = $newImage;
            }

            $this->repository->update(
                $subcategoryId,
                [
                    'idCategoria' =>
                        $data['idCategoria'],

                    'nombreSubcategoria' =>
                        $data['nombreSubcategoria'],

                    'descripcion' =>
                        $data['descripcion'],

                    'imagen' =>
                        $imagePath,
                ]
            );

            if (
                ($removeImage || $newImage !== null)
                && $oldImage !== null
            ) {
                $this->imageService->delete(
                    $oldImage
                );
            }
        } catch (Throwable $exception) {
            if ($newImage !== null) {
                $this->imageService->delete(
                    $newImage
                );
            }

            throw $exception;
        }
    }

    public function changeActiveState(
        int $subcategoryId,
        bool $active
    ): void {
        $subcategory = $this->repository
            ->findById($subcategoryId);

        if ($subcategory === null) {
            throw new ValidationException([
                'general' =>
                    'La subcategoría solicitada no existe.',
            ]);
        }

        if ($active) {
            $category = $this->repository
                ->findCategoryById(
                    (int) $subcategory['idCategoria']
                );

            if (
                $category === null
                || !(bool) $category['activo']
            ) {
                throw new ValidationException([
                    'general' =>
                        'No puedes activar una subcategoría '
                        . 'cuya categoría está inactiva.',
                ]);
            }
        }

        $this->repository->setActiveState(
            $subcategoryId,
            $active
        );
    }

    private function normalize(array $input): array
    {
        return [
            'idCategoria' => (int) (
                $input['idCategoria'] ?? 0
            ),

            'nombreSubcategoria' => trim(
                (string) (
                    $input['nombreSubcategoria']
                    ?? ''
                )
            ),

            'descripcion' => trim(
                (string) (
                    $input['descripcion']
                    ?? ''
                )
            ),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['idCategoria'] <= 0) {
            $errors['idCategoria'] =
                'Selecciona una categoría.';
        }

        $nameLength = mb_strlen(
            $data['nombreSubcategoria']
        );

        if ($nameLength < 2 || $nameLength > 80) {
            $errors['nombreSubcategoria'] =
                'El nombre debe contener entre 2 y 80 caracteres.';
        }

        if (
            $data['descripcion'] !== ''
            && mb_strlen($data['descripcion']) > 255
        ) {
            $errors['descripcion'] =
                'La descripción no puede superar 255 caracteres.';
        }

        return $errors;
    }

    private function storeOptionalImage(
        mixed $file
    ): ?string {
        if (!$this->hasUploadedFile($file)) {
            return null;
        }

        try {
            return $this->imageService->store(
                $file,
                'subcategorias'
            );
        } catch (RuntimeException $exception) {
            throw new ValidationException([
                'imagen' => $exception->getMessage(),
            ]);
        }
    }

    private function hasUploadedFile(
        mixed $file
    ): bool {
        return is_array($file)
            && (int) (
                $file['error']
                ?? UPLOAD_ERR_NO_FILE
            ) !== UPLOAD_ERR_NO_FILE;
    }
}