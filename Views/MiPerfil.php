<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION["idUsuario"])) {
    header("Location: Login.php");
    exit();
}

require_once __DIR__ . "/../Models/Login.php";

$login = new Login();
$datosUsuario = $login->buscarUserById($_SESSION["idUsuario"]);

if (!$datosUsuario) {
    header("Location: Home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil | Inventario</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>

</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <span class="navbar-brand">Mi Perfil</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="Home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Inventario.php">Inventario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">

        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow">

                    <div class="card-header bg-primary text-white">
                        <h3 class="text-center mb-0">Editar Perfil</h3>
                    </div>

                    <div class="card-body">

                        <?php
                        if (isset($_SESSION["mensaje"])) {
                        ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= $_SESSION["mensaje"] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php
                            unset($_SESSION["mensaje"]);
                        }
                        ?>

                        <?php
                        if (isset($_SESSION["exito"])) {
                        ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= $_SESSION["exito"] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php
                            unset($_SESSION["exito"]);
                        }
                        ?>

                        <form action="../Controllers/LoginControlador.php" method="POST">

                            <input type="hidden" name="accion" value="actualizarPerfil">
                            <input type="hidden" name="idUsuario" value="<?= $_SESSION["idUsuario"] ?>">

                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($datosUsuario['nombre'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($datosUsuario['apellido'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Correo</label>
                                <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($datosUsuario['correo'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nueva Contraseña (dejar en blanco si no deseas cambiarla)</label>
                                <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirmar Contraseña</label>
                                <input type="password" name="password_confirm" class="form-control" placeholder="Confirmar contraseña">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Guardar Cambios
                            </button>

                        </form>

                    </div>

                </div>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
