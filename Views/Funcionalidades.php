<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionalidades</title>
    <link rel="stylesheet" href="Styles/estilohome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
    <header>
        <div class="nav-container">
           <div class="logo">
                <img src="../Views/Styles/img/LogoT.png" height="50px" width="75px" alt="Logo TrackIT" />
                <span class="teams-title"> | Semestral</span>
            </div>

            <nav class="nav__container">
                <ul class="nav-items__container">
                    <li><a href="Website.php">Inicio</a></li>
                    <li><a href="Funcionalidades.php">Funcionalidades</a></li>
                    <li><a href="Noticia.php">Noticias</a></li>
                    <li><a href="Nosotros.php">Nosotros</a></li>
                </ul>
            </nav>

        <div class="search-input-wrapper">
            <i class="fas fa-book"></i>
            <a href="Ayuda.php">Manual de Usuario</a>
        </div>

            <div class="social-icons">
                <a href="https://www.instagram.com/" target="_blank" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.linkedin.com/" target="_blank" aria-label="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="https://X.com/" target="_blank" aria-label="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
            </div>

            <div class="sign-in">
                <a href="Login.php" class="sign-in-button">Iniciar sesión</a>
            </div>

        </div>
    </header>

    <section class="features-section__container">
        <div class="timeline-header">
            <br><br>
            <h1>Funcionalidades Destacadas de TrackIT System</h1>
            <p>Descubre cómo nuestro software puede transformar la gestión de equipos dentro de tu empresa</p>
        </div>

        <div class="features-items__container">
            <!-- Artículo 1: Login y Autenticación (Sistema de Usuario) -->
            <article class="feature__item">
                <div class="feature__image">
                    <!-- Icono para representar el login seguro -->
                    <i class="fas fa-user-lock feature-icon"></i>
                </div>
                <div class="feature_information">
                    <h2>Autenticación Segura y Gestión de Usuarios</h2>
                    <p>
                        TrackIT System protege la información de la organización mediante un 
                        sistema de autenticación seguro y control de acceso basado en roles. 
                        Los usuarios administrativos y operativos cuentan con permisos específicos
                        para gestionar inventarios, colaboradores y solicitudes de equipos.
                        <br><br>
                    </p>
                    <button class="news__button">
                        <a
                            href="Login.php "
                            ><span>Iniciar ahora</span>
                            <i class="fas fa-sign-in-alt ml-2"></i>
                        </a>
                    </button>
                </div>
            </article>

            <!-- Artículo 2: Gestión de Tareas (Core del Sistema) -->
            <article class="feature__item">
                <div class="feature__image">
                    <!-- Placeholder de imagen para el gestor de tareas -->
                    <i class="fas fa-tasks feature-icon text-green-500"></i>
                </div>
                <div class="feature_information">
                    <h2>Gestión Inteligente de Activos Tecnológicos</h2>
                    <p>
                        El corazón de TrackIT System es su potente módulo de administración 
                        de activos. La plataforma permite registrar equipos de cómputo,
                         dispositivos de red, software y licencias. Además, ofrece reportes detallados
                          sobre el estado de cada activo, su ubicación, historial de mantenimiento y 
                          asignación a colaboradores.
                        <br><br>
                    </p>
                    <button class="news__button">
                        <a
                            href="Login.php"
                            ><span>Gestionar productos</span>
                            <i class="fas fa-sign-in-alt ml-2"></i>
                        </a>
                    </button>
            </article>

            <!-- Artículo 3: Colaboración y Equipos -->
            <article class="feature__item">
                <div class="feature__image">
                    <!-- Icono para colaboración -->
                    <i class="fas fa-users feature-icon text-purple-500"></i>
                </div>
                <div class="feature_information">
                    <h2>Administración de Colaboradores y Asignación de Equipos</h2>
                    <p>
                        TrackIT System permite asociar equipos y licencias de software
                         a cada colaborador de la organización.
                         El sistema mantiene un historial de asignaciones, 
                        devoluciones y cambios de ubicación, facilitando la 
                        trazabilidad de los activos tecnológicos.
                        También permite gestionar solicitudes de nuevos equipos 
                        o software, ayudando a las empresas a planificar sus necesidades 
                        tecnológicas futuras. 
                        <br><br> </p>
                    <button class="news__button">
                        <a
                            href="Login.php"
                            ><span>Iniciar colaboración</span>
                            <i class="fas fa-sign-in-alt ml-2"></i>
                        </a>
                    </button>
                </div>
            </article>

            <!-- Artículo 4: Productividad y Métricas -->
            <article class="feature__item">
                <div class="feature__image">
                    <!-- Icono para métricas y reportes -->
                    <i class="fas fa-chart-bar feature-icon text-red-500"></i>
                </div>
                <div class="feature_information">
                    <h2>Reportes, Estadísticas y Control de Inventario</h2>
                    <p>
                        Obtén una visión completa de la infraestructura tecnológica de tu organización mediante dashboards y reportes dinámicos
                        que muestran métricas clave como la cantidad de activos, su estado,
                        depreciación, asignaciones y solicitudes pendientes.
                        <br><br>
                    </p>
                    <button class="news__button">
                        <a
                            href="Login.php"
                            ><span>ver dashboard</span>
                            <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            >
                            <path d="M5 12h14"></path>
                            <path d="M12 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </button>
                </div>
            </article>

            </div>
    </section>

    <footer>
      <ul>
        <li><a href="Website.php">Inicio</a></li>
        <li><a href="Funcionalidades.php">Funcionalidades</a></li>
        <li><a href="Noticia.php">Noticias</a></li>
        <li class="active"><a href="Nosotros.php">Nosotros</a></li>
    </ul>
      <p style="width: 100%; text-align: center">
        © 2026 TrackIT. Todos los derechos reservados.
      </p>
    </footer>
</body>

</html>