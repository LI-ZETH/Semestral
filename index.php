<?php

require_once __DIR__ . "/Models/Login.php";

$login = new Login();

// Iniciar sesión para verificar si ya está logueado
session_start();

// 1. Si ya está logueado, ir al Home
if (isset($_SESSION["idUsuario"])) {
    header("Location: Views/Home.php");
    exit();
}

// 2. Verificar si existe algún usuario en el sistema
try {

    $totalUsuarios = $login->existeAdministrador();

    // 3. Si no hay usuarios, ir a crear el primer administrador
    if ($totalUsuarios == 0) {
        header("Location: Views/RegistrarAdministrador.php");
        exit();
    }

    // 4. Si ya hay usuarios, ir al login
    header("Location: Views/Login.php");
    exit();

} catch (Exception $e) {

    die("Error en el sistema: " . $e->getMessage());
}