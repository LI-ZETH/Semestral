<?php

class EstadoEquipo
{
    private $idEstado;
    private $nombreEstado;

    public function __construct(
        $idEstado = null,
        $nombreEstado = ""
    ) {
        $this->idEstado = $idEstado;
        $this->nombreEstado = $nombreEstado;
    }

    // Getters

    public function getIdEstado()
    {
        return $this->idEstado;
    }

    public function getNombreEstado()
    {
        return $this->nombreEstado;
    }

    // Setters

    public function setIdEstado($idEstado)
    {
        $this->idEstado = $idEstado;
    }

    public function setNombreEstado($nombreEstado)
    {
        $this->nombreEstado = $nombreEstado;
    }
}