<section class="news-section__container">
    <div class="timeline-header">
        <span class="public-eyebrow">Actualidad TrackiT</span>
        <h1>Noticias del proyecto</h1>
        <p>
            Conoce las funciones que convierten a TrackiT en una
            plataforma integral de administración tecnológica.
        </p>
    </div>

    <div class="news-items__container">
        <article class="news__item news__item--featured">
            <div class="news__image out-text">
                <img
                    src="<?= e(asset_url('assets/img/public/Noticia1.png')) ?>"
                    alt="Presentación de TrackiT System"
                >
            </div>

            <div class="news_information">
                <span class="news-date">Julio de 2026</span>
                <h2>
                    TrackiT cambia la forma de gestionar los equipos
                    tecnológicos
                </h2>
                <p>
                    La plataforma centraliza el registro de equipos,
                    software, licencias, dispositivos de red y telefonía.
                    Incorpora códigos QR, depreciación, asignaciones,
                    reparaciones, seguridad criptográfica y trazabilidad
                    de cada movimiento.
                </p>
                <a class="news__button" href="<?= e(base_url()) ?>">
                    Ver TrackiT
                </a>
            </div>
        </article>

        <div class="news-grid-secondary">
            <article class="news__item vertical">
                <div class="news__image vertical">
                    <img
                        src="<?= e(asset_url('assets/img/public/Noticia2.jpeg')) ?>"
                        alt="Transformación digital de una empresa"
                    >
                </div>
                <div class="news_information vertical">
                    <span class="news-date">Julio de 2026</span>
                    <h2>Un cambio radical para la gestión empresarial</h2>
                    <p>
                        La información tecnológica deja de estar dispersa
                        y pasa a formar parte de un inventario verificable.
                    </p>
                </div>
            </article>

            <article class="news__item vertical">
                <div class="news__image vertical">
                    <img
                        src="<?= e(asset_url('assets/img/public/Noticia3.png')) ?>"
                        alt="Automatización de activos tecnológicos"
                    >
                </div>
                <div class="news_information vertical">
                    <span class="news-date">Julio de 2026</span>
                    <h2>Automatización continua de activos</h2>
                    <p>
                        Estados, custodios, movimientos y reportes se
                        actualizan como parte de los flujos del sistema.
                    </p>
                </div>
            </article>
        </div>

        <article class="news__item">
            <div class="news_information">
                <span class="news-date">Proyecto final</span>
                <h2>Innovación en la gestión de activos tecnológicos</h2>
                <p>
                    TrackiT mejora la organización, la seguridad y la
                    trazabilidad de la infraestructura tecnológica. La
                    aplicación ayuda a reducir pérdidas, planificar
                    presupuestos y fundamentar decisiones con información
                    actualizada.
                </p>
                <a
                    class="news__button"
                    href="<?= e(base_url('funcionalidades')) ?>"
                >
                    Conocer funcionalidades
                </a>
            </div>

            <div class="news__image out-text">
                <img
                    src="<?= e(asset_url('assets/img/public/Ayuda1.jpeg')) ?>"
                    alt="Panel de análisis y reportes"
                >
            </div>
        </article>

        <article class="video-promo-card">
            <div>
                <span class="public-eyebrow">Demostración</span>
                <h2>Descubre el funcionamiento de TrackiT</h2>
                <p>
                    El video del proyecto presenta el inventario, las
                    asignaciones, los códigos QR, las solicitudes, las
                    licencias y la trazabilidad de los activos.
                </p>
            </div>

            <a
                class="download-button primary-button"
                href="https://youtu.be/JGosaTGJQt8?si=aU3KEJ2FfXNxs4xR"
                target="_blank"
                rel="noopener noreferrer"
            >
                Ver demostración
            </a>
        </article>
    </div>
</section>
