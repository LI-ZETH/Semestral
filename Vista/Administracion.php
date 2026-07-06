<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION["idUsuario"]) || $_SESSION["idRol"] != 1) {
    header("Location: Home.php");
    exit();
}

require_once __DIR__ . "/../Modelo/Login.php";

$login = new Login();
$usuarios = $login->obtenerTodosUsuarios();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración | Inventario</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }
    </style>

</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <span class="navbar-brand">Panel de Administración</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="Home.php">Home</a>
                    </li>
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

                <h1 class="mb-4">Gestión de Usuarios</h1>

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

                <div class="card shadow">

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-hover">

                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Apellido</th>
                                        <th>Usuario</th>
                                        <th>Correo</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php
                                    if ($usuarios && count($usuarios) > 0) {
                                        foreach ($usuarios as $usuario) {
                                            $rolText = $usuario['idRol'] == 1 ? 'Administrador' : 'Colaborador';
                                            $estadoText = $usuario['activo'] == 1 ? 'Activo' : 'Inactivo';
                                            $estadoClase = $usuario['activo'] == 1 ? 'success' : 'danger';
                                    ?>

                                            <tr>
                                                <td><?= $usuario['idUsuario'] ?></td>
                                                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                                <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                                                <td><?= htmlspecialchars($usuario['usuario']) ?></td>
                                                <td><?= htmlspecialchars($usuario['correo']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $usuario['idRol'] == 1 ? 'danger' : 'info' ?>">
                                                        <?= $rolText ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $estadoClase ?>">
                                                        <?= $estadoText ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <form action="../Controlador/LoginControlador.php" method="POST" style="display:inline;">
                                                        <input type="hidden" name="accion" value="cambiarRol">
                                                        <input type="hidden" name="idUsuario" value="<?= $usuario['idUsuario'] ?>">

                                                        <?php if ($usuario['idRol'] == 2) { ?>
                                                            <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('¿Cambiar a Administrador?')">
                                                                Hacer Admin
                                                            </button>
                                                        <?php } else { ?>
                                                            <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('¿Cambiar a Colaborador?')">
                                                                Hacer Collab
                                                            </button>
                                                        <?php } ?>

                                                    </form>
                                                </td>
                                            </tr>

                                    <?php
                                        }
                                    } else {
                                    ?>

                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                No hay usuarios registrados
                                            </td>
                                        </tr>

                                    <?php
                                    }
                                    ?>

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
