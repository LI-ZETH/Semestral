<?php
use App\Core\Auth;

$applicationUrl = Auth::check()
    ? base_url('panel')
    : base_url('login');
?>
<section class="news-section__container manual-section">
    <div class="timeline-header">
        <span class="public-eyebrow">Centro de ayuda</span>
        <h1>Manual de usuario de TrackiT System</h1>
        <p>
            Aprende a utilizar las principales funcionalidades de la
            plataforma de gestión de activos tecnológicos.
        </p>
    </div>

    <div class="news-items__container">
        <article class="news__item news__item--featured">
            <div class="news__image out-text">
                <img
                    src="<?= e(asset_url('assets/img/public/GUIA.jpg')) ?>"
                    alt="Guía de TrackiT System"
                >
            </div>

            <div class="news_information">
                <h2>Bienvenido a TrackiT System</h2>
                <p>
                    TrackiT es una plataforma CMDB diseñada para
                    centralizar la administración de hardware, software,
                    licencias, dispositivos de red y colaboradores.
                </p>

                <ul class="manual-check-list">
                    <li>Registrar y consultar activos tecnológicos.</li>
                    <li>Asignar equipos y licencias a colaboradores.</li>
                    <li>Gestionar solicitudes y reparaciones.</li>
                    <li>Generar reportes y exportaciones.</li>
                    <li>Consultar fichas mediante códigos QR.</li>
                    <li>Mantener historial, auditoría y trazabilidad.</li>
                </ul>

                <a class="news__button" href="<?= e($applicationUrl) ?>">
                    Abrir la aplicación
                </a>
            </div>
        </article>

        <article class="manual-card">
            <span>01</span>
            <h2>Administración del inventario</h2>
            <p>
                El administrador crea categorías, subcategorías y
                productos. Después registra cada copia individual con su
                código, serie, costo, estado, ubicación e imágenes.
            </p>
        </article>

        <article class="manual-card">
            <span>02</span>
            <h2>Usuarios y colaboradores</h2>
            <p>
                Las cuentas se organizan por rol. El administrador puede
                registrar, editar, activar, desactivar y desbloquear
                usuarios sin eliminar su historial.
            </p>
        </article>

        <article class="manual-card">
            <span>03</span>
            <h2>Asignaciones y devoluciones</h2>
            <p>
                Una copia disponible puede asignarse a un colaborador y a
                una ubicación. La devolución registra condición, motivo,
                ubicación de recepción y estado posterior.
            </p>
        </article>

        <article class="manual-card">
            <span>04</span>
            <h2>Solicitudes y reparaciones</h2>
            <p>
                Los colaboradores solicitan recursos o reportan fallas.
                El administrador revisa los casos y el técnico documenta
                diagnóstico, trabajo realizado, costo y resultado.
            </p>
        </article>

        <article class="manual-card">
            <span>05</span>
            <h2>Reportes, licencias y auditoría</h2>
            <p>
                El sistema ofrece inventario dinámico, depreciación,
                movimientos, accesos, necesidades presupuestarias,
                licencias y una bitácora firmada para comprobar la
                integridad de las operaciones.
            </p>
        </article>

        <article class="manual-image-card">
            <img
                src="<?= e(asset_url('assets/img/public/Ayuda1.jpeg')) ?>"
                alt="Reportes y estadísticas de TrackiT"
            >
            <div>
                <h2>Consejo para la demostración</h2>
                <p>
                    Realiza el recorrido con una cuenta de cada rol para
                    mostrar cómo cambian los módulos y permisos. También
                    puedes abrir TrackiT desde un celular conectado a la
                    misma red local.
                </p>
            </div>
        </article>
    </div>
</section>
