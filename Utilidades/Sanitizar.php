<?php

class Sanitizar
{
    /**
     * Limpia una cadena de texto.
     */
    public function limpiarCadena($dato)
    {
        $dato = trim($dato);
        $dato = stripslashes($dato);
        $dato = strip_tags($dato);
        $dato = htmlspecialchars($dato, ENT_QUOTES, 'UTF-8');

        // Primera letra en mayúscula y el resto en minúsculas
        $dato = mb_convert_case($dato, MB_CASE_TITLE, "UTF-8");
        return $dato;
    }

    /**
     * Sanitiza un correo electrónico.
     */
    public function limpiarCorreo($correo)
    {
        return filter_var(trim($correo), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitiza un número entero.
     */
    public function limpiarEntero($numero)
    {
        return filter_var($numero, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitiza un número decimal.
     */
    public function limpiarDecimal($numero)
    {
        return filter_var($numero, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Sanitiza una URL.
     */
    public function limpiarURL($url)
    {
        return filter_var(trim($url), FILTER_SANITIZE_URL);
    }
}