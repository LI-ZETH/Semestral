<?php

class DevolucionEquipo
{
    private $idDevolucion;
    private $idAsignacion;
    private $usuarioRecibe;
    private $motivo;
    private $observaciones;
    private $fechaRecepcion;

    public function __construct(
        $idDevolucion = null,
        $idAsignacion = null,
        $usuarioRecibe = null,
        $motivo = "",
        $observaciones = "",
        $fechaRecepcion = null
    ) {
        $this->idDevolucion = $idDevolucion;
        $this->idAsignacion = $idAsignacion;
        $this->usuarioRecibe = $usuarioRecibe;
        $this->motivo = $motivo;
        $this->observaciones = $observaciones;
        $this->fechaRecepcion = $fechaRecepcion;
    }

    // Getters

    public function getIdDevolucion(){ return $this->idDevolucion; }
    public function getIdAsignacion(){ return $this->idAsignacion; }
    public function getUsuarioRecibe(){ return $this->usuarioRecibe; }
    public function getMotivo(){ return $this->motivo; }
    public function getObservaciones(){ return $this->observaciones; }
    public function getFechaRecepcion(){ return $this->fechaRecepcion; }

    // Setters

    public function setIdDevolucion($idDevolucion){ $this->idDevolucion = $idDevolucion; }
    public function setIdAsignacion($idAsignacion){ $this->idAsignacion = $idAsignacion; }
    public function setUsuarioRecibe($usuarioRecibe){ $this->usuarioRecibe = $usuarioRecibe; }
    public function setMotivo($motivo){ $this->motivo = $motivo; }
    public function setObservaciones($observaciones){ $this->observaciones = $observaciones; }
    public function setFechaRecepcion($fechaRecepcion){ $this->fechaRecepcion = $fechaRecepcion; }
}