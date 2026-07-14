<?php

class Auditoria
{
    private $idAuditoria;
    private $idUsuario;
    private $accion;
    private $descripcion;
    private $firmaDigital;
    private $fecha;

    public function __construct(
        $idAuditoria = null,
        $idUsuario = null,
        $accion = "",
        $descripcion = "",
        $firmaDigital = "",
        $fecha = null
    ) {
        $this->idAuditoria = $idAuditoria;
        $this->idUsuario = $idUsuario;
        $this->accion = $accion;
        $this->descripcion = $descripcion;
        $this->firmaDigital = $firmaDigital;
        $this->fecha = $fecha;
    }

    // Getters

    public function getIdAuditoria(){ return $this->idAuditoria; }
    public function getIdUsuario(){ return $this->idUsuario; }
    public function getAccion(){ return $this->accion; }
    public function getDescripcion(){ return $this->descripcion; }
    public function getFirmaDigital(){ return $this->firmaDigital; }
    public function getFecha(){ return $this->fecha; }

    // Setters

    public function setIdAuditoria($idAuditoria){ $this->idAuditoria = $idAuditoria; }
    public function setIdUsuario($idUsuario){ $this->idUsuario = $idUsuario; }
    public function setAccion($accion){ $this->accion = $accion; }
    public function setDescripcion($descripcion){ $this->descripcion = $descripcion; }
    public function setFirmaDigital($firmaDigital){ $this->firmaDigital = $firmaDigital; }
    public function setFecha($fecha){ $this->fecha = $fecha; }
}