<?php

class Usuario
{
    private $idUsuario;
    private $cedula;
    private $nombre;
    private $apellido;
    private $usuario;
    private $correo;
    private $passwordHash;
    private $idRol;
    private $activo;
    private $intentosFallidos;
    private $bloqueado;
    private $fechaRegistro;

    public function __construct(
        $idUsuario = null,
        $cedula = "",
        $nombre = "",
        $apellido = "",
        $usuario = "",
        $correo = "",
        $passwordHash = "",
        $idRol = null,
        $activo = 1,
        $intentosFallidos = 0,
        $bloqueado = 0,
        $fechaRegistro = null
    ){
        $this->idUsuario = $idUsuario;
        $this->cedula = $cedula;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->usuario = $usuario;
        $this->correo = $correo;
        $this->passwordHash = $passwordHash;
        $this->idRol = $idRol;
        $this->activo = $activo;
        $this->intentosFallidos = $intentosFallidos;
        $this->bloqueado = $bloqueado;
        $this->fechaRegistro = $fechaRegistro;
    }

    // Getters

    public function getIdUsuario(){
        return $this->idUsuario;
    }

    public function getCedula(){
        return $this->cedula;
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function getApellido(){
        return $this->apellido;
    }

    public function getUsuario(){
        return $this->usuario;
    }

    public function getCorreo(){
        return $this->correo;
    }

    public function getPasswordHash(){
        return $this->passwordHash;
    }

    public function getIdRol(){
        return $this->idRol;
    }

    public function getActivo(){
        return $this->activo;
    }

    public function getIntentosFallidos(){
        return $this->intentosFallidos;
    }

    public function getBloqueado(){
        return $this->bloqueado;
    }

    public function getFechaRegistro(){
        return $this->fechaRegistro;
    }

    // Setters

    public function setIdUsuario($idUsuario){
        $this->idUsuario = $idUsuario;
    }

    public function setCedula($cedula){
        $this->cedula = $cedula;
    }

    public function setNombre($nombre){
        $this->nombre = $nombre;
    }

    public function setApellido($apellido){
        $this->apellido = $apellido;
    }

    public function setUsuario($usuario){
        $this->usuario = $usuario;
    }

    public function setCorreo($correo){
        $this->correo = $correo;
    }

    public function setPasswordHash($passwordHash){
        $this->passwordHash = $passwordHash;
    }

    public function setIdRol($idRol){
        $this->idRol = $idRol;
    }

    public function setActivo($activo){
        $this->activo = $activo;
    }

    public function setIntentosFallidos($intentosFallidos){
        $this->intentosFallidos = $intentosFallidos;
    }

    public function setBloqueado($bloqueado){
        $this->bloqueado = $bloqueado;
    }

    public function setFechaRegistro($fechaRegistro){
        $this->fechaRegistro = $fechaRegistro;
    }
}