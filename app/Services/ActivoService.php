<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\ValidationException;
use App\Interfaces\ActivoRepositoryInterface;
use DateTimeImmutable;
use RuntimeException;
use Throwable;

final class ActivoService
{
    private const MIN_IMAGES = 2;
    private const MAX_IMAGES = 8;

    public function __construct(
        private readonly ActivoRepositoryInterface $repository,
        private readonly ImageUploadService $imageService
    ) {
    }

    public function listByProduct(
        int $productId,
        array $filters = []
    ): ?array {
        return $this->repository->listByProduct(
            $productId,
            $filters
        );
    }

    public function listProducts(): array
    {
        return $this->repository->listActiveProducts();
    }

    public function listStates(
        ?int $currentStateId = null
    ): array {
        return $this->repository->listAvailableStates(
            $currentStateId
        );
    }

    public function listLocations(): array
    {
        return $this->repository->listActiveLocations();
    }

    public function findById(int $assetId): ?array
    {
        $asset = $this->repository->findById($assetId);

        if ($asset === null) {
            return null;
        }

        $asset['images'] = $this->repository
            ->findImages($assetId);

        return $asset;
    }

    public function create(
        array $input,
        array $files,
        int $userId
    ): int {
        $data = $this->normalize($input);
        $errors = $this->validateData($data);

        $product = $this->repository->findProductById(
            $data['idProducto']
        );

        if (
            $product === null
            || !(bool) $product['activo']
            || !(bool) $product['subcategoriaActiva']
            || !(bool) $product['categoriaActiva']
        ) {
            $errors['idProducto'] =
                'Selecciona un producto activo.';
        }

        $state = $this->validateState(
            $data['idEstadoActivo'],
            null,
            $errors
        );

        $this->validateLocation(
            $data['idUbicacion'],
            $errors
        );

        $this->validateConflicts(
            $data['codigoActivo'],
            $data['numeroSerie'],
            null,
            $errors
        );

        $uploadedFiles = $this->normalizeUploadedFiles(
            $files['imagenes'] ?? null
        );

        if (count($uploadedFiles) < self::MIN_IMAGES) {
            $errors['imagenes'] =
                'Debes seleccionar al menos dos imágenes del activo.';
        }

        if (count($uploadedFiles) > self::MAX_IMAGES) {
            $errors['imagenes'] =
                'Puedes registrar un máximo de ocho imágenes.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $storedImages = [];

        try {
            $storedImages = $this->storeImages($uploadedFiles);

            $this->repository->beginTransaction();

            $assetId = $this->repository->create([
                ...$data,
                'qrToken' => bin2hex(random_bytes(32)),
            ]);

            $order = 1;

            foreach ($storedImages as $index => $image) {
                $this->repository->insertImage(
                    $assetId,
                    [
                        ...$image,
                        'esPrincipal' => $index === 0,
                        'ordenVisual' => $order,
                    ]
                );

                $order++;
            }

            $this->repository->insertMovement([
                'idActivo' => $assetId,
                'idUsuario' => $userId,
                'tipoMovimiento' => 'REGISTRO',
                'idEstadoAnterior' => null,
                'idEstadoNuevo' => $state['idEstadoActivo'],
                'idUbicacionAnterior' => null,
                'idUbicacionNueva' => $data['idUbicacion'],
                'descripcion' =>
                    'Registro inicial del activo '
                    . $data['codigoActivo'] . '.',
            ]);

            $this->repository->commit();

            return $assetId;
        } catch (Throwable $exception) {
            $this->repository->rollBack();
            $this->deleteStoredImages($storedImages);

            throw $exception;
        }
    }

    public function update(
        int $assetId,
        array $input,
        array $files,
        int $userId
    ): void {
        $asset = $this->repository->findById($assetId);

        if ($asset === null) {
            throw new ValidationException([
                'general' => 'El activo solicitado no existe.',
            ]);
        }

        $currentImages = $this->repository
            ->findImages($assetId);

        $data = $this->normalize($input);
        $errors = $this->validateData($data);

        $product = $this->repository->findProductById(
            $data['idProducto']
        );

        if (
            $product === null
            || !(bool) $product['activo']
            || !(bool) $product['subcategoriaActiva']
            || !(bool) $product['categoriaActiva']
        ) {
            $errors['idProducto'] =
                'Selecciona un producto activo.';
        }

        $state = $this->validateState(
            $data['idEstadoActivo'],
            $asset,
            $errors
        );

        $this->validateLocation(
            $data['idUbicacion'],
            $errors
        );

        $this->validateConflicts(
            $data['codigoActivo'],
            $data['numeroSerie'],
            $assetId,
            $errors
        );

        $removeImageIds = $this->normalizeImageIds(
            $input['eliminarImagenes'] ?? []
        );

        $currentImageMap = [];

        foreach ($currentImages as $image) {
            $currentImageMap[(int) $image['idImagenActivo']] = $image;
        }

        foreach ($removeImageIds as $imageId) {
            if (!isset($currentImageMap[$imageId])) {
                $errors['imagenes'] =
                    'Una de las imágenes seleccionadas no pertenece al activo.';
                break;
            }
        }

        $uploadedFiles = $this->normalizeUploadedFiles(
            $files['imagenes'] ?? null
        );

        $remainingCount = count($currentImages)
            - count($removeImageIds)
            + count($uploadedFiles);

        if ($remainingCount < self::MIN_IMAGES) {
            $errors['imagenes'] =
                'El activo debe conservar al menos dos imágenes.';
        }

        if ($remainingCount > self::MAX_IMAGES) {
            $errors['imagenes'] =
                'El activo no puede tener más de ocho imágenes activas.';
        }

        $selectedPrincipalId = (int) (
            $input['imagenPrincipalId'] ?? 0
        );

        if (
            $selectedPrincipalId > 0
            && !isset($currentImageMap[$selectedPrincipalId])
        ) {
            $errors['imagenPrincipalId'] =
                'La imagen principal seleccionada no es válida.';
        }

        if (
            $selectedPrincipalId > 0
            && in_array(
                $selectedPrincipalId,
                $removeImageIds,
                true
            )
        ) {
            $errors['imagenPrincipalId'] =
                'No puedes eliminar la imagen elegida como principal.';
        }

        if (
            $this->repository->hasActiveAssignment($assetId)
            && (int) $asset['idEstadoActivo']
                !== $data['idEstadoActivo']
        ) {
            $errors['idEstadoActivo'] =
                'El estado de un activo asignado se cambia desde el proceso de devolución.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $storedImages = [];
        $removedImagePaths = [];

        foreach ($removeImageIds as $imageId) {
            $removedImagePaths[] = (string) (
                $currentImageMap[$imageId]['rutaImagen']
                ?? ''
            );
        }

        try {
            $storedImages = $this->storeImages($uploadedFiles);

            $this->repository->beginTransaction();

            $this->repository->update($assetId, $data);

            if ($removeImageIds !== []) {
                $this->repository->deactivateImages(
                    $assetId,
                    $removeImageIds
                );
            }

            $nextOrder = $this->repository
                ->getNextImageOrder($assetId);
            $newImageIds = [];

            foreach ($storedImages as $image) {
                $newImageIds[] = $this->repository->insertImage(
                    $assetId,
                    [
                        ...$image,
                        'esPrincipal' => false,
                        'ordenVisual' => $nextOrder,
                    ]
                );

                $nextOrder++;
            }

            $principalId = $this->resolvePrincipalImageId(
                $currentImages,
                $removeImageIds,
                $selectedPrincipalId,
                $newImageIds
            );

            $this->repository->clearPrincipalImage($assetId);
            $this->repository->setPrincipalImage(
                $assetId,
                $principalId
            );

            $stateChanged = (int) $asset['idEstadoActivo']
                !== $data['idEstadoActivo'];
            $locationChanged = $this->nullableInt(
                $asset['idUbicacion']
            ) !== $data['idUbicacion'];

            $movementType = $stateChanged
                ? 'CAMBIO_ESTADO'
                : ($locationChanged
                    ? 'CAMBIO_UBICACION'
                    : 'ACTUALIZACION');

            $this->repository->insertMovement([
                'idActivo' => $assetId,
                'idUsuario' => $userId,
                'tipoMovimiento' => $movementType,
                'idEstadoAnterior' => (int) $asset['idEstadoActivo'],
                'idEstadoNuevo' => $state['idEstadoActivo'],
                'idUbicacionAnterior' => $this->nullableInt(
                    $asset['idUbicacion']
                ),
                'idUbicacionNueva' => $data['idUbicacion'],
                'descripcion' =>
                    'Actualización administrativa del activo '
                    . $data['codigoActivo'] . '.',
            ]);

            $this->repository->commit();

            foreach ($removedImagePaths as $path) {
                $this->imageService->delete($path);
            }
        } catch (Throwable $exception) {
            $this->repository->rollBack();
            $this->deleteStoredImages($storedImages);

            throw $exception;
        }
    }

    public function changeActiveState(
        int $assetId,
        bool $active,
        int $userId
    ): void {
        $asset = $this->repository->findById($assetId);

        if ($asset === null) {
            throw new ValidationException([
                'general' => 'El activo solicitado no existe.',
            ]);
        }

        if (
            !$active
            && $this->repository->hasActiveAssignment($assetId)
        ) {
            throw new ValidationException([
                'general' =>
                    'No puedes desactivar un activo que está asignado.',
            ]);
        }

        if ($active) {
            $product = $this->repository->findProductById(
                (int) $asset['idProducto']
            );

            if (
                $product === null
                || !(bool) $product['activo']
                || !(bool) $product['subcategoriaActiva']
                || !(bool) $product['categoriaActiva']
            ) {
                throw new ValidationException([
                    'general' =>
                        'No puedes activar el activo porque su producto, subcategoría o categoría está inactivo.',
                ]);
            }

            if (
                $this->repository->countActiveImages($assetId)
                < self::MIN_IMAGES
            ) {
                throw new ValidationException([
                    'general' =>
                        'El activo necesita al menos dos imágenes antes de activarse.',
                ]);
            }
        }

        $this->repository->beginTransaction();

        try {
            $this->repository->setActiveState(
                $assetId,
                $active
            );

            $this->repository->insertMovement([
                'idActivo' => $assetId,
                'idUsuario' => $userId,
                'tipoMovimiento' => 'ACTUALIZACION',
                'idEstadoAnterior' => (int) $asset['idEstadoActivo'],
                'idEstadoNuevo' => (int) $asset['idEstadoActivo'],
                'idUbicacionAnterior' => $this->nullableInt(
                    $asset['idUbicacion']
                ),
                'idUbicacionNueva' => $this->nullableInt(
                    $asset['idUbicacion']
                ),
                'descripcion' => $active
                    ? 'Reactivación del activo '
                        . $asset['codigoActivo'] . '.'
                    : 'Desactivación del activo '
                        . $asset['codigoActivo'] . '.',
            ]);

            $this->repository->commit();
        } catch (Throwable $exception) {
            $this->repository->rollBack();
            throw $exception;
        }
    }

    private function normalize(array $input): array
    {
        $serial = trim((string) ($input['numeroSerie'] ?? ''));
        $ip = trim((string) ($input['direccionIP'] ?? ''));
        $lifeMonths = trim(
            (string) ($input['vidaUtilMeses'] ?? '')
        );
        $locationId = (int) ($input['idUbicacion'] ?? 0);

        return [
            'idProducto' => (int) ($input['idProducto'] ?? 0),
            'codigoActivo' => strtoupper(
                trim((string) ($input['codigoActivo'] ?? ''))
            ),
            'numeroSerie' => $serial !== '' ? $serial : null,
            'direccionIP' => $ip !== '' ? $ip : null,
            'costo' => $this->normalizeMoney(
                $input['costo'] ?? '0'
            ),
            'fechaAdquisicion' => trim(
                (string) ($input['fechaAdquisicion'] ?? '')
            ),
            'fechaIngreso' => trim(
                (string) ($input['fechaIngreso'] ?? '')
            ),
            'vidaUtilMeses' => $lifeMonths !== ''
                ? (int) $lifeMonths
                : null,
            'valorResidual' => $this->normalizeMoney(
                $input['valorResidual'] ?? '0'
            ),
            'idEstadoActivo' => (int) (
                $input['idEstadoActivo'] ?? 0
            ),
            'idUbicacion' => $locationId > 0
                ? $locationId
                : null,
            'observaciones' => $this->nullableText(
                $input['observaciones'] ?? null
            ),
        ];
    }

    private function validateData(array $data): array
    {
        $errors = [];

        if ($data['idProducto'] <= 0) {
            $errors['idProducto'] =
                'Selecciona el producto al que pertenece la copia.';
        }

        $codeLength = mb_strlen($data['codigoActivo']);

        if ($codeLength < 3 || $codeLength > 40) {
            $errors['codigoActivo'] =
                'El código debe contener entre 3 y 40 caracteres.';
        } elseif (
            preg_match(
                '/^[A-Z0-9._-]+$/',
                $data['codigoActivo']
            ) !== 1
        ) {
            $errors['codigoActivo'] =
                'Usa solamente letras, números, puntos, guiones o guion bajo.';
        }

        if (
            $data['numeroSerie'] !== null
            && mb_strlen($data['numeroSerie']) > 120
        ) {
            $errors['numeroSerie'] =
                'El número de serie no puede superar 120 caracteres.';
        }

        if (
            $data['direccionIP'] !== null
            && filter_var(
                $data['direccionIP'],
                FILTER_VALIDATE_IP
            ) === false
        ) {
            $errors['direccionIP'] =
                'Introduce una dirección IPv4 o IPv6 válida.';
        }

        if ($data['costo'] < 0) {
            $errors['costo'] =
                'El costo no puede ser negativo.';
        }

        if ($data['valorResidual'] < 0) {
            $errors['valorResidual'] =
                'El valor residual no puede ser negativo.';
        } elseif ($data['valorResidual'] > $data['costo']) {
            $errors['valorResidual'] =
                'El valor residual no puede superar el costo.';
        }

        if (!$this->isValidDate($data['fechaAdquisicion'])) {
            $errors['fechaAdquisicion'] =
                'Selecciona una fecha de adquisición válida.';
        }

        if (!$this->isValidDate($data['fechaIngreso'])) {
            $errors['fechaIngreso'] =
                'Selecciona una fecha de ingreso válida.';
        }

        if (
            $this->isValidDate($data['fechaAdquisicion'])
            && $this->isValidDate($data['fechaIngreso'])
            && $data['fechaIngreso'] < $data['fechaAdquisicion']
        ) {
            $errors['fechaIngreso'] =
                'La fecha de ingreso no puede ser anterior a la adquisición.';
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

        if ($data['idEstadoActivo'] <= 0) {
            $errors['idEstadoActivo'] =
                'Selecciona el estado inicial del activo.';
        }

        if (
            $data['observaciones'] !== null
            && mb_strlen($data['observaciones']) > 2000
        ) {
            $errors['observaciones'] =
                'Las observaciones no pueden superar 2000 caracteres.';
        }

        return $errors;
    }

    private function validateState(
        int $stateId,
        ?array $currentAsset,
        array &$errors
    ): array {
        $state = $this->repository->findStateById($stateId);

        if ($state === null || !(bool) $state['activo']) {
            $errors['idEstadoActivo'] =
                'Selecciona un estado activo.';

            return [
                'idEstadoActivo' => $stateId,
                'codigoEstado' => '',
            ];
        }

        if (
            $state['codigoEstado'] === 'ASIGNADO'
            && (
                $currentAsset === null
                || (int) $currentAsset['idEstadoActivo'] !== $stateId
            )
        ) {
            $errors['idEstadoActivo'] =
                'El estado Asignado solo se establece desde el módulo de asignaciones.';
        }

        return $state;
    }

    private function validateLocation(
        ?int $locationId,
        array &$errors
    ): void {
        if ($locationId === null) {
            return;
        }

        $location = $this->repository->findLocationById(
            $locationId
        );

        if ($location === null || !(bool) $location['activo']) {
            $errors['idUbicacion'] =
                'Selecciona una ubicación activa.';
        }
    }

    private function validateConflicts(
        string $assetCode,
        ?string $serialNumber,
        ?int $excludeId,
        array &$errors
    ): void {
        if ($assetCode === '') {
            return;
        }

        $conflicts = $this->repository->findConflicts(
            $assetCode,
            $serialNumber,
            $excludeId
        );

        foreach ($conflicts as $conflict) {
            if (
                mb_strtolower(trim((string) $conflict['codigoActivo']))
                === mb_strtolower(trim($assetCode))
            ) {
                $errors['codigoActivo'] =
                    'Ya existe un activo con ese código.';
            }

            if (
                $serialNumber !== null
                && $conflict['numeroSerie'] !== null
                && mb_strtolower(
                    trim((string) $conflict['numeroSerie'])
                ) === mb_strtolower(trim($serialNumber))
            ) {
                $errors['numeroSerie'] =
                    'Ya existe un activo con ese número de serie.';
            }
        }
    }

    private function normalizeUploadedFiles(mixed $files): array
    {
        if (
            !is_array($files)
            || !isset(
                $files['name'],
                $files['type'],
                $files['tmp_name'],
                $files['error'],
                $files['size']
            )
        ) {
            return [];
        }

        if (!is_array($files['name'])) {
            return ((int) $files['error'] === UPLOAD_ERR_NO_FILE)
                ? []
                : [$files];
        }

        $normalized = [];

        foreach ($files['name'] as $index => $name) {
            $error = (int) ($files['error'][$index]
                ?? UPLOAD_ERR_NO_FILE);

            if ($error === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $normalized[] = [
                'name' => (string) $name,
                'type' => (string) (
                    $files['type'][$index] ?? ''
                ),
                'tmp_name' => (string) (
                    $files['tmp_name'][$index] ?? ''
                ),
                'error' => $error,
                'size' => (int) (
                    $files['size'][$index] ?? 0
                ),
            ];
        }

        return $normalized;
    }

    private function storeImages(array $uploadedFiles): array
    {
        $stored = [];

        try {
            foreach ($uploadedFiles as $file) {
                $temporaryPath = (string) $file['tmp_name'];
                $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $fileInfo->file($temporaryPath);

                $path = $this->imageService->store(
                    $file,
                    'activos'
                );

                $stored[] = [
                    'rutaImagen' => $path,
                    'nombreOriginal' => mb_substr(
                        basename((string) $file['name']),
                        0,
                        255
                    ),
                    'mimeType' => is_string($mimeType)
                        ? mb_substr($mimeType, 0, 100)
                        : null,
                    'tamanoBytes' => (int) $file['size'],
                ];
            }

            return $stored;
        } catch (RuntimeException $exception) {
            $this->deleteStoredImages($stored);

            throw new ValidationException([
                'imagenes' => $exception->getMessage(),
            ]);
        }
    }

    private function deleteStoredImages(array $storedImages): void
    {
        foreach ($storedImages as $image) {
            $this->imageService->delete(
                $image['rutaImagen'] ?? null
            );
        }
    }

    private function normalizeImageIds(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $ids = [];

        foreach ($value as $imageId) {
            $normalizedId = (int) $imageId;

            if ($normalizedId > 0) {
                $ids[] = $normalizedId;
            }
        }

        return array_values(array_unique($ids));
    }

    private function resolvePrincipalImageId(
        array $currentImages,
        array $removeImageIds,
        int $selectedPrincipalId,
        array $newImageIds
    ): int {
        if ($selectedPrincipalId > 0) {
            return $selectedPrincipalId;
        }

        foreach ($currentImages as $image) {
            $imageId = (int) $image['idImagenActivo'];

            if (
                (bool) $image['esPrincipal']
                && !in_array($imageId, $removeImageIds, true)
            ) {
                return $imageId;
            }
        }

        foreach ($currentImages as $image) {
            $imageId = (int) $image['idImagenActivo'];

            if (!in_array($imageId, $removeImageIds, true)) {
                return $imageId;
            }
        }

        if ($newImageIds !== []) {
            return (int) $newImageIds[0];
        }

        throw new RuntimeException(
            'No fue posible determinar la imagen principal.'
        );
    }

    private function normalizeMoney(mixed $value): float
    {
        $normalized = str_replace(
            ',',
            '.',
            trim((string) $value)
        );

        return is_numeric($normalized)
            ? round((float) $normalized, 2)
            : -1.0;
    }

    private function nullableText(mixed $value): ?string
    {
        $text = trim((string) $value);

        return $text !== '' ? $text : null;
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $integer = (int) $value;

        return $integer > 0 ? $integer : null;
    }

    private function isValidDate(string $value): bool
    {
        if ($value === '') {
            return false;
        }

        $date = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $value
        );

        return $date !== false
            && $date->format('Y-m-d') === $value;
    }
}
