<?php

session_start();

require_once __DIR__ . "/../Modelo/Login.php";
require_once __DIR__ . "/../Modelo/Usuario.php";
require_once __DIR__ . "/../Utilidades/Sanitizar.php";
require_once __DIR__ . "/../Utilidades/Validar.php";

class LoginControlador
{
    private $login;
    private $sanitizar;
    private $validar;

    public function __construct()
    {
        $this->login = new Login();
        $this->sanitizar = new Sanitizar();
        $this->validar = new Validar();
    }

    /**
     * Iniciar sesión
     */
    public function iniciarSesion()
    {
        try {

            if ($_SERVER["REQUEST_METHOD"] != "POST") {
                header("Location: ../Vista/Login.php");
                exit();
            }

            $usuario = $this->sanitizar->limpiarCadena($_POST["usuario"] ?? "");
            $password = $_POST["password"] ?? "";

            // Validar campos vacíos
            if (
                !$this->validar->campoRequerido($usuario) ||
                !$this->validar->campoRequerido($password)
            ) {

                $_SESSION["mensaje"] = "Todos los campos son obligatorios.";

                header("Location: ../Vista/Login.php");
                exit();
            }

            // Buscar usuario
            $datosUsuario = $this->login->buscarUsuario($usuario);

            $ip = $_SERVER["REMOTE_ADDR"];

            if (!$datosUsuario) {

                $this->login->registrarIntento(
                    null,
                    $usuario,
                    $ip,
                    0,
                    "Usuario no encontrado"
                );

                $_SESSION["mensaje"] = "Usuario o contraseña incorrectos.";

                header("Location: ../Vista/Login.php");
                exit();
            }

            // Verificar si está bloqueado
            if ($datosUsuario["bloqueado"] == 1) {

                $_SESSION["mensaje"] = "El usuario se encuentra bloqueado.";

                header("Location: ../Vista/Login.php");
                exit();
            }

            // Verificar contraseña
            if (password_verify($password, $datosUsuario["passwordHash"])) {

                // Reiniciar intentos
                $this->login->reiniciarIntentos($datosUsuario["idUsuario"]);

                // Registrar acceso exitoso
                $this->login->registrarIntento(
                    $datosUsuario["idUsuario"],
                    $usuario,
                    $ip,
                    1,
                    "Inicio de sesión exitoso"
                );

                $_SESSION["idUsuario"] = $datosUsuario["idUsuario"];
                $_SESSION["nombre"] = $datosUsuario["nombre"];
                $_SESSION["usuario"] = $datosUsuario["usuario"];
                $_SESSION["idRol"] = $datosUsuario["idRol"];

                header("Location: ../Vista/Home.php");
                exit();

            } else {

                // Incrementar intentos fallidos
                $this->login->aumentarIntentos(
                    $datosUsuario["idUsuario"],
                    $datosUsuario["intentosFallidos"]
                );

                // Registrar intento fallido
                $this->login->registrarIntento(
                    $datosUsuario["idUsuario"],
                    $usuario,
                    $ip,
                    0,
                    "Contraseña incorrecta"
                );

                $_SESSION["mensaje"] = "Usuario o contraseña incorrectos.";

                header("Location: ../Vista/Login.php");
                exit();
            }

        } catch (Exception $e) {

            $_SESSION["mensaje"] = $e->getMessage();

            header("Location: ../Vista/Login.php");
            exit();
        }
    }
        /**
     * Registrar el primer administrador
     */
    public function registrarAdministrador()
    {
        try {

            // Si ya existe un usuario, no permitir acceso
            if ($this->login->existeAdministrador() > 0) {
                header("Location: ../Vista/Login.php");
                exit();
            }

            $usuario = new Usuario();

            $usuario->setNombre(
                $this->sanitizar->limpiarCadena($_POST["nombre"] ?? "")
            );

            $usuario->setApellido(
                $this->sanitizar->limpiarCadena($_POST["apellido"] ?? "")
            );

            $usuario->setUsuario(
                $this->sanitizar->limpiarCadena($_POST["usuario"] ?? "")
            );

            $usuario->setCorreo(
                $this->sanitizar->limpiarCorreo($_POST["correo"] ?? "")
            );

            // Validaciones básicas
            if (
                !$this->validar->campoRequerido($usuario->getNombre()) ||
                !$this->validar->campoRequerido($usuario->getApellido()) ||
                !$this->validar->campoRequerido($usuario->getUsuario()) ||
                !$this->validar->campoRequerido($usuario->getCorreo()) ||
                !$this->validar->campoRequerido($_POST["password"])
            ) {
                $_SESSION["mensaje"] = "Todos los campos son obligatorios.";
                header("Location: ../Vista/RegistrarAdministrador.php");
                exit();
            }

            $usuario->setPasswordHash(
                password_hash($_POST["password"], PASSWORD_DEFAULT)
            );

            // Registrar en BD
            $this->login->registrarAdministrador($usuario);

            $_SESSION["mensaje"] = "Administrador creado correctamente.";

            header("Location: ../Vista/Login.php");
            exit();

        } catch (Exception $e) {

            $_SESSION["mensaje"] = $e->getMessage();
            header("Location: ../Vista/RegistrarAdministrador.php");
            exit();
        }
    }

    /**
     * Registrar un nuevo colaborador
     */
    public function registrarColaborador()
    {
        try {

            $usuario = new Usuario();

            $usuario->setNombre(
                $this->sanitizar->limpiarCadena($_POST["nombre"] ?? "")
            );

            $usuario->setApellido(
                $this->sanitizar->limpiarCadena($_POST["apellido"] ?? "")
            );

            $usuario->setUsuario(
                $this->sanitizar->limpiarCadena($_POST["usuario"] ?? "")
            );

            $usuario->setCorreo(
                $this->sanitizar->limpiarCorreo($_POST["correo"] ?? "")
            );

            $password = $_POST["password"] ?? "";
            $passwordConfirm = $_POST["password_confirm"] ?? "";

            $ip = $_SERVER["REMOTE_ADDR"];

            // Validaciones básicas
            if (
                !$this->validar->campoRequerido($usuario->getNombre()) ||
                !$this->validar->campoRequerido($usuario->getApellido()) ||
                !$this->validar->campoRequerido($usuario->getUsuario()) ||
                !$this->validar->campoRequerido($usuario->getCorreo()) ||
                !$this->validar->campoRequerido($password)
            ) {
                // Registrar intento fallido
                $this->login->registrarIntento(
                    null,
                    $usuario->getUsuario(),
                    $ip,
                    0,
                    "Campos obligatorios faltantes"
                );

                $_SESSION["mensaje"] = "Todos los campos son obligatorios.";
                header("Location: ../Vista/Registro.php");
                exit();
            }

            // Verificar que las contraseñas coincidan
            if ($password !== $passwordConfirm) {
                // Registrar intento fallido
                $this->login->registrarIntento(
                    null,
                    $usuario->getUsuario(),
                    $ip,
                    0,
                    "Las contraseñas no coinciden"
                );

                $_SESSION["mensaje"] = "Las contraseñas no coinciden.";
                header("Location: ../Vista/Registro.php");
                exit();
            }

            // Verificar longitud mínima de contraseña
            if (strlen($password) < 6) {
                // Registrar intento fallido
                $this->login->registrarIntento(
                    null,
                    $usuario->getUsuario(),
                    $ip,
                    0,
                    "Contraseña muy corta (mínimo 6 caracteres)"
                );

                $_SESSION["mensaje"] = "La contraseña debe tener al menos 6 caracteres.";
                header("Location: ../Vista/Registro.php");
                exit();
            }

            // Verificar si el usuario ya existe
            if ($this->login->usuarioExiste($usuario->getUsuario())) {
                // Registrar intento fallido
                $this->login->registrarIntento(
                    null,
                    $usuario->getUsuario(),
                    $ip,
                    0,
                    "Usuario ya existe"
                );

                $_SESSION["mensaje"] = "El nombre de usuario ya está en uso.";
                header("Location: ../Vista/Registro.php");
                exit();
            }

            $usuario->setPasswordHash(
                password_hash($password, PASSWORD_DEFAULT)
            );

            // Registrar en BD como colaborador
            $this->login->registrarColaborador($usuario);

            // Obtener el ID del usuario recién creado
            $datosUsuario = $this->login->buscarUsuario($usuario->getUsuario());
            $idUsuarioNuevo = $datosUsuario ? $datosUsuario["idUsuario"] : null;

            // Registrar intento exitoso de registro
            $this->login->registrarIntento(
                $idUsuarioNuevo,
                $usuario->getUsuario(),
                $ip,
                1,
                "Registro de nuevo colaborador exitoso"
            );

            $_SESSION["exito"] = "Cuenta creada correctamente. Inicia sesión con tus credenciales.";

            header("Location: ../Vista/Login.php");
            exit();

        } catch (Exception $e) {

            $ip = $_SERVER["REMOTE_ADDR"];
            $usuario = new Usuario();
            $usuario->setUsuario($this->sanitizar->limpiarCadena($_POST["usuario"] ?? ""));

            // Registrar intento fallido por excepción
            $this->login->registrarIntento(
                null,
                $usuario->getUsuario(),
                $ip,
                0,
                "Error: " . $e->getMessage()
            );

            $_SESSION["mensaje"] = $e->getMessage();
            header("Location: ../Vista/Registro.php");
            exit();
        }
    }

    /**
     * Actualizar el perfil del usuario logueado
     */
    public function actualizarPerfil()
    {
        try {

            // Verificar si el usuario está logueado
            if (!isset($_SESSION["idUsuario"])) {
                header("Location: ../Vista/Login.php");
                exit();
            }

            if ($_SERVER["REQUEST_METHOD"] != "POST") {
                header("Location: ../Vista/MiPerfil.php");
                exit();
            }

            $idUsuario = (int)($_POST["idUsuario"] ?? 0);
            $nombre = $this->sanitizar->limpiarCadena($_POST["nombre"] ?? "");
            $apellido = $this->sanitizar->limpiarCadena($_POST["apellido"] ?? "");
            $correo = $this->sanitizar->limpiarCorreo($_POST["correo"] ?? "");
            $password = $_POST["password"] ?? "";
            $passwordConfirm = $_POST["password_confirm"] ?? "";

            // Validaciones
            if (
                !$this->validar->campoRequerido($nombre) ||
                !$this->validar->campoRequerido($apellido) ||
                !$this->validar->campoRequerido($correo)
            ) {
                $_SESSION["mensaje"] = "Los campos nombre, apellido y correo son obligatorios.";
                header("Location: ../Vista/MiPerfil.php");
                exit();
            }

            // Validar contraseña si se va a cambiar
            if (!empty($password)) {

                if ($password !== $passwordConfirm) {
                    $_SESSION["mensaje"] = "Las contraseñas no coinciden.";
                    header("Location: ../Vista/MiPerfil.php");
                    exit();
                }

                if (strlen($password) < 6) {
                    $_SESSION["mensaje"] = "La contraseña debe tener al menos 6 caracteres.";
                    header("Location: ../Vista/MiPerfil.php");
                    exit();
                }

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $this->login->actualizarPerfil($idUsuario, $nombre, $apellido, $correo, $passwordHash);

            } else {
                // Actualizar sin cambiar contraseña
                $this->login->actualizarPerfil($idUsuario, $nombre, $apellido, $correo);
            }

            // Actualizar la sesión con los nuevos datos
            $_SESSION["nombre"] = $nombre;

            $_SESSION["exito"] = "Perfil actualizado correctamente.";

            header("Location: ../Vista/MiPerfil.php");
            exit();

        } catch (Exception $e) {

            $_SESSION["mensaje"] = $e->getMessage();
            header("Location: ../Vista/MiPerfil.php");
            exit();
        }
    }

    /**
     * Cambiar el rol de un usuario (solo administradores)
     */
    public function cambiarRol()
    {
        try {

            // Verificar si el usuario está autenticado y es administrador
            if (!isset($_SESSION["idUsuario"]) || $_SESSION["idRol"] != 1) {
                header("Location: ../Vista/Login.php");
                exit();
            }

            if ($_SERVER["REQUEST_METHOD"] != "POST") {
                header("Location: ../Vista/Administracion.php");
                exit();
            }

            $idUsuario = (int)($_POST["idUsuario"] ?? 0);

            if ($idUsuario <= 0) {
                $_SESSION["mensaje"] = "Usuario inválido.";
                header("Location: ../Vista/Administracion.php");
                exit();
            }

            // Obtener el rol actual del usuario
            $usuarios = $this->login->obtenerTodosUsuarios();
            $rolActual = null;

            foreach ($usuarios as $u) {
                if ($u["idUsuario"] == $idUsuario) {
                    $rolActual = $u["idRol"];
                    break;
                }
            }

            if ($rolActual === null) {
                $_SESSION["mensaje"] = "Usuario no encontrado.";
                header("Location: ../Vista/Administracion.php");
                exit();
            }

            // Cambiar de colaborador a administrador o viceversa
            $nuevoRol = ($rolActual == 1) ? 2 : 1;

            $this->login->cambiarRol($idUsuario, $nuevoRol);

            $rolTexto = ($nuevoRol == 1) ? "Administrador" : "Colaborador";
            $_SESSION["exito"] = "Rol cambiado a " . $rolTexto . " correctamente.";

            header("Location: ../Vista/Administracion.php");
            exit();

        } catch (Exception $e) {

            $_SESSION["mensaje"] = $e->getMessage();
            header("Location: ../Vista/Administracion.php");
            exit();
        }
    }
}
$controlador = new LoginControlador();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["accion"])) {

        if ($_POST["accion"] == "registrarAdministrador") {
            $controlador->registrarAdministrador();
        } elseif ($_POST["accion"] == "registrarColaborador") {
            $controlador->registrarColaborador();
        } elseif ($_POST["accion"] == "actualizarPerfil") {
            $controlador->actualizarPerfil();
        } elseif ($_POST["accion"] == "cambiarRol") {
            $controlador->cambiarRol();
        }

    } else {
        $controlador->iniciarSesion();
    }

}