<?php

class Reparacion
{
    private $idReparacion;
    private $idEquipo;
    private $tecnico;
    private $descripcion;
    private $fechaInicio;
    private $fechaFin;
    private $estado;

    public function __construct(
        $idReparacion = null,
        $idEquipo = null,
        $tecnico = null,
        $descripcion = "",
        $fechaInicio = null,
        $fechaFin = null,
        $estado = "Pendiente"
    ) {
        $this->idReparacion = $idReparacion;
        $this->idEquipo = $idEquipo;
        $this->tecnico = $tecnico;
        $this->descripcion = $descripcion;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->estado = $estado;
    }

    // Getters

    public function getIdReparacion(){ return $this->idReparacion; }
    public function getIdEquipo(){ return $this->idEquipo; }
    public function getTecnico(){ return $this->tecnico; }
    public function getDescripcion(){ return $this->descripcion; }
    public function getFechaInicio(){ return $this->fechaInicio; }
    public function getFechaFin(){ return $this->fechaFin; }
    public function getEstado(){ return $this->estado; }

    // Setters

    public function setIdReparacion($idReparacion){ $this->idReparacion = $idReparacion; }
    public function setIdEquipo($idEquipo){ $this->idEquipo = $idEquipo; }
    public function setTecnico($tecnico){ $this->tecnico = $tecnico; }
    public function setDescripcion($descripcion){ $this->descripcion = $descripcion; }
    public function setFechaInicio($fechaInicio){ $this->fechaInicio = $fechaInicio; }
    public function setFechaFin($fechaFin){ $this->fechaFin = $fechaFin; }
    public function setEstado($estado){ $this->estado = $estado; }
}