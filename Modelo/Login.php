<?php

require_once __DIR__ . "/../Config/Conexion.php";
require_once __DIR__ . "/Interfaces/ILogin.php";

class Login extends Conexion implements ILogin
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = $this->conectar();
    }

    /**
     * Busca un usuario por su nombre de usuario.
     */
    public function buscarUsuario($usuario)
    {
        try {

            $sql = "SELECT * FROM Usuario
                    WHERE usuario = :usuario
                    AND activo = 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":usuario", $usuario);
            $stmt->execute();

            return $stmt->fetch();

        } catch (PDOException $e) {

            throw new Exception("Error al buscar el usuario: " . $e->getMessage());

        }
    }

    /**
     * Guarda cada intento de login.
     */
    public function registrarIntento($idUsuario, $usuarioIngresado, $ip, $exito, $descripcion)
    {
        try {

            $sql = "INSERT INTO Historial_Login
                    (idUsuario, usuarioIngresado, direccionIP, exito, descripcion)
                    VALUES
                    (:idUsuario, :usuarioIngresado, :ip, :exito, :descripcion)";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(":idUsuario", $idUsuario);
            $stmt->bindParam(":usuarioIngresado", $usuarioIngresado);
            $stmt->bindParam(":ip", $ip);
            $stmt->bindParam(":exito", $exito);
            $stmt->bindParam(":descripcion", $descripcion);

            return $stmt->execute();

        } catch (PDOException $e) {

            throw new Exception("Error al registrar el historial.");

        }
    }

    /**
     * Incrementa los intentos fallidos.
     */
    public function aumentarIntentos($idUsuario, $intentosActuales)
    {
        try {

            $nuevoIntento = $intentosActuales + 1;

            if ($nuevoIntento >= 3) {

                $sql = "UPDATE Usuario
                        SET intentosFallidos = :intentos,
                            bloqueado = 1
                        WHERE idUsuario = :idUsuario";

            } else {

                $sql = "UPDATE Usuario
                        SET intentosFallidos = :intentos
                        WHERE idUsuario = :idUsuario";

            }

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(":intentos", $nuevoIntento);
            $stmt->bindParam(":idUsuario", $idUsuario);

            return $stmt->execute();

        } catch (PDOException $e) {

            throw new Exception("Error al actualizar intentos.");

        }
    }

    /**
     * Reinicia los intentos cuando el login es correcto.
     */
    public function reiniciarIntentos($idUsuario)
    {
        try {

            $sql = "UPDATE Usuario
                    SET intentosFallidos = 0
                    WHERE idUsuario = :idUsuario";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(":idUsuario", $idUsuario);

            return $stmt->execute();

        } catch (PDOException $e) {

            throw new Exception("Error al reiniciar intentos.");

        }
    }

    public function existeAdministrador()
    {
    try {

        $sql = "SELECT COUNT(*) AS total FROM Usuario";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetch()["total"];

        } catch (PDOException $e) {

        throw new Exception("Error al verificar los usuarios.");

        }
    }

    public function registrarAdministrador(Usuario $usuario)
    {
        try {

            $sql = "INSERT INTO Usuario
            (
                nombre,
                apellido,
                usuario,
                correo,
                passwordHash,
                idRol,
                activo,
                intentosFallidos,
                bloqueado
            )

            VALUES
            (
                :nombre,
                :apellido,
                :usuario,
                :correo,
                :passwordHash,
                1,
                1,
                0,
                0
            )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(":nombre", $usuario->getNombre());
            $stmt->bindValue(":apellido", $usuario->getApellido());
            $stmt->bindValue(":usuario", $usuario->getUsuario());
            $stmt->bindValue(":correo", $usuario->getCorreo());
            $stmt->bindValue(":passwordHash", $usuario->getPasswordHash());

            return $stmt->execute();

        } catch (PDOException $e) {

            throw new Exception("No fue posible registrar el administrador.");

        }
    }

    /**
     * Registra un nuevo usuario como colaborador.
     */
    public function registrarColaborador(Usuario $usuario)
    {
        try {

            $sql = "INSERT INTO Usuario
            (
                nombre,
                apellido,
                usuario,
                correo,
                passwordHash,
                idRol,
                activo,
                intentosFallidos,
                bloqueado
            )

            VALUES
            (
                :nombre,
                :apellido,
                :usuario,
                :correo,
                :passwordHash,
                2,
                1,
                0,
                0
            )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindValue(":nombre", $usuario->getNombre());
            $stmt->bindValue(":apellido", $usuario->getApellido());
            $stmt->bindValue(":usuario", $usuario->getUsuario());
            $stmt->bindValue(":correo", $usuario->getCorreo());
            $stmt->bindValue(":passwordHash", $usuario->getPasswordHash());

            return $stmt->execute();

        } catch (PDOException $e) {

            throw new Exception("No fue posible registrar el colaborador.");

        }
    }

    /**
     * Obtiene todos los usuarios de la base de datos.
     */
    public function obtenerTodosUsuarios()
    {
        try {

            $sql = "SELECT * FROM Usuario ORDER BY nombre ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $e) {

            throw new Exception("Error al obtener los usuarios.");

        }
    }

    /**
     * Cambia el rol de un usuario.
     */
    public function cambiarRol($idUsuario, $nuevoRol)
    {
        try {

            $sql = "UPDATE Usuario SET idRol = :idRol WHERE idUsuario = :idUsuario";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":idRol", $nuevoRol);
            $stmt->bindParam(":idUsuario", $idUsuario);

            return $stmt->execute();

        } catch (PDOException $e) {

            throw new Exception("Error al cambiar el rol del usuario.");

        }
    }

    /**
     * Verifica si el usuario ya existe.
     */
    public function usuarioExiste($usuario)
    {
        try {

            $sql = "SELECT COUNT(*) AS total FROM Usuario WHERE usuario = :usuario";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":usuario", $usuario);
            $stmt->execute();

            return $stmt->fetch()["total"] > 0;

        } catch (PDOException $e) {

            throw new Exception("Error al verificar si el usuario existe.");

        }
    }

    /**
     * Busca un usuario por su ID.
     */
    public function buscarUserById($idUsuario)
    {
        try {

            $sql = "SELECT * FROM Usuario WHERE idUsuario = :idUsuario";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":idUsuario", $idUsuario);
            $stmt->execute();

            return $stmt->fetch();

        } catch (PDOException $e) {

            throw new Exception("Error al buscar el usuario por ID.");

        }
    }

    /**
     * Actualiza los datos del perfil de un usuario.
     */
    public function actualizarPerfil($idUsuario, $nombre, $apellido, $correo, $passwordHash = null)
    {
        try {

            if ($passwordHash !== null) {
                // Actualizar con contraseña
                $sql = "UPDATE Usuario 
                        SET nombre = :nombre, 
                            apellido = :apellido, 
                            correo = :correo,
                            passwordHash = :passwordHash
                        WHERE idUsuario = :idUsuario";

                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(":nombre", $nombre);
                $stmt->bindParam(":apellido", $apellido);
                $stmt->bindParam(":correo", $correo);
                $stmt->bindParam(":passwordHash", $passwordHash);
                $stmt->bindParam(":idUsuario", $idUsuario);

            } else {
                // Actualizar sin contraseña
                $sql = "UPDATE Usuario 
                        SET nombre = :nombre, 
                            apellido = :apellido, 
                            correo = :correo
                        WHERE idUsuario = :idUsuario";

                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(":nombre", $nombre);
                $stmt->bindParam(":apellido", $apellido);
                $stmt->bindParam(":correo", $correo);
                $stmt->bindParam(":idUsuario", $idUsuario);
            }

            return $stmt->execute();

        } catch (PDOException $e) {

            throw new Exception("Error al actualizar el perfil.");

        }
    }
}