<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION["idUsuario"])) {
    header("Location: Login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario | Inventario HW & SW</title>

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
            <span class="navbar-brand">Inventario</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="Home.php">Home</a>
                    </li>
                    <?php if ($_SESSION["idRol"] == 2) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="MiPerfil.php">Mi Perfil</a>
                        </li>
                    <?php } ?>
                    <?php if ($_SESSION["idRol"] == 1) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="Administracion.php">Panel Admin</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="Logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">

        <div class="row">
            <div class="col-md-12">

                <h1 class="mb-4">Inventario de Productos</h1>

                <div class="card shadow">

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-hover">

                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Tipo</th>
                                        <th>Cantidad</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            No hay productos registrados aún.
                                        </td>
                                    </tr>
                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
