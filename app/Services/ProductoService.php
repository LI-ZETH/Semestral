<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\ValidationException;
use App\Interfaces\ProductoRepositoryInterface;
use RuntimeException;
use Throwable;

final class ProductoService
{
    private const TYPES = [
        'HARDWARE',
        'SOFTWARE',
        'LICENCIA',
    ];

    public function __construct(
        private readonly ProductoRepositoryInterface $repository,
        private readonly ImageUploadService $imageService
    ) {
    }

    public function listBySubcategory(int $subcategoryId): ?array
    {
        return $this->repository->listBySubcategory($subcategoryId);
    }

    public function listSubcategories(): array
    {
        return $this->repository->listActiveSubcategories();
    }

    public function findById(int $productId): ?array
    {
        return $this->repository->findById($productId);
    }

    public function create(array $input, array $files): int
    {
        $data = $this->normalize($input);
        $errors = $this->validate($data);

        $subcategory = $this->repository->findSubcategoryById(
            $data['idSubcategoria']
        );

        if (
            $subcategory === null
            || !(bool) $subcategory['activo']
            || !(bool) $subcategory['categoriaActiva']
        ) {
            $errors['idSubcategoria'] =
                'Selecciona una subcategoría activa.';
        }

        if (
            $data['idSubcategoria'] > 0
            && $this->repository->productExists(
                $data['idSubcategoria'],
                $data['nombreProducto'],
                $data['marca'] ?? '',
                $data['modelo'] ?? ''
            )
        ) {
            $errors['nombreProducto'] =
                'Ya existe este producto con la misma marca y modelo.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $imagePath = $this->storeOptionalImage(
            $files['imagen'] ?? null
        );

        try {
            return $this->repository->create([
                ...$data,
                'imagen' => $imagePath,
            ]);
        } catch (Throwable $exception) {
            if ($imagePath !== null) {
                $this->imageService->delete($imagePath);
            }

            throw $exception;
        }
    }

    public function update(
        int $productId,
        array $input,
        array $files
    ): void {
        $product = $this->repository->findById($productId);

        if ($product === null) {
            throw new ValidationException([
                'general' => 'El producto solicitado no existe.',
            ]);
        }

        $data = $this->normalize($input);
        $errors = $this->validate($data);

        $subcategory = $this->repository->findSubcategoryById(
            $data['idSubcategoria']
        );

        if (
            $subcategory === null
            || !(bool) $subcategory['activo']
            || !(bool) $subcategory['categoriaActiva']
        ) {
            $errors['idSubcategoria'] =
                'Selecciona una subcategoría activa.';
        }

        if (
            $data['idSubcategoria'] > 0
            && $this->repository->productExists(
                $data['idSubcategoria'],
                $data['nombreProducto'],
                $data['marca'] ?? '',
                $data['modelo'] ?? '',
                $productId
            )
        ) {
            $errors['nombreProducto'] =
                'Ya existe este producto con la misma marca y modelo.';
        }

        $removeImage = (
            (string) ($input['eliminarImagen'] ?? '0')
        ) === '1';

        $imageFile = $files['imagen'] ?? null;
        $hasNewImage = $this->hasUploadedFile($imageFile);

        if ($removeImage && $hasNewImage) {
            $errors['imagen'] =
                'Elige entre reemplazar o eliminar la imagen.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $oldImage = !empty($product['imagen'])
            ? (string) $product['imagen']
            : null;

        $newImage = null;
        $imagePath = $oldImage;

        try {
            if ($removeImage) {
                $imagePath = null;
            } elseif ($hasNewImage) {
                $newImage = $this->storeOptionalImage($imageFile);
                $imagePath = $newImage;
            }

            $this->repository->update(
                $productId,
                [
                    ...$data,
                    'imagen' => $imagePath,
                ]
            );

            if (
                ($removeImage || $newImage !== null)
                && $oldImage !== null
            ) {
                $this->imageService->delete($oldImage);
            }
        } catch (Throwable $exception) {
            if ($newImage !== null) {
                $this->imageService->delete($newImage);
            }

            throw $exception;
        }
    }

    public function changeActiveState(int $productId, bool $active): void
    {
        $product = $this->repository->findById($productId);

        if ($product === null) {
            throw new ValidationException([
                'general' => 'El producto solicitado no existe.',
            ]);
        }

        if (
            $active
            && (
                !(bool) $product['subcategoriaActiva']
                || !(bool) $product['categoriaActiva']
            )
        ) {
            throw new ValidationException([
                'general' =>
                    'No puedes activar un producto cuya categoría '
                    . 'o subcategoría está inactiva.',
            ]);
        }

        $this->repository->setActiveState($productId, $active);
    }

    private function normalize(array $input): array
    {
        $brand = trim((string) ($input['marca'] ?? ''));
        $model = trim((string) ($input['modelo'] ?? ''));
        $description = trim((string) ($input['descripcion'] ?? ''));
        $life = trim((string) ($input['vidaUtilMeses'] ?? ''));

        return [
            'idSubcategoria' => (int) ($input['idSubcategoria'] ?? 0),
            'nombreProducto' => trim(
                (string) ($input['nombreProducto'] ?? '')
            ),
            'marca' => $brand !== '' ? $brand : null,
            'modelo' => $model !== '' ? $model : null,
            'descripcion' => $description !== '' ? $description : null,
            'tipoProducto' => strtoupper(
                trim((string) ($input['tipoProducto'] ?? ''))
            ),
            'vidaUtilMeses' => $life !== '' ? (int) $life : null,
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['idSubcategoria'] <= 0) {
            $errors['idSubcategoria'] =
                'Selecciona una subcategoría.';
        }

        $nameLength = mb_strlen($data['nombreProducto']);

        if ($nameLength < 2 || $nameLength > 120) {
            $errors['nombreProducto'] =
                'El nombre debe contener entre 2 y 120 caracteres.';
        }

        if (
            $data['marca'] !== null
            && mb_strlen($data['marca']) > 80
        ) {
            $errors['marca'] =
                'La marca no puede superar 80 caracteres.';
        }

        if (
            $data['modelo'] !== null
            && mb_strlen($data['modelo']) > 100
        ) {
            $errors['modelo'] =
                'El modelo no puede superar 100 caracteres.';
        }

        if (!in_array($data['tipoProducto'], self::TYPES, true)) {
            $errors['tipoProducto'] =
                'Selecciona un tipo de producto válido.';
        }

        if (
            $data['vidaUtilMeses'] !== null
            && (
                $data['vidaUtilMeses'] < 1
                || $data['vidaUtilMeses'] > 600
            )
        ) {
            $errors['vidaUtilMeses'] =
                'La vida útil debe estar entre 1 y 600 meses.';
        }

        return $errors;
    }

    private function storeOptionalImage(mixed $file): ?string
    {
        if (!$this->hasUploadedFile($file)) {
            return null;
        }

        try {
            return $this->imageService->store(
                $file,
                'productos'
            );
        } catch (RuntimeException $exception) {
            throw new ValidationException([
                'imagen' => $exception->getMessage(),
            ]);
        }
    }

    private function hasUploadedFile(mixed $file): bool
    {
        return is_array($file)
            && (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE)
                !== UPLOAD_ERR_NO_FILE;
    }
}
