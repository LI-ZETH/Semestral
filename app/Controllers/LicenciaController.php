<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Roles;
use App\Core\Session;
use App\Core\ValidationException;
use App\Repositories\LicenciaRepository;
use App\Services\Crypto\PasswordHasherService;
use App\Services\Crypto\RsaEncryptionService;
use App\Services\LicenciaService;
use Throwable;

final class LicenciaController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        $filters = [
            'search' => trim((string) ($_GET['buscar'] ?? '')),
            'expiration' => trim((string) ($_GET['vencimiento'] ?? '')),
        ];

        $this->view(
            'licencias/index',
            [
                'title' => 'Licencias de software',
                'licenses' => $this->buildService()->listAll($filters),
                'filters' => $filters,
                'success' => flash('success'),
                'error' => flash('error'),
            ]
        );
    }

    public function create(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);
        $service = $this->buildService();

        $this->view(
            'licencias/create',
            [
                'title' => 'Registrar licencia',
                'assets' => $service->listEligibleAssets(),
                'errors' => flash('errors', []),
                'old' => flash(
                    'old',
                    [
                        'cantidadPuestos' => 1,
                        'renovacionAutomatica' => 0,
                    ]
                ),
            ]
        );
    }

    public function store(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);

        try {
            $licenseId = $this->buildService()->create($_POST);

            Session::flash(
                'success',
                'La licencia fue registrada correctamente.'
            );

            $this->redirectToDetail($licenseId);
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            Session::flash('old', $this->safeOldInput($_POST));
            header('Location: ' . base_url('licencias/crear'));
            exit;
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                ['general' => 'No fue posible registrar la licencia.']
            );
            Session::flash('old', $this->safeOldInput($_POST));
            header('Location: ' . base_url('licencias/crear'));
            exit;
        }
    }

    public function show(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);
        $licenseId = $this->queryId('id');
        $result = $this->buildService()->detail($licenseId);

        if ($result === null) {
            $this->renderNotFound();
            return;
        }

        $this->view(
            'licencias/show',
            [
                'title' => 'Detalle de licencia',
                ...$result,
                'success' => flash('success'),
                'error' => flash('error'),
            ]
        );
    }

    public function edit(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);
        $licenseId = $this->queryId('id');
        $license = $this->buildService()->findById($licenseId);

        if ($license === null) {
            $this->renderNotFound();
            return;
        }

        $this->view(
            'licencias/edit',
            [
                'title' => 'Editar licencia',
                'license' => array_replace(
                    $license,
                    flash('old', [])
                ),
                'errors' => flash('errors', []),
            ]
        );
    }

    public function update(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);
        $licenseId = $this->postId('idLicencia');

        if ($licenseId <= 0) {
            $this->renderNotFound();
            return;
        }

        try {
            $this->buildService()->update($licenseId, $_POST);

            Session::flash(
                'success',
                'La licencia fue actualizada correctamente.'
            );

            $this->redirectToDetail($licenseId);
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            Session::flash('old', $this->safeOldInput($_POST));
            header(
                'Location: '
                . base_url('licencias/editar?id=' . $licenseId)
            );
            exit;
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                ['general' => 'No fue posible actualizar la licencia.']
            );
            Session::flash('old', $this->safeOldInput($_POST));
            header(
                'Location: '
                . base_url('licencias/editar?id=' . $licenseId)
            );
            exit;
        }
    }

    public function assignmentForm(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);
        $licenseId = $this->queryId('id');
        $service = $this->buildService();
        $result = $service->detail($licenseId);

        if ($result === null) {
            $this->renderNotFound();
            return;
        }

        $this->view(
            'licencias/assign',
            [
                'title' => 'Asignar licencia',
                ...$result,
                'collaborators' => $service->listCollaborators(),
                'errors' => flash('errors', []),
                'old' => flash('old', []),
            ]
        );
    }

    public function assign(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);
        $licenseId = $this->postId('idLicencia');
        $administratorId = Auth::id() ?? 0;

        try {
            $this->buildService()->assign(
                $licenseId,
                $_POST,
                $administratorId
            );

            Session::flash(
                'success',
                'La licencia fue asignada correctamente.'
            );

            $this->redirectToDetail($licenseId);
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            Session::flash('old', [
                'idColaborador' =>
                    (int) ($_POST['idColaborador'] ?? 0),
                'correoAsignado' => trim(
                    (string) ($_POST['correoAsignado'] ?? '')
                ),
                'observaciones' => trim(
                    (string) ($_POST['observaciones'] ?? '')
                ),
            ]);
            header(
                'Location: '
                . base_url('licencias/asignar?id=' . $licenseId)
            );
            exit;
        } catch (Throwable $exception) {
            Session::flash('error', 'No fue posible asignar la licencia.');
            $this->redirectToDetail($licenseId);
        }
    }

    public function revoke(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);
        $assignmentId = $this->postId('idAsignacionLicencia');

        try {
            $licenseId = $this->buildService()->revoke($assignmentId);
            Session::flash(
                'success',
                'La asignación de licencia fue revocada.'
            );
            $this->redirectToDetail($licenseId);
        } catch (ValidationException $exception) {
            Session::flash(
                'error',
                $exception->getErrors()['general']
                    ?? 'No fue posible revocar la asignación.'
            );
        } catch (Throwable $exception) {
            Session::flash(
                'error',
                'No fue posible revocar la asignación.'
            );
        }

        header('Location: ' . base_url('licencias'));
        exit;
    }

    public function keyForm(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);
        $licenseId = $this->queryId('id');
        $license = $this->buildService()->findById($licenseId);

        if ($license === null) {
            $this->renderNotFound();
            return;
        }

        $this->view(
            'licencias/key',
            [
                'title' => 'Mostrar clave de licencia',
                'license' => $license,
                'errors' => flash('errors', []),
            ]
        );
    }

    public function revealKey(): void
    {
        Auth::requireRole(Roles::ADMINISTRADOR);
        $licenseId = $this->postId('idLicencia');
        $password = (string) ($_POST['contrasenaActual'] ?? '');
        $service = $this->buildService();
        $license = $service->findById($licenseId);

        if ($license === null) {
            $this->renderNotFound();
            return;
        }

        try {
            $key = $service->revealKey(
                $licenseId,
                Auth::id() ?? 0,
                $password
            );

            $this->view(
                'licencias/revealed_key',
                [
                    'title' => 'Clave de licencia',
                    'license' => $license,
                    'licenseKey' => $key,
                ]
            );
        } catch (ValidationException $exception) {
            Session::flash('errors', $exception->getErrors());
            header(
                'Location: '
                . base_url('licencias/clave?id=' . $licenseId)
            );
            exit;
        }
    }

    public function myLicenses(): void
    {
        Auth::requireRole(Roles::COLABORADOR);

        $this->view(
            'licencias/my_licenses',
            [
                'title' => 'Mis licencias',
                'licenses' => $this->buildService()->myLicenses(
                    Auth::id() ?? 0
                ),
            ]
        );
    }

    private function buildService(): LicenciaService
    {
        $configuration = require BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'config'
            . DIRECTORY_SEPARATOR
            . 'crypto.php';

        return new LicenciaService(
            new LicenciaRepository(),
            new RsaEncryptionService(
                (string) $configuration['private_key_path'],
                (string) $configuration['public_key_path'],
                (string) $configuration['private_key_passphrase']
            ),
            new PasswordHasherService()
        );
    }

    private function queryId(string $field): int
    {
        $value = filter_input(INPUT_GET, $field, FILTER_VALIDATE_INT);

        return is_int($value) ? $value : 0;
    }

    private function postId(string $field): int
    {
        $value = filter_input(INPUT_POST, $field, FILTER_VALIDATE_INT);

        return is_int($value) ? $value : 0;
    }

    private function safeOldInput(array $input): array
    {
        return [
            'idActivo' => (int) ($input['idActivo'] ?? 0),
            'proveedor' => trim((string) ($input['proveedor'] ?? '')),
            'tipoLicencia' => trim(
                (string) ($input['tipoLicencia'] ?? '')
            ),
            'urlAcceso' => trim((string) ($input['urlAcceso'] ?? '')),
            'cantidadPuestos' => (int) ($input['cantidadPuestos'] ?? 1),
            'fechaInicio' => trim((string) ($input['fechaInicio'] ?? '')),
            'fechaExpiracion' => trim(
                (string) ($input['fechaExpiracion'] ?? '')
            ),
            'renovacionAutomatica' =>
                isset($input['renovacionAutomatica']) ? 1 : 0,
            'observaciones' => trim(
                (string) ($input['observaciones'] ?? '')
            ),
        ];
    }

    private function redirectToDetail(int $licenseId): void
    {
        header(
            'Location: '
            . base_url('licencias/ver?id=' . $licenseId)
        );
        exit;
    }

    private function renderNotFound(): void
    {
        http_response_code(404);
        $this->view(
            'errors/404',
            [
                'title' => 'Licencia no encontrada',
                'path' => '/licencias',
            ]
        );
    }
}
