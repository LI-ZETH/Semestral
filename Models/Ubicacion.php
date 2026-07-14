<?php

class Ubicacion
{
    private $idUbicacion;
    private $edificio;
    private $piso;
    private $oficina;
    private $descripcion;

    public function __construct(
        $idUbicacion = null,
        $edificio = "",
        $piso = "",
        $oficina = "",
        $descripcion = ""
    ) {
        $this->idUbicacion = $idUbicacion;
        $this->edificio = $edificio;
        $this->piso = $piso;
        $this->oficina = $oficina;
        $this->descripcion = $descripcion;
    }

    // Getters

    public function getIdUbicacion()
    {
        return $this->idUbicacion;
    }

    public function getEdificio()
    {
        return $this->edificio;
    }

    public function getPiso()
    {
        return $this->piso;
    }

    public function getOficina()
    {
        return $this->oficina;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    // Setters

    public function setIdUbicacion($idUbicacion)
    {
        $this->idUbicacion = $idUbicacion;
    }

    public function setEdificio($edificio)
    {
        $this->edificio = $edificio;
    }

    public function setPiso($piso)
    {
        $this->piso = $piso;
    }

    public function setOficina($oficina)
    {
        $this->oficina = $oficina;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
}