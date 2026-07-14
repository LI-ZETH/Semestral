<?php

class SolicitudEquipo
{
    private $idSolicitud;
    private $idColaborador;
    private $tipoSolicitud;
    private $descripcion;
    private $estado;
    private $fechaSolicitud;

    public function __construct(
        $idSolicitud = null,
        $idColaborador = null,
        $tipoSolicitud = "",
        $descripcion = "",
        $estado = "En espera",
        $fechaSolicitud = null
    ) {
        $this->idSolicitud = $idSolicitud;
        $this->idColaborador = $idColaborador;
        $this->tipoSolicitud = $tipoSolicitud;
        $this->descripcion = $descripcion;
        $this->estado = $estado;
        $this->fechaSolicitud = $fechaSolicitud;
    }

    // Getters

    public function getIdSolicitud(){ return $this->idSolicitud; }
    public function getIdColaborador(){ return $this->idColaborador; }
    public function getTipoSolicitud(){ return $this->tipoSolicitud; }
    public function getDescripcion(){ return $this->descripcion; }
    public function getEstado(){ return $this->estado; }
    public function getFechaSolicitud(){ return $this->fechaSolicitud; }

    // Setters

    public function setIdSolicitud($idSolicitud){ $this->idSolicitud = $idSolicitud; }
    public function setIdColaborador($idColaborador){ $this->idColaborador = $idColaborador; }
    public function setTipoSolicitud($tipoSolicitud){ $this->tipoSolicitud = $tipoSolicitud; }
    public function setDescripcion($descripcion){ $this->descripcion = $descripcion; }
    public function setEstado($estado){ $this->estado = $estado; }
    public function setFechaSolicitud($fechaSolicitud){ $this->fechaSolicitud = $fechaSolicitud; }
}