<?php

require_once "../Modelo/Login.php";

$login = new Login();

if ($login->existeAdministrador() > 0) {
    header("Location: Login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Primer Administrador</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h3 class="text-center">
Crear Administrador
</h3>

</div>

<div class="card-body">

<form action="../Controlador/LoginControlador.php" method="POST">

<input type="hidden" name="accion" value="registrarAdministrador">

<div class="mb-3">

<label>Nombre</label>

<input
type="text"
name="nombre"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Apellido</label>

<input
type="text"
name="apellido"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Usuario</label>

<input
type="text"
name="usuario"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Correo</label>

<input
type="email"
name="correo"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Contraseña</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<button class="btn btn-success w-100">

Crear Administrador

</button>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>