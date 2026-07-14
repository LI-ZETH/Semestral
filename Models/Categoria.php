<?php

class Categoria
{
    private $idCategoria;
    private $nombreCategoria;

    public function __construct(
        $idCategoria = null,
        $nombreCategoria = ""
    ) {
        $this->idCategoria = $idCategoria;
        $this->nombreCategoria = $nombreCategoria;
    }

    // Getters

    public function getIdCategoria()
    {
        return $this->idCategoria;
    }

    public function getNombreCategoria()
    {
        return $this->nombreCategoria;
    }

    // Setters

    public function setIdCategoria($idCategoria)
    {
        $this->idCategoria = $idCategoria;
    }

    public function setNombreCategoria($nombreCategoria)
    {
        $this->nombreCategoria = $nombreCategoria;
    }
}