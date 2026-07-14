<?php

class Colaborador
{
    private $idColaborador;
    private $idUsuario;
    private $telefono;
    private $foto;
    private $idUbicacion;
    private $activo;

    public function __construct(
        $idColaborador = null,
        $idUsuario = null,
        $telefono = "",
        $foto = "",
        $idUbicacion = null,
        $activo = 1
    ) {
        $this->idColaborador = $idColaborador;
        $this->idUsuario = $idUsuario;
        $this->telefono = $telefono;
        $this->foto = $foto;
        $this->idUbicacion = $idUbicacion;
        $this->activo = $activo;
    }

    // Getters

    public function getIdColaborador()
    {
        return $this->idColaborador;
    }

    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function getFoto()
    {
        return $this->foto;
    }

    public function getIdUbicacion()
    {
        return $this->idUbicacion;
    }

    public function getActivo()
    {
        return $this->activo;
    }

    // Setters

    public function setIdColaborador($idColaborador)
    {
        $this->idColaborador = $idColaborador;
    }

    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    public function setFoto($foto)
    {
        $this->foto = $foto;
    }

    public function setIdUbicacion($idUbicacion)
    {
        $this->idUbicacion = $idUbicacion;
    }

    public function setActivo($activo)
    {
        $this->activo = $activo;
    }
}