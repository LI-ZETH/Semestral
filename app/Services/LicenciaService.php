<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\ValidationException;
use App\Interfaces\CifradoReversibleInterface;
use App\Interfaces\LicenciaRepositoryInterface;
use App\Services\Crypto\PasswordHasherService;
use DateTimeImmutable;
use Throwable;

final class LicenciaService
{
    public function __construct(
        private readonly LicenciaRepositoryInterface $repository,
        private readonly CifradoReversibleInterface $cipher,
        private readonly PasswordHasherService $passwordHasher
    ) {
    }

    public function listAll(array $filters = []): array
    {
        return $this->repository->listAll($filters);
    }

    public function listEligibleAssets(): array
    {
        return $this->repository->listEligibleAssets();
    }

    public function findById(int $licenseId): ?array
    {
        return $this->repository->findById($licenseId);
    }

    public function detail(int $licenseId): ?array
    {
        $license = $this->repository->findById($licenseId);

        if ($license === null) {
            return null;
        }

        $assignments = $this->repository->listAssignments($licenseId);
        $activeAssignments = $this->repository
            ->countActiveAssignments($licenseId);

        return [
            'license' => $license,
            'assignments' => $assignments,
            'activeAssignments' => $activeAssignments,
            'availableSeats' => max(
                (int) $license['cantidadPuestos'] - $activeAssignments,
                0
            ),
        ];
    }

    public function create(array $input): int
    {
        $data = $this->normalize($input);
        $errors = $this->validate($data, true);

        if ($data['idActivo'] > 0) {
            if (
                $this->repository->findByAssetId($data['idActivo']) !== null
            ) {
                $errors['idActivo'] =
                    'La copia seleccionada ya tiene datos de licencia.';
            } elseif (
                $this->repository->findEligibleAsset($data['idActivo'])
                === null
            ) {
                $errors['idActivo'] =
                    'Selecciona una copia activa de un producto tipo licencia.';
            }
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $data['claveCifrada'] = $data['claveLicencia'] !== null
            ? $this->cipher->cifrar($data['claveLicencia'])
            : null;

        unset($data['claveLicencia']);

        return $this->repository->create($data);
    }

    public function update(
        int $licenseId,
        array $input
    ): void {
        $license = $this->repository->findById($licenseId);

        if ($license === null) {
            throw new ValidationException([
                'general' => 'La licencia solicitada no existe.',
            ]);
        }

        $data = $this->normalize($input, (int) $license['idActivo']);
        $errors = $this->validate($data, false);
        $activeAssignments = $this->repository
            ->countActiveAssignments($licenseId);

        if ($data['cantidadPuestos'] < $activeAssignments) {
            $errors['cantidadPuestos'] =
                'No puedes reducir los puestos por debajo de las '
                . 'asignaciones activas (' . $activeAssignments . ').';
        }

        $removeKey = ((string) ($input['eliminarClave'] ?? '0')) === '1';

        if ($removeKey && $data['claveLicencia'] !== null) {
            $errors['claveLicencia'] =
                'Elige entre reemplazar o eliminar la clave.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $data['claveCifrada'] = $license['claveCifrada'];

        if ($removeKey) {
            $data['claveCifrada'] = null;
        } elseif ($data['claveLicencia'] !== null) {
            $data['claveCifrada'] = $this->cipher
                ->cifrar($data['claveLicencia']);
        }

        unset($data['idActivo'], $data['claveLicencia']);

        $this->repository->update($licenseId, $data);
    }

    public function listCollaborators(): array
    {
        return $this->repository->listActiveCollaborators();
    }

    public function assign(
        int $licenseId,
        array $input,
        int $administratorId
    ): int {
        $collaboratorId = (int) ($input['idColaborador'] ?? 0);
        $email = trim((string) ($input['correoAsignado'] ?? ''));
        $observations = trim((string) ($input['observaciones'] ?? ''));
        $errors = [];

        if ($collaboratorId <= 0) {
            $errors['idColaborador'] = 'Selecciona un colaborador.';
        }

        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['correoAsignado'] = 'Escribe un correo válido.';
        }

        if (mb_strlen($email) > 120) {
            $errors['correoAsignado'] =
                'El correo no puede superar 120 caracteres.';
        }

        if (mb_strlen($observations) > 2000) {
            $errors['observaciones'] =
                'Las observaciones no pueden superar 2000 caracteres.';
        }

        $collaborator = $collaboratorId > 0
            ? $this->repository->findActiveCollaborator($collaboratorId)
            : null;

        if ($collaboratorId > 0 && $collaborator === null) {
            $errors['idColaborador'] =
                'Selecciona un colaborador activo.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        try {
            $this->repository->beginTransaction();
            $license = $this->repository->findById($licenseId, true);

            if ($license === null || !(bool) $license['activo']) {
                throw new ValidationException([
                    'general' => 'La licencia no está disponible.',
                ]);
            }

            if (
                $license['fechaExpiracion'] !== null
                && (string) $license['fechaExpiracion'] < date('Y-m-d')
            ) {
                throw new ValidationException([
                    'general' =>
                        'No se puede asignar una licencia vencida.',
                ]);
            }

            $activeCount = $this->repository
                ->countActiveAssignments($licenseId);

            if ($activeCount >= (int) $license['cantidadPuestos']) {
                throw new ValidationException([
                    'general' =>
                        'La licencia ya no tiene puestos disponibles.',
                ]);
            }

            if ($this->repository->hasActiveAssignment(
                $licenseId,
                $collaboratorId
            )) {
                throw new ValidationException([
                    'idColaborador' =>
                        'Este colaborador ya tiene la licencia asignada.',
                ]);
            }

            $assignmentId = $this->repository->createAssignment([
                'idLicencia' => $licenseId,
                'idColaborador' => $collaboratorId,
                'idUsuarioAsigna' => $administratorId,
                'correoAsignado' => $email !== '' ? $email : null,
                'observaciones' =>
                    $observations !== '' ? $observations : null,
            ]);

            $this->repository->commit();

            return $assignmentId;
        } catch (Throwable $exception) {
            $this->repository->rollBack();
            throw $exception;
        }
    }

    public function revoke(int $assignmentId): int
    {
        try {
            $this->repository->beginTransaction();
            $assignment = $this->repository->findAssignmentById(
                $assignmentId,
                true
            );

            if (
                $assignment === null
                || $assignment['estadoAsignacion'] !== 'ACTIVA'
                || $assignment['fechaRevocacion'] !== null
            ) {
                throw new ValidationException([
                    'general' =>
                        'La asignación ya fue revocada o no existe.',
                ]);
            }

            $this->repository->revokeAssignment($assignmentId);
            $this->repository->commit();

            return (int) $assignment['idLicencia'];
        } catch (Throwable $exception) {
            $this->repository->rollBack();
            throw $exception;
        }
    }

    public function revealKey(
        int $licenseId,
        int $userId,
        string $currentPassword
    ): string {
        $license = $this->repository->findById($licenseId);

        if ($license === null || empty($license['claveCifrada'])) {
            throw new ValidationException([
                'general' =>
                    'Esta licencia no tiene una clave almacenada.',
            ]);
        }

        $passwordHash = $this->repository->getUserPasswordHash($userId);

        if (
            $passwordHash === null
            || !$this->passwordHasher->verificar(
                $currentPassword,
                $passwordHash
            )
        ) {
            throw new ValidationException([
                'contrasenaActual' =>
                    'La contraseña actual no es correcta.',
            ]);
        }

        return $this->cipher->descifrar(
            (string) $license['claveCifrada']
        );
    }

    public function myLicenses(int $userId): array
    {
        return $this->repository->listMyLicenses($userId);
    }

    private function normalize(
        array $input,
        ?int $fixedAssetId = null
    ): array {
        $provider = trim((string) ($input['proveedor'] ?? ''));
        $url = trim((string) ($input['urlAcceso'] ?? ''));
        $key = trim((string) ($input['claveLicencia'] ?? ''));
        $start = trim((string) ($input['fechaInicio'] ?? ''));
        $expiration = trim((string) ($input['fechaExpiracion'] ?? ''));
        $observations = trim((string) ($input['observaciones'] ?? ''));

        return [
            'idActivo' => $fixedAssetId
                ?? (int) ($input['idActivo'] ?? 0),
            'proveedor' => $provider !== '' ? $provider : null,
            'tipoLicencia' => trim(
                (string) ($input['tipoLicencia'] ?? '')
            ),
            'urlAcceso' => $url !== '' ? $url : null,
            'claveLicencia' => $key !== '' ? $key : null,
            'cantidadPuestos' => (int) ($input['cantidadPuestos'] ?? 1),
            'fechaInicio' => $start !== '' ? $start : null,
            'fechaExpiracion' => $expiration !== '' ? $expiration : null,
            'renovacionAutomatica' =>
                isset($input['renovacionAutomatica']) ? 1 : 0,
            'observaciones' =>
                $observations !== '' ? $observations : null,
        ];
    }

    private function validate(array $data, bool $creating): array
    {
        $errors = [];

        if ($creating && $data['idActivo'] <= 0) {
            $errors['idActivo'] =
                'Selecciona una copia de producto tipo licencia.';
        }

        $typeLength = mb_strlen($data['tipoLicencia']);

        if ($typeLength < 2 || $typeLength > 80) {
            $errors['tipoLicencia'] =
                'El tipo debe contener entre 2 y 80 caracteres.';
        }

        if (
            $data['proveedor'] !== null
            && mb_strlen($data['proveedor']) > 120
        ) {
            $errors['proveedor'] =
                'El proveedor no puede superar 120 caracteres.';
        }

        if (
            $data['urlAcceso'] !== null
            && (
                mb_strlen($data['urlAcceso']) > 500
                || filter_var(
                    $data['urlAcceso'],
                    FILTER_VALIDATE_URL
                ) === false
            )
        ) {
            $errors['urlAcceso'] = 'Escribe una URL válida.';
        }

        if (
            $data['claveLicencia'] !== null
            && mb_strlen($data['claveLicencia']) > 180
        ) {
            $errors['claveLicencia'] =
                'La clave no puede superar 180 caracteres.';
        }

        if (
            $data['cantidadPuestos'] < 1
            || $data['cantidadPuestos'] > 100000
        ) {
            $errors['cantidadPuestos'] =
                'La cantidad de puestos debe estar entre 1 y 100000.';
        }

        foreach (['fechaInicio', 'fechaExpiracion'] as $field) {
            if (
                $data[$field] !== null
                && !$this->isValidDate($data[$field])
            ) {
                $errors[$field] = 'La fecha no tiene un formato válido.';
            }
        }

        if (
            $data['fechaInicio'] !== null
            && $data['fechaExpiracion'] !== null
            && $data['fechaExpiracion'] < $data['fechaInicio']
        ) {
            $errors['fechaExpiracion'] =
                'La expiración no puede ser anterior al inicio.';
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

    private function isValidDate(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }
}
