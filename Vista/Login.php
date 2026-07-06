<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Inventario</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background:#f4f6f9;
        }

        .login-card{
            width:420px;
            border-radius:15px;
        }

    </style>

</head>

<body>

<div class="container vh-100 d-flex justify-content-center align-items-center">

    <div class="card shadow login-card">

        <div class="card-body p-4">

            <h2 class="text-center mb-4">
                Inventario Hardware & Software
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

            <form action="../Controlador/LoginControlador.php" method="POST">

                <div class="mb-3">

                    <label class="form-label">
                        Usuario
                    </label>

                    <input
                        type="text"
                        name="usuario"
                        class="form-control"
                        maxlength="20"
                        required
                        autocomplete="off">

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Contraseña
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required>

                </div>

                <div class="d-grid">

                    <button
                        class="btn btn-primary"
                        type="submit">

                        Iniciar Sesión

                    </button>

                </div>

                <p class="text-center text-muted small mt-3">

                    ¿No tienes cuenta? <a href="Registro.php">Crea una aquí</a>

                </p>

            </form>

        </div>

    </div>

</div>

</body>

</html>