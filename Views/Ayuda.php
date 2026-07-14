<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Ayuda - TrackIT System</title>
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

<section class="news-section__container">
    <div class="timeline-header">
        <br><br>
        <h1>Manual de Usuario - TrackIT System</h1>
        <p>
            Aprende a utilizar las principales funcionalidades de nuestra plataforma de
            gestión de activos tecnológicos.
        </p>
    </div>

    <div class="news-items__container">

        <article class="news__item">
    
    <div class="news__image out-text">
        <img
            src="../Views/Styles/img/GUIA.jpg"
            alt="Logo de TrackIT System"
            style="max-width:300px;"
        />
    </div>

    <div class="news_information">
        <h2>Bienvenido a TrackIT System</h2>

        <p>
            TrackIT System es una plataforma CMDB (Configuration Management Database)
            diseñada para centralizar la administración de activos tecnológicos de una
            organización. Nuestro sistema permite gestionar equipos de cómputo,
            software, licencias, dispositivos de red y colaboradores desde un único
            lugar.

            <br><br>

            A través de la plataforma podrá:

            <br><br>

            ✔ Registrar activos tecnológicos.<br>
            ✔ Asignar equipos a colaboradores.<br>
            ✔ Generar reportes y estadísticas.<br>
            ✔ Gestionar solicitudes de equipos y software.<br>
            ✔ Consultar información mediante códigos QR.<br>
            ✔ Mantener un historial y trazabilidad de los activos.
        </p>

        <button class="news__button">
            <a href="Website.php">
                <span>Ir al Inicio</span>
            </a>
        </button>
    </div>

</article>

        <!-- Inventario -->
        <article class="news__item">
            <div class="news_information">
                <h2>Administración de Inventario</h2>

                <p>
                    El módulo de inventario permite registrar y administrar todos los activos
                    tecnológicos de la organización.

                    <br><br>

                    Para agregar un nuevo equipo:

                    <br><br>

                    1. Ingrese al módulo de Inventario.<br>
                    2. Presione el botón "Nuevo Equipo".<br>
                    3. Complete la información solicitada.<br>
                    4. Agregue las imágenes del activo.<br>
                    5. Guarde el registro.
                </p>

                <button class="news__button">
                    <a href="Login.php">
                        <span>Gestionar Inventario</span>
                    </a>
                </button>
            </div>
        </article>

        <!-- Colaboradores -->
        <article class="news__item">
            <div class="news_information">
                <h2>Gestión de Colaboradores</h2>

                <p>
                    Desde este módulo podrá registrar y administrar la información de los
                    colaboradores de la empresa.

                    <br><br>

                    Cada colaborador puede tener asignados:

                    <br><br>

                    ✔ Equipos de cómputo.<br>
                    ✔ Teléfonos.<br>
                    ✔ Licencias de software.<br>
                    ✔ Dispositivos de red.

                    <br><br>

                    El sistema mantiene un historial de asignaciones, devoluciones y cambios
                    de ubicación.
                </p>

                <button class="news__button">
                    <a href="Login.php">
                        <span>Gestionar Colaboradores</span>
                    </a>
                </button>
            </div>
        </article>

        <!-- Solicitudes -->
        <article class="news__item">
            <div class="news_information">
                <h2>Solicitudes de Equipos y Software</h2>

                <p>
                    Los colaboradores pueden enviar solicitudes de nuevos equipos o licencias
                    de software directamente desde la plataforma.

                    <br><br>

                    Cada solicitud puede encontrarse en los siguientes estados:

                    <br><br>

                    🟡 En Espera<br>
                    🔵 En Trámite<br>
                    🟢 Aprobada<br>
                    🔴 Rechazada

                    <br><br>

                    Esta funcionalidad ayuda a la organización a planificar presupuestos y
                    necesidades tecnológicas futuras.
                </p>

                <button class="news__button">
                    <a href="Login.php">
                        <span>Crear Solicitud</span>
                    </a>
                </button>
            </div>
        </article>

        <!-- Reportes -->
        <article class="news__item">

    <div class="news_information">
        <h2>Reportes y Estadísticas</h2>

        <p>
            El sistema incorpora dashboards y reportes dinámicos que permiten obtener
            información sobre el estado de la infraestructura tecnológica.

            <br><br>

            Entre los reportes disponibles se encuentran:

            <br><br>

            ✔ Equipos por categoría.<br>
            ✔ Equipos asignados.<br>
            ✔ Activos en inventario.<br>
            ✔ Equipos en descarte.<br>
            ✔ Activos próximos a depreciarse.<br>
            ✔ Exportación de información a Excel.

            <br><br>

            Estos reportes facilitan la toma de decisiones y la planificación
            tecnológica de la organización.
        </p>

        <button class="news__button">
            <a href="Login.php">
                <span>Ver Reportes</span>
            </a>
        </button>
    </div>

    <div class="news__image out-text">
        <img
            src="../Views/Styles/img/Ayuda1.jpeg"
            alt="Reportes y Estadísticas"
        />
    </div>

</article>

    </div>
</section>

<footer>
    <ul>
        <li><a href="Website.php">Inicio</a></li>
        <li><a href="Funcionalidades.php">Funcionalidades</a></li>
        <li><a href="Noticias.php">Noticias</a></li>
        <li><a href="Nosotros.php">Nosotros</a></li>
    </ul>

    <p style="width:100%; text-align:center">
        © 2026 TrackIT System. Todos los derechos reservados.
    </p>
</footer>

</body>
</html>