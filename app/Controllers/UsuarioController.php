<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Permissions;
use App\Core\Session;
use App\Core\ValidationException;
use App\Repositories\UsuarioManagementRepository;
use App\Services\Auth\PasswordPolicy;
use App\Services\Auth\UserManagementService;
use App\Services\Crypto\PasswordHasherService;

final class UsuarioController extends Controller
{
    public function index(): void
    {
        Auth::requirePermission(
            Permissions::USUARIOS_VER
        );

        $service = $this->buildService();

        $filters = [
            'search' => trim(
                (string) ($_GET['search'] ?? '')
            ),
            'role' => trim(
                (string) ($_GET['role'] ?? '')
            ),
            'status' => (string) (
                $_GET['status'] ?? ''
            ),
        ];

        $this->view(
            'usuarios/index',
            [
                'title' => 'Administración de usuarios',
                'users' => $service->listUsers($filters),
                'roles' => $service->listRoles(),
                'filters' => $filters,
                'success' => flash('success'),
                'error' => flash('error'),
            ]
        );
    }

    public function create(): void
    {
        Auth::requirePermission(
            Permissions::USUARIOS_CREAR
        );

        $this->view(
            'usuarios/create',
            [
                'title' => 'Registrar usuario',
                'roles' => $this->buildService()
                    ->listRoles(),
                'errors' => flash('errors', []),
                'old' => flash(
                    'old',
                    [
                        'nombreRol' => 'Colaborador',
                        'fechaIngreso' => date('Y-m-d'),
                    ]
                ),
            ]
        );
    }

    public function store(): void
    {
        Auth::requirePermission(
            Permissions::USUARIOS_CREAR
        );

        try {
            $this->buildService()->create($_POST);

            Session::flash(
                'success',
                'El usuario fue registrado correctamente.'
            );

            $this->redirectToUsers();
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
                'Location: ' . base_url('usuarios/crear')
            );

            exit;
        }
    }

    public function edit(): void
    {
        Auth::requirePermission(
            Permissions::USUARIOS_EDITAR
        );

        $userId = $this->getUserIdFromQuery();
        $service = $this->buildService();
        $user = $service->findUser($userId);

        if ($user === null) {
            $this->renderNotFound();

            return;
        }

        $old = flash('old', []);

        $this->view(
            'usuarios/edit',
            [
                'title' => 'Editar usuario',
                'roles' => $service->listRoles(),
                'errors' => flash('errors', []),
                'user' => array_replace($user, $old),
            ]
        );
    }

    public function update(): void
    {
        Auth::requirePermission(
            Permissions::USUARIOS_EDITAR
        );

        $userId = filter_input(
            INPUT_POST,
            'idUsuario',
            FILTER_VALIDATE_INT
        );

        if (!is_int($userId) || $userId <= 0) {
            $this->renderNotFound();

            return;
        }

        try {
            $this->buildService()->update(
                $userId,
                Auth::id() ?? 0,
                $_POST
            );

            Session::flash(
                'success',
                'La información del usuario fue actualizada.'
            );

            $this->redirectToUsers();
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
                . base_url(
                    'usuarios/editar?id=' . $userId
                )
            );

            exit;
        }
    }

    public function changeState(): void
    {
        Auth::requirePermission(
            Permissions::USUARIOS_CAMBIAR_ESTADO
        );

        $userId = filter_input(
            INPUT_POST,
            'idUsuario',
            FILTER_VALIDATE_INT
        );

        $activeValue = filter_input(
            INPUT_POST,
            'activo',
            FILTER_VALIDATE_INT
        );

        if (
            !is_int($userId)
            || $userId <= 0
            || !is_int($activeValue)
            || !in_array($activeValue, [0, 1], true)
        ) {
            Session::flash(
                'error',
                'No fue posible cambiar el estado.'
            );

            $this->redirectToUsers();
        }

        try {
            $active = $activeValue === 1;

            $this->buildService()->changeActiveState(
                $userId,
                Auth::id() ?? 0,
                $active
            );

            Session::flash(
                'success',
                $active
                    ? 'La cuenta fue activada.'
                    : 'La cuenta fue desactivada.'
            );
        } catch (ValidationException $exception) {
            Session::flash(
                'error',
                $exception->getErrors()['general']
                    ?? 'No fue posible cambiar el estado.'
            );
        }

        $this->redirectToUsers();
    }

    public function unlock(): void
    {
        Auth::requirePermission(
            Permissions::USUARIOS_DESBLOQUEAR
        );

        $userId = filter_input(
            INPUT_POST,
            'idUsuario',
            FILTER_VALIDATE_INT
        );

        if (!is_int($userId) || $userId <= 0) {
            Session::flash(
                'error',
                'No fue posible desbloquear la cuenta.'
            );

            $this->redirectToUsers();
        }

        try {
            $this->buildService()->unlock($userId);

            Session::flash(
                'success',
                'La cuenta fue desbloqueada correctamente.'
            );
        } catch (ValidationException $exception) {
            Session::flash(
                'error',
                $exception->getErrors()['general']
                    ?? 'No fue posible desbloquear la cuenta.'
            );
        }

        $this->redirectToUsers();
    }

    private function buildService(): UserManagementService
    {
        return new UserManagementService(
            new UsuarioManagementRepository(),
            new PasswordHasherService(),
            new PasswordPolicy()
        );
    }

    private function getUserIdFromQuery(): int
    {
        $userId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        return is_int($userId)
            ? $userId
            : 0;
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
            'usuario' => trim(
                (string) ($input['usuario'] ?? '')
            ),
            'correo' => trim(
                (string) ($input['correo'] ?? '')
            ),
            'nombreRol' => trim(
                (string) ($input['nombreRol'] ?? '')
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
            'fechaIngreso' => trim(
                (string) ($input['fechaIngreso'] ?? '')
            ),
        ];
    }

    private function redirectToUsers(): never
    {
        header(
            'Location: ' . base_url('usuarios')
        );

        exit;
    }

    private function renderNotFound(): void
    {
        http_response_code(404);

        $this->view(
            'errors/404',
            [
                'title' => 'Usuario no encontrado',
                'path' => '/usuarios',
            ]
        );
    }
}