<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Inventario</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background:#f4f6f9;
        }

        .registro-card{
            width:450px;
            border-radius:15px;
        }

    </style>

</head>

<body>

<div class="container vh-100 d-flex justify-content-center align-items-center">

    <div class="card shadow registro-card">

        <div class="card-body p-4">

            <h2 class="text-center mb-4">
                Crear Cuenta
            </h2>

            <?php
            if(isset($_SESSION["mensaje"]))
            {
            ?>

                <div class="alert alert-danger">

                    <?=$_SESSION["mensaje"]?>

                </div>

            <?php
                unset($_SESSION["mensaje"]);
            }
            ?>

            <?php
            if(isset($_SESSION["exito"]))
            {
            ?>

                <div class="alert alert-success">

                    <?=$_SESSION["exito"]?>

                </div>

            <?php
                unset($_SESSION["exito"]);
            }
            ?>

            <form action="../Controllers/LoginControlador.php" method="POST">

                <input type="hidden" name="accion" value="registrarColaborador">

                <div class="mb-3">

                    <label class="form-label">Nombre</label>

                    <input type="text" name="nombre" class="form-control" required>

                </div>

                <div class="mb-3">

                    <label class="form-label">Apellido</label>

                    <input type="text" name="apellido" class="form-control" required>

                </div>

                <div class="mb-3">

                    <label class="form-label">Usuario</label>

                    <input type="text" name="usuario" class="form-control" required>

                </div>

                <div class="mb-3">

                    <label class="form-label">Correo</label>

                    <input type="email" name="correo" class="form-control" required>

                </div>

                <div class="mb-3">

                    <label class="form-label">Contraseña</label>

                    <input type="password" name="password" class="form-control" required>

                </div>

                <div class="mb-3">

                    <label class="form-label">Confirmar Contraseña</label>

                    <input type="password" name="password_confirm" class="form-control" required>

                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">

                    Crear Cuenta

                </button>

                <p class="text-center text-muted small">

                    ¿Ya tienes cuenta? <a href="Login.php">Inicia sesión aquí</a>

                </p>

            </form>

        </div>

    </div>

</div>

</body>

</html>
