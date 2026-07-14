<?php

class AsignacionEquipo
{
    private $idAsignacion;
    private $idEquipo;
    private $idColaborador;
    private $usuarioEntrega;
    private $fechaEntrega;
    private $fechaDevolucion;
    private $estado;

    public function __construct(
        $idAsignacion = null,
        $idEquipo = null,
        $idColaborador = null,
        $usuarioEntrega = null,
        $fechaEntrega = null,
        $fechaDevolucion = null,
        $estado = "Asignado"
    ) {
        $this->idAsignacion = $idAsignacion;
        $this->idEquipo = $idEquipo;
        $this->idColaborador = $idColaborador;
        $this->usuarioEntrega = $usuarioEntrega;
        $this->fechaEntrega = $fechaEntrega;
        $this->fechaDevolucion = $fechaDevolucion;
        $this->estado = $estado;
    }

    // Getters

    public function getIdAsignacion(){ return $this->idAsignacion; }
    public function getIdEquipo(){ return $this->idEquipo; }
    public function getIdColaborador(){ return $this->idColaborador; }
    public function getUsuarioEntrega(){ return $this->usuarioEntrega; }
    public function getFechaEntrega(){ return $this->fechaEntrega; }
    public function getFechaDevolucion(){ return $this->fechaDevolucion; }
    public function getEstado(){ return $this->estado; }

    // Setters

    public function setIdAsignacion($idAsignacion){ $this->idAsignacion = $idAsignacion; }
    public function setIdEquipo($idEquipo){ $this->idEquipo = $idEquipo; }
    public function setIdColaborador($idColaborador){ $this->idColaborador = $idColaborador; }
    public function setUsuarioEntrega($usuarioEntrega){ $this->usuarioEntrega = $usuarioEntrega; }
    public function setFechaEntrega($fechaEntrega){ $this->fechaEntrega = $fechaEntrega; }
    public function setFechaDevolucion($fechaDevolucion){ $this->fechaDevolucion = $fechaDevolucion; }
    public function setEstado($estado){ $this->estado = $estado; }
}