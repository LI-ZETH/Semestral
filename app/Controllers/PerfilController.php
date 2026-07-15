<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Permissions;
use App\Core\Session;
use App\Core\ValidationException;
use App\Repositories\PerfilRepository;
use App\Services\Auth\PasswordPolicy;
use App\Services\Crypto\PasswordHasherService;
use App\Services\PerfilService;
use Throwable;

final class PerfilController extends Controller
{
    public function show(): void
    {
        Auth::requirePermission(
            Permissions::PERFIL_VER
        );

        $profile = $this->buildService()->getProfile(
            Auth::id() ?? 0
        );

        if ($profile === null) {
            $this->renderNotFound();
            return;
        }

        $this->view(
            'perfil/show',
            [
                'title' => 'Mi perfil',
                'profile' => $profile,
                'success' => flash('success'),
                'error' => flash('error'),
            ]
        );
    }

    public function edit(): void
    {
        Auth::requirePermission(
            Permissions::PERFIL_EDITAR
        );

        $service = $this->buildService();
        $profile = $service->getProfile(
            Auth::id() ?? 0
        );

        if ($profile === null) {
            $this->renderNotFound();
            return;
        }

        $this->view(
            'perfil/edit',
            [
                'title' => 'Editar mi perfil',
                'profile' => array_replace(
                    $profile,
                    flash('old', [])
                ),
                'locations' => $service->listLocations(),
                'errors' => flash('errors', []),
            ]
        );
    }

    public function update(): void
    {
        Auth::requirePermission(
            Permissions::PERFIL_EDITAR
        );

        $userId = Auth::id() ?? 0;

        try {
            $updated = $this->buildService()->updateProfile(
                $userId,
                $_POST
            );

            Auth::login($updated);

            Session::flash(
                'success',
                'Tu información fue actualizada correctamente.'
            );

            $this->redirectToProfile();
        } catch (ValidationException $exception) {
            Session::flash(
                'errors',
                $exception->getErrors()
            );
            Session::flash(
                'old',
                $this->safeOldInput($_POST)
            );

            header(
                'Location: '
                . base_url('perfil/editar')
            );

            exit;
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        'No fue posible actualizar tu perfil.',
                ]
            );
            Session::flash(
                'old',
                $this->safeOldInput($_POST)
            );

            header(
                'Location: '
                . base_url('perfil/editar')
            );

            exit;
        }
    }

    public function passwordForm(): void
    {
        Auth::requirePermission(
            Permissions::PERFIL_EDITAR
        );

        $this->view(
            'perfil/password',
            [
                'title' => 'Cambiar contraseña',
                'errors' => flash('errors', []),
            ]
        );
    }

    public function changePassword(): void
    {
        Auth::requirePermission(
            Permissions::PERFIL_EDITAR
        );

        try {
            $this->buildService()->changePassword(
                Auth::id() ?? 0,
                $_POST
            );

            Session::flash(
                'success',
                'La contraseña fue actualizada correctamente.'
            );

            $this->redirectToProfile();
        } catch (ValidationException $exception) {
            Session::flash(
                'errors',
                $exception->getErrors()
            );

            header(
                'Location: '
                . base_url('perfil/contrasena')
            );

            exit;
        } catch (Throwable $exception) {
            Session::flash(
                'errors',
                [
                    'general' =>
                        'No fue posible actualizar la contraseña.',
                ]
            );

            header(
                'Location: '
                . base_url('perfil/contrasena')
            );

            exit;
        }
    }

    private function buildService(): PerfilService
    {
        return new PerfilService(
            new PerfilRepository(),
            new PasswordHasherService(),
            new PasswordPolicy()
        );
    }

    private function safeOldInput(array $input): array
    {
        return [
            'cedula' => trim(
                (string) ($input['cedula'] ?? '')
            ),
            'nombre' => trim(
                (string) ($input['nombre'] ?? '')
            ),
            'apellido' => trim(
                (string) ($input['apellido'] ?? '')
            ),
            'correo' => trim(
                (string) ($input['correo'] ?? '')
            ),
            'telefono' => trim(
                (string) ($input['telefono'] ?? '')
            ),
            'cargo' => trim(
                (string) ($input['cargo'] ?? '')
            ),
            'departamento' => trim(
                (string) ($input['departamento'] ?? '')
            ),
            'idUbicacionActual' => (int) (
                $input['idUbicacion'] ?? 0
            ),
            'observacionesUbicacion' => trim(
                (string) (
                    $input['observacionesUbicacion']
                    ?? ''
                )
            ),
        ];
    }

    private function redirectToProfile(): never
    {
        header(
            'Location: ' . base_url('perfil')
        );

        exit;
    }

    private function renderNotFound(): void
    {
        http_response_code(404);

        $this->view(
            'errors/404',
            [
                'title' => 'Perfil no encontrado',
                'path' => '/perfil',
            ]
        );
    }
}
