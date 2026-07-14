<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\ValidationException;
use App\Interfaces\CategoriaRepositoryInterface;
use RuntimeException;
use Throwable;

final class CategoriaService
{
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
        return $this->repository->findById(
            $categoryId
        );
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

        $imageFile = $files['imagen'] ?? null;

        if (
            !is_array($imageFile)
            || (int) ($imageFile['error'] ?? UPLOAD_ERR_NO_FILE)
                === UPLOAD_ERR_NO_FILE
        ) {
            $errors['imagen'] =
                'Selecciona una imagen para la categoría.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $imagePath = null;

        try {
            $imagePath = $this->imageService->store(
                $imageFile,
                'categorias'
            );

            return $this->repository->create([
                'nombreCategoria' =>
                    $data['nombreCategoria'],

                'descripcion' =>
                    $data['descripcion'],

                'imagen' =>
                    $imagePath,
            ]);
        } catch (ValidationException $exception) {
            throw $exception;
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
        int $categoryId,
        array $input,
        array $files
    ): void {
        $category = $this->repository->findById(
            $categoryId
        );

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

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $newImagePath = null;
        $imagePath = $category['imagen'];

        $imageFile = $files['imagen'] ?? null;

        try {
            if (
                is_array($imageFile)
                && (int) (
                    $imageFile['error']
                    ?? UPLOAD_ERR_NO_FILE
                ) !== UPLOAD_ERR_NO_FILE
            ) {
                $newImagePath =
                    $this->imageService->store(
                        $imageFile,
                        'categorias'
                    );

                $imagePath = $newImagePath;
            }

            $this->repository->update(
                $categoryId,
                [
                    'nombreCategoria' =>
                        $data['nombreCategoria'],

                    'descripcion' =>
                        $data['descripcion'],

                    'imagen' =>
                        $imagePath,
                ]
            );

            if (
                $newImagePath !== null
                && !empty($category['imagen'])
            ) {
                $this->imageService->delete(
                    (string) $category['imagen']
                );
            }
        } catch (Throwable $exception) {
            if ($newImagePath !== null) {
                $this->imageService->delete(
                    $newImagePath
                );
            }

            throw $exception;
        }
    }

    public function changeActiveState(
        int $categoryId,
        bool $active
    ): void {
        $category = $this->repository->findById(
            $categoryId
        );

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
                (string) (
                    $input['nombreCategoria']
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

        if (
            mb_strlen(
                $data['nombreCategoria']
            ) < 3
            || mb_strlen(
                $data['nombreCategoria']
            ) > 80
        ) {
            $errors['nombreCategoria'] =
                'El nombre debe contener entre 3 y 80 caracteres.';
        }

        if (
            $data['descripcion'] !== ''
            && mb_strlen(
                $data['descripcion']
            ) > 255
        ) {
            $errors['descripcion'] =
                'La descripción no puede superar 255 caracteres.';
        }

        return $errors;
    }
}