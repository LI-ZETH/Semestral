<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\ValidationException;
use App\Repositories\UsuarioRepository;
use App\Services\Auth\InitialAdminService;
use App\Services\Auth\PasswordPolicy;
use App\Services\Crypto\PasswordHasherService;
use App\Services\Crypto\RsaSignatureService;
use RuntimeException;
use App\Core\Auth;
use App\Core\ClientInfo;
use App\Services\Auth\AuthenticationService;

final class AuthController extends Controller
{
    public function showInitialAdministrator(): void
    {
        $repository = new UsuarioRepository();

        if ($repository->administratorExists()) {
            $this->renderNotFound();

            return;
        }

        $this->view(
            'auth/initial-admin',
            [
                'title' => 'Configurar administrador',
                'errors' => flash('errors', []),
                'old' => flash('old', []),
            ]
        );
    }

    public function storeInitialAdministrator(): void
    {
        $repository = new UsuarioRepository();

        if ($repository->administratorExists()) {
            $this->renderNotFound();

            return;
        }

        try {
            $service = $this->buildInitialAdminService(
                $repository
            );

            $service->register($_POST);

            Session::flash(
                'success',
                'El primer administrador fue registrado correctamente.'
            );

            header(
                'Location: ' . base_url()
            );

            exit;
        } catch (ValidationException $exception) {
            Session::flash(
                'errors',
                $exception->getErrors()
            );

            Session::flash(
                'old',
                [
                    'cedula' => trim(
                        (string) ($_POST['cedula'] ?? '')
                    ),
                    'nombre' => trim(
                        (string) ($_POST['nombre'] ?? '')
                    ),
                    'apellido' => trim(
                        (string) ($_POST['apellido'] ?? '')
                    ),
                    'usuario' => trim(
                        (string) ($_POST['usuario'] ?? '')
                    ),
                    'correo' => trim(
                        (string) ($_POST['correo'] ?? '')
                    ),
                ]
            );

            header(
                'Location: '
                . base_url(
                    'configuracion/primer-administrador'
                )
            );

            exit;
        }
    }

    private function buildInitialAdminService(
        UsuarioRepository $repository
    ): InitialAdminService {
        $configurationPath = BASE_PATH
            . DIRECTORY_SEPARATOR
            . 'config'
            . DIRECTORY_SEPARATOR
            . 'crypto.php';

        if (!is_file($configurationPath)) {
            throw new RuntimeException(
                'No existe config/crypto.php.'
            );
        }

        $configuration = require $configurationPath;

        $rsaService = new RsaSignatureService(
            (string) $configuration['private_key_path'],
            (string) $configuration['public_key_path'],
            (string) $configuration[
                'private_key_passphrase'
            ]
        );

        return new InitialAdminService(
            $repository,
            new PasswordHasherService(),
            new PasswordPolicy(),
            $rsaService
        );
    }

    private function renderNotFound(): void
    {
        http_response_code(404);

        $this->view(
            'errors/404',
            [
                'title' => 'Página no encontrada',
                'path' => '/configuracion/primer-administrador',
            ]
        );
    }

    public function showLogin(): void
    {
        Auth::requireGuest();

        $repository = new UsuarioRepository();

        if (!$repository->administratorExists()) {
            header(
                'Location: '
                . base_url(
                    'configuracion/primer-administrador'
                )
            );

            exit;
        }

        $this->view(
            'auth/login',
            [
                'title' => 'Iniciar sesión',
                'errors' => flash('errors', []),
                'old' => flash('old', []),
                'warning' => flash('warning'),
                'success' => flash('success'),
            ]
        );
    }

    public function login(): void
    {
        Auth::requireGuest();

        try {
            $repository = new UsuarioRepository();

            $service = new AuthenticationService(
                $repository,
                new PasswordHasherService()
            );

            $user = $service->authenticate(
                $_POST,
                ClientInfo::ipAddress(),
                ClientInfo::userAgent()
            );

            Auth::login($user);

            Session::flash(
                'success',
                'Bienvenido a Tránsito CMDB.'
            );

            header(
                'Location: ' . base_url('panel')
            );

            exit;
        } catch (ValidationException $exception) {
            Session::flash(
                'errors',
                $exception->getErrors()
            );

            Session::flash(
                'old',
                [
                    'identifier' => trim(
                        (string) (
                            $_POST['identifier']
                            ?? ''
                        )
                    ),
                ]
            );

            header(
                'Location: ' . base_url('login')
            );

            exit;
        }
    }

    public function logout(): void
    {
        Auth::logout();

        /*
        * Se inicia una sesión nueva únicamente para mostrar
        * el mensaje temporal.
        */
        Session::start();

        Session::flash(
            'success',
            'La sesión fue cerrada correctamente.'
        );

        header(
            'Location: ' . base_url('login')
        );

        exit;
    }
}