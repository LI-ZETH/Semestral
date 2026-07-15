<?php
use App\Core\Auth;

$actionUrl = Auth::check()
    ? base_url('panel')
    : base_url('login');
?>
<section class="features-section__container">
    <div class="timeline-header">
        <span class="public-eyebrow">Capacidades principales</span>
        <h1>Funcionalidades destacadas de TrackiT System</h1>
        <p>
            Descubre cómo la aplicación transforma la gestión de
            equipos, usuarios, licencias y procesos tecnológicos.
        </p>
    </div>

    <div class="features-items__container">
        <article class="feature__item">
            <div class="feature__image" aria-hidden="true">🔐</div>
            <div class="feature_information">
                <h2>Autenticación segura y gestión de usuarios</h2>
                <p>
                    TrackiT protege la información mediante contraseñas
                    con hash, bloqueo después de intentos fallidos,
                    historial de acceso y permisos diferenciados para
                    administradores, técnicos y colaboradores.
                </p>
                <a class="news__button" href="<?= e($actionUrl) ?>">
                    Acceder al sistema
                </a>
            </div>
        </article>

        <article class="feature__item">
            <div class="feature__image" aria-hidden="true">🖥️</div>
            <div class="feature_information">
                <h2>Gestión inteligente de activos tecnológicos</h2>
                <p>
                    Registra hardware, software, dispositivos de red,
                    telefonía y licencias. Cada copia conserva código,
                    serie, costo, vida útil, estado, ubicación, imágenes
                    y una ficha identificada mediante QR.
                </p>
                <a class="news__button" href="<?= e($actionUrl) ?>">
                    Gestionar inventario
                </a>
            </div>
        </article>

        <article class="feature__item">
            <div class="feature__image" aria-hidden="true">👥</div>
            <div class="feature_information">
                <h2>Colaboradores, asignaciones y solicitudes</h2>
                <p>
                    Asocia equipos y licencias con colaboradores,
                    conserva el historial de entregas y devoluciones y
                    permite solicitar nuevos recursos o reportar fallas
                    sobre los activos bajo custodia.
                </p>
                <a class="news__button" href="<?= e($actionUrl) ?>">
                    Abrir colaboración
                </a>
            </div>
        </article>

        <article class="feature__item">
            <div class="feature__image" aria-hidden="true">📊</div>
            <div class="feature_information">
                <h2>Reportes, depreciación y auditoría</h2>
                <p>
                    Consulta métricas, inventario dinámico, necesidades
                    presupuestarias, accesos, movimientos y activos
                    próximos al fin de su vida útil. La bitácora conserva
                    integridad criptográfica y firma RSA.
                </p>
                <a class="news__button" href="<?= e($actionUrl) ?>">
                    Ver el panel
                </a>
            </div>
        </article>
    </div>
</section>
