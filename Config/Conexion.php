<?php

class Conexion
{
    private $host = "localhost";
    private $dbname = "Inventario";
    private $usuario = "root";
    private $password = "";

    protected function conectar()
    {
        try {

            $conexion = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->usuario,
                $this->password
            );

            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $conexion;

        } catch (PDOException $e) {

            die("Error de conexión: " . $e->getMessage());

        }
    }
}