<?php
session_start();

// Verificar si el usuario inició sesión
if (!isset($_SESSION["idUsuario"])) {
    header("Location: Login.php");
    exit();
}

$rolText = ($_SESSION["idRol"] == 1) ? "Administrador" : "Colaborador";
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio | Inventario HW & SW</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-option {
            transition: transform 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .card-option:hover {
            transform: translateY(-5px);
            text-decoration: none;
        }

        .card-option:hover .card {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
    </style>

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">

        <div class="container-fluid">

            <a class="navbar-brand" href="#">
                Inventario HW & SW
            </a>

            <div class="ms-auto">

                <span class="text-white me-3">

                    <strong><?php echo $_SESSION["nombre"]; ?></strong>
                    <small class="badge bg-light text-dark"><?php echo $rolText; ?></small>

                </span>

                <a href="Logout.php" class="btn btn-outline-light btn-sm">
                    Cerrar Sesión
                </a>

            </div>

        </div>

    </nav>

    <div class="container mt-5">

        <div class="row mb-4">
            <div class="col-md-12">
                <h1>Panel Principal</h1>
                <p class="text-muted">Bienvenido al sistema de gestión de inventario</p>
            </div>
        </div>

        <!-- OPCIONES PARA ADMINISTRADOR -->
        <?php if ($_SESSION["idRol"] == 1) { ?>

            <div class="row">

                <div class="col-md-4 mb-4">
                    <a href="Administracion.php" class="card-option">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">👥 Administrar Usuarios</h5>
                                <p class="card-text text-muted">Gestionar roles y permisos</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 mb-4">
                    <a href="Inventario.php" class="card-option">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">📦 Ver Inventario</h5>
                                <p class="card-text text-muted">Consultar productos</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 mb-4">
                    <a href="MiPerfil.php" class="card-option">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">⚙️ Mi Perfil</h5>
                                <p class="card-text text-muted">Editar mis datos</p>
                            </div>
                        </div>
                    </a>
                </div>

            </div>

        <?php } ?>

        <!-- OPCIONES PARA COLABORADOR -->
        <?php if ($_SESSION["idRol"] == 2) { ?>

            <div class="row">

                <div class="col-md-6 mb-4">
                    <a href="Inventario.php" class="card-option">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">📦 Ver Inventario</h5>
                                <p class="card-text text-muted">Consultar productos disponibles</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6 mb-4">
                    <a href="MiPerfil.php" class="card-option">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">⚙️ Mi Perfil</h5>
                                <p class="card-text text-muted">Cambiar mis datos</p>
                            </div>
                        </div>
                    </a>
                </div>

            </div>

        <?php } ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

        <div class="card shadow">

            <div class="card-body">

                <h2 class="mb-3">
                    Panel Principal
                </h2>

                <p>
                    Bienvenido al Sistema de Inventario de Hardware y Software.
                </p>

                <hr>

                <div class="row">

                    <div class="col-md-3 mb-3">

                        <div class="card border-primary">

                            <div class="card-body text-center">

                                <h5>Usuarios</h5>

                                <p>Administración de usuarios.</p>

                                <a href="#" class="btn btn-primary">
                                    Ingresar
                                </a>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3 mb-3">

                        <div class="card border-success">

                            <div class="card-body text-center">

                                <h5>Inventario</h5>

                                <p>Equipos y software.</p>

                                <a href="#" class="btn btn-success">
                                    Ingresar
                                </a>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3 mb-3">

                        <div class="card border-warning">

                            <div class="card-body text-center">

                                <h5>Categorías</h5>

                                <p>Gestión de categorías.</p>

                                <a href="#" class="btn btn-warning">
                                    Ingresar
                                </a>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3 mb-3">

                        <div class="card border-info">

                            <div class="card-body text-center">

                                <h5>Colaboradores</h5>

                                <p>Administración de colaboradores.</p>

                                <a href="#" class="btn btn-info text-white">
                                    Ingresar
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>