<?php

interface ILogin
{
    /**
     * Busca un usuario por su nombre de usuario.
     *
     * @param string $usuario
     * @return array|false
     */
    public function buscarUsuario($usuario);

    /**
     * Registra un intento de inicio de sesión.
     *
     * @param int|null $idUsuario
     * @param string $usuarioIngresado
     * @param string $ip
     * @param bool $exito
     * @param string $descripcion
     * @return bool
     */
    public function registrarIntento($idUsuario, $usuarioIngresado, $ip, $exito, $descripcion);

    /**
     * Incrementa los intentos fallidos del usuario.
     *
     * @param int $idUsuario
     * @param int $intentosActuales
     * @return bool
     */
    public function aumentarIntentos($idUsuario, $intentosActuales);

    /**
     * Reinicia el contador de intentos fallidos.
     *
     * @param int $idUsuario
     * @return bool
     */
    public function reiniciarIntentos($idUsuario);

    public function existeAdministrador();

    public function registrarAdministrador(Usuario $usuario);
}