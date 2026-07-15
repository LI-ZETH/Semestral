<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\ValidationException;
use App\Interfaces\UbicacionRepositoryInterface;

final class UbicacionService
{
    private const TYPES = [
        'EDIFICIO',
        'OFICINA',
        'CASA',
        'BODEGA',
        'OTRA',
    ];

    public function __construct(
        private readonly UbicacionRepositoryInterface $repository
    ) {
    }

    public function listAll(array $filters = []): array
    {
        return $this->repository->listAll($filters);
    }

    public function findById(int $locationId): ?array
    {
        return $this->repository->findById($locationId);
    }

    public function create(array $input): int
    {
        $data = $this->normalize($input);
        $errors = $this->validate($data);

        if (
            $data['nombreUbicacion'] !== ''
            && $this->repository->nameExists(
                $data['nombreUbicacion']
            )
        ) {
            $errors['nombreUbicacion'] =
                'Ya existe una ubicación con ese nombre.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        return $this->repository->create($data);
    }

    public function update(
        int $locationId,
        array $input
    ): void {
        $location = $this->repository->findById($locationId);

        if ($location === null) {
            throw new ValidationException([
                'general' => 'La ubicación solicitada no existe.',
            ]);
        }

        $data = $this->normalize($input);
        $errors = $this->validate($data);

        if (
            $data['nombreUbicacion'] !== ''
            && $this->repository->nameExists(
                $data['nombreUbicacion'],
                $locationId
            )
        ) {
            $errors['nombreUbicacion'] =
                'Ya existe una ubicación con ese nombre.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $this->repository->update($locationId, $data);
    }

    public function changeActiveState(
        int $locationId,
        bool $active
    ): void {
        $location = $this->repository->findById($locationId);

        if ($location === null) {
            throw new ValidationException([
                'general' => 'La ubicación solicitada no existe.',
            ]);
        }

        if (!$active) {
            $usage = $this->repository->countActiveUsage(
                $locationId
            );

            $assetCount = (int) ($usage['totalActivos'] ?? 0);
            $collaboratorCount = (int) (
                $usage['totalColaboradores'] ?? 0
            );

            if ($assetCount > 0 || $collaboratorCount > 0) {
                throw new ValidationException([
                    'general' =>
                        'No puedes desactivar esta ubicación porque '
                        . 'todavía tiene activos o colaboradores asociados.',
                ]);
            }
        }

        $this->repository->setActiveState(
            $locationId,
            $active
        );
    }

    public function types(): array
    {
        return self::TYPES;
    }

    private function normalize(array $input): array
    {
        return [
            'nombreUbicacion' => trim(
                (string) ($input['nombreUbicacion'] ?? '')
            ),
            'tipoUbicacion' => strtoupper(trim(
                (string) ($input['tipoUbicacion'] ?? '')
            )),
            'edificio' => $this->nullableString(
                $input['edificio'] ?? null
            ),
            'piso' => $this->nullableString(
                $input['piso'] ?? null
            ),
            'oficina' => $this->nullableString(
                $input['oficina'] ?? null
            ),
            'direccion' => $this->nullableString(
                $input['direccion'] ?? null
            ),
            'descripcion' => $this->nullableString(
                $input['descripcion'] ?? null
            ),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        $nameLength = mb_strlen($data['nombreUbicacion']);

        if ($nameLength < 3 || $nameLength > 100) {
            $errors['nombreUbicacion'] =
                'El nombre debe contener entre 3 y 100 caracteres.';
        }

        if (!in_array($data['tipoUbicacion'], self::TYPES, true)) {
            $errors['tipoUbicacion'] =
                'Selecciona un tipo de ubicación válido.';
        }

        $this->validateMaximum(
            $data['edificio'],
            80,
            'edificio',
            'El edificio no puede superar 80 caracteres.',
            $errors
        );

        $this->validateMaximum(
            $data['piso'],
            30,
            'piso',
            'El piso no puede superar 30 caracteres.',
            $errors
        );

        $this->validateMaximum(
            $data['oficina'],
            50,
            'oficina',
            'La oficina no puede superar 50 caracteres.',
            $errors
        );

        $this->validateMaximum(
            $data['direccion'],
            255,
            'direccion',
            'La dirección no puede superar 255 caracteres.',
            $errors
        );

        $this->validateMaximum(
            $data['descripcion'],
            255,
            'descripcion',
            'La descripción no puede superar 255 caracteres.',
            $errors
        );

        return $errors;
    }

    private function validateMaximum(
        ?string $value,
        int $maximum,
        string $field,
        string $message,
        array &$errors
    ): void {
        if ($value !== null && mb_strlen($value) > $maximum) {
            $errors[$field] = $message;
        }
    }

    private function nullableString(mixed $value): ?string
    {
        $normalized = trim((string) ($value ?? ''));

        return $normalized === '' ? null : $normalized;
    }
}
