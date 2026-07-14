<?php

class Rol
{
    private $idRol;
    private $nombreRol;

    public function __construct(
        $idRol = null,
        $nombreRol = ""
    ) {
        $this->idRol = $idRol;
        $this->nombreRol = $nombreRol;
    }

    // Getters

    public function getIdRol()
    {
        return $this->idRol;
    }

    public function getNombreRol()
    {
        return $this->nombreRol;
    }

    // Setters

    public function setIdRol($idRol)
    {
        $this->idRol = $idRol;
    }

    public function setNombreRol($nombreRol)
    {
        $this->nombreRol = $nombreRol;
    }
}