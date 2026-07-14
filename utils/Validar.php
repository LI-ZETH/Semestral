<?php

class Validar
{
    /**
     * Verifica que un campo no esté vacío.
     */
    public function campoRequerido($dato)
    {
        return !empty(trim($dato));
    }

    /**
     * Valida la longitud de un texto.
     */
    public function validarLongitud($dato, $min, $max)
    {
        $longitud = mb_strlen(trim($dato), "UTF-8");

        return ($longitud >= $min && $longitud <= $max);
    }

    /**
     * Valida un correo electrónico.
     */
    public function validarCorreo($correo)
    {
        return filter_var($correo, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Valida que solo contenga letras y espacios.
     */
    public function validarTexto($texto)
    {
        return preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/u', $texto);
    }

    /**
     * Valida un nombre de usuario.
     * Permite letras, números y guion bajo.
     */
    public function validarUsuario($usuario)
    {
        return preg_match('/^[A-Za-z0-9_]+$/', $usuario);
    }

    /**
     * Valida una contraseña.
     * Mínimo 8 caracteres,
     * una mayúscula,
     * una minúscula,
     * un número
     * y un carácter especial.
     */
    public function validarPassword($password)
    {
        return preg_match(
            '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.#_-])[A-Za-z\d@$!%*?&.#_-]{8,}$/',
            $password
        );
    }

    /**
     * Valida números enteros.
     */
    public function validarNumero($numero)
    {
        return filter_var($numero, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Valida números decimales.
     */
    public function validarDecimal($numero)
    {
        return filter_var($numero, FILTER_VALIDATE_FLOAT) !== false;
    }

    /**
     * Valida una URL.
     */
    public function validarURL($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }
}