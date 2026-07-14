<?php

class Equipo
{
    private $idEquipo;
    private $etiquetaEquipo;
    private $nombre;
    private $marca;
    private $modelo;
    private $numeroSerie;
    private $direccionIP;
    private $idCategoria;
    private $idEstado;

    public function __construct(
        $idEquipo = null,
        $etiquetaEquipo = "",
        $nombre = "",
        $marca = "",
        $modelo = "",
        $numeroSerie = "",
        $direccionIP = "",
        $idCategoria = null,
        $idEstado = null
    ) {
        $this->idEquipo = $idEquipo;
        $this->etiquetaEquipo = $etiquetaEquipo;
        $this->nombre = $nombre;
        $this->marca = $marca;
        $this->modelo = $modelo;
        $this->numeroSerie = $numeroSerie;
        $this->direccionIP = $direccionIP;
        $this->idCategoria = $idCategoria;
        $this->idEstado = $idEstado;
    }

    // Getters

    public function getIdEquipo()
    {
        return $this->idEquipo;
    }

    public function getEtiquetaEquipo()
    {
        return $this->etiquetaEquipo;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getMarca()
    {
        return $this->marca;
    }

    public function getModelo()
    {
        return $this->modelo;
    }

    public function getNumeroSerie()
    {
        return $this->numeroSerie;
    }

    public function getDireccionIP()
    {
        return $this->direccionIP;
    }

    public function getIdCategoria()
    {
        return $this->idCategoria;
    }

    public function getIdEstado()
    {
        return $this->idEstado;
    }

    // Setters

    public function setIdEquipo($idEquipo)
    {
        $this->idEquipo = $idEquipo;
    }

    public function setEtiquetaEquipo($etiquetaEquipo)
    {
        $this->etiquetaEquipo = $etiquetaEquipo;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setMarca($marca)
    {
        $this->marca = $marca;
    }

    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    }

    public function setNumeroSerie($numeroSerie)
    {
        $this->numeroSerie = $numeroSerie;
    }

    public function setDireccionIP($direccionIP)
    {
        $this->direccionIP = $direccionIP;
    }

    public function setIdCategoria($idCategoria)
    {
        $this->idCategoria = $idCategoria;
    }

    public function setIdEstado($idEstado)
    {
        $this->idEstado = $idEstado;
    }
}