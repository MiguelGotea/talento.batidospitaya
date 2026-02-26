<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Básico -->
    <title>Empleos Batidos Pitaya Nicaragua - Vacantes 2026</title>
    <meta name="description"
        content="Encuentra empleo en Batidos Pitaya. Vacantes en Managua, Granada, Masaya. Únete a nuestro equipo de energía natural y experiencia WOW.">
    <meta name="keywords"
        content="empleo batidos pitaya, trabajo batidos pitaya nicaragua, vacantes batidos pitaya, empleo managua, trabajo managua, vacantes nicaragua 2026, trabajo pura fruta">
    <meta name="author" content="Batidos Pitaya">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://talento.batidospitaya.com">

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-BEJV259C10"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'G-BEJV259C10');
    </script>

    <!-- Open Graph -->
    <meta property="og:title" content="Trabaja en Batidos Pitaya - Energía Natural y Experiencia WOW">
    <meta property="og:description"
        content="Disfrutá los mejores batidos de pura fruta. En Pitaya impulsamos energía natural, hábitos positivos y experiencia WOW. ¡Únete a nuestro equipo!">
    <meta property="og:image" content="https://talento.batidospitaya.com/assets/img/og-image.jpg">
    <meta property="og:url" content="https://talento.batidospitaya.com">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Batidos Pitaya - Portal de Empleo">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Trabaja en Batidos Pitaya">
    <meta name="twitter:description"
        content="Energía natural, hábitos positivos y experiencia WOW. Plazas disponibles en Nicaragua.">
    <meta name="twitter:image" content="https://talento.batidospitaya.com/assets/img/og-image.jpg">

    <!-- Favicon -->
    <link rel="icon" href="assets/img/favicon.png" type="image/png">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/global.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/stats-modern.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/whatsapp-float.css?v=<?php echo time(); ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container position-relative">
            <!-- Caja de Plazas Vigente -->
            <div class="vigente-box">
                VIGENTE: <?php echo date('d/m/Y'); ?>
            </div>

            <div class="hero-content text-center">
                <img src="assets/img/logo.png" alt="Batidos Pitaya - Empleos Nicaragua" class="hero-logo">
                <h1 class="hero-title-seo">Empleos en Batidos Pitaya Nicaragua - Vacantes Disponibles</h1>
                <p class="hero-subtitle">
                    Únete a nuestro equipo. Encuentra oportunidades laborales en Managua, Granada, Masaya y más ciudades
                    de Nicaragua.
                </p>
            </div>
        </div>
    </section>

    <!-- Filtros y Búsqueda -->
    <section class="filters-section" style="display: none;">
        <div class="container">
            <div class="filters-card">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" id="busqueda" class="form-control"
                                placeholder="Buscar por cargo o ubicación...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="filtroCategoria" class="form-select">
                            <option value="">Todas las categorías</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filtroUbicacion" class="form-select">
                            <option value="">Todas las ubicaciones</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="ordenamiento" class="form-select">
                            <option value="salario" selected>Mejor salario</option>
                            <option value="fecha">Más recientes</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tabs de Categorías -->
    <section class="categories-section">
        <div class="container">

            <div class="category-tabs" id="categoryTabs">
                <button class="category-tab active" data-categoria="">
                    <i class="bi bi-grid-3x3-gap"></i>
                    Todas
                </button>
            </div>

            <!-- Estadísticas (Ocultas/Eliminadas según requerimiento) -->
            <div class="stats-container d-none">
                <div class="stat-card-modern">
                    <div class="stat-icon">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label-small">Encontramos para ti</div>
                        <div class="stat-number-large" id="totalVacantes">0</div>
                        <div class="stat-label-main">Vacantes</div>
                    </div>
                </div>
                <div class="stat-card-modern">
                    <div class="stat-icon">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label-small">Actualizado hace 5 minutos</div>
                        <div class="stat-number-large" id="totalCategorias">0</div>
                        <div class="stat-label-main">Áreas de Trabajo</div>
                    </div>
                </div>
            </div>

            <!-- Enlaces Rápidos (ocultos) -->
            <div class="quick-links-section" style="display: none;">
                <h2 class="quick-links-title">Explora Oportunidades Laborales</h2>
                <div class="quick-links-grid">
                    <a href="#vacantes" class="quick-link-card" data-categoria="Operaciones">
                        <i class="bi bi-gear-fill"></i>
                        <span>Empleos en Operaciones</span>
                    </a>
                    <a href="#vacantes" class="quick-link-card" data-categoria="Ventas">
                        <i class="bi bi-cart-fill"></i>
                        <span>Trabajos en Ventas</span>
                    </a>
                    <a href="#vacantes" class="quick-link-card" data-categoria="Administración">
                        <i class="bi bi-briefcase-fill"></i>
                        <span>Vacantes Administrativas</span>
                    </a>
                    <a href="#vacantes" class="quick-link-card" data-categoria="Logística">
                        <i class="bi bi-truck"></i>
                        <span>Oportunidades en Logística</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Grid de Vacantes -->
    <section class="vacantes-section">
        <div class="container">

            <!-- Loader -->
            <div id="loader" class="loader-container">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>

            <!-- Grid de Cards -->
            <div id="vacantesGrid" class="vacantes-grid">
                <!-- Las cards se cargarán dinámicamente aquí -->
            </div>

            <!-- Sin resultados -->
            <div id="sinResultados" class="sin-resultados" style="display: none;">
                <i class="bi bi-inbox"></i>
                <h3>No se encontraron vacantes</h3>
                <p>Intenta ajustar los filtros de búsqueda</p>
            </div>

            <!-- Paginación -->
            <div class="pagination-container mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0">Mostrar:</label>
                        <select class="form-select form-select-sm" id="registrosPorPagina" style="width: auto;">
                            <option value="12">12</option>
                            <option value="24" selected>24</option>
                            <option value="48">48</option>
                        </select>
                        <span class="mb-0">vacantes</span>
                    </div>
                    <div id="paginacion"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="footer-title">Batidos Pitaya</h5>
                    <p class="footer-text">
                        Energía natural, hábitos positivos y experiencia WOW.
                    </p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="footer-title">Contacto Reclutamiento</h5>
                    <ul class="footer-links">
                        <li><i class="bi bi-envelope"></i> <span class="email-protected" data-user="seleccion"
                                data-domain="batidospitaya.com"></span></li>
                        <li><i class="bi bi-telephone"></i> +505 8409 2477</li>
                        <li><i class="bi bi-geo-alt"></i> <a href="https://maps.app.goo.gl/1dRmhhQJYVoU3BVg6"
                                target="_blank" rel="noopener noreferrer" class="text-white">Ver ubicaciones</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="footer-title">Síguenos</h5>
                    <div class="social-links">
                        <a href="https://www.facebook.com/BatidosPitaya" target="_blank" rel="noopener noreferrer"
                            class="social-link" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://www.instagram.com/batidospitaya/" target="_blank" rel="noopener noreferrer"
                            class="social-link" title="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="https://ni.linkedin.com/company/batidospitaya" target="_blank"
                            rel="noopener noreferrer" class="social-link" title="LinkedIn"><i
                                class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy;
                    <?php echo date('Y'); ?> Batidos Pitaya. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </footer>

    <!-- Modal de Detalle de Plaza -->
    <div class="modal fade" id="modalDetallePlaza" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo">Detalle de la Plaza</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalContenido">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnPostularModal">
                        <i class="bi bi-send"></i> Postular a esta plaza
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón Flotante de WhatsApp -->
    <div class="whatsapp-float" id="whatsappFloat">
        <button class="whatsapp-button" id="whatsappButton">
            <i class="bi bi-whatsapp"></i>
        </button>
        <div class="whatsapp-popup" id="whatsappPopup" style="display: none;">
            <div class="whatsapp-header">
                <img src="assets/img/logo.png" alt="Batidos Pitaya" class="whatsapp-logo">
                <div>
                    <strong>Batidos Pitaya</strong>
                    <p class="mb-0 small">Reclutamiento</p>
                </div>
            </div>
            <div class="whatsapp-body">
                <p class="mb-2">¡Hola! 👋</p>
                <p class="mb-3">¿Tienes preguntas sobre las vacantes? Escríbenos por WhatsApp.</p>
                <textarea class="form-control mb-2" id="whatsappMessage" rows="3"
                    placeholder="Escribe tu mensaje aquí...">Hola, me interesa obtener más información sobre las vacantes disponibles.</textarea>
                <button class="btn btn-success w-100" id="sendWhatsapp">
                    <i class="bi bi-whatsapp"></i> Enviar mensaje
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/main.js?v=<?php echo time(); ?>"></script>

    <!-- Schema.org JobPosting (se generará dinámicamente) -->
    <script type="application/ld+json" id="schemaJobPostings">
    {
        "@context": "https://schema.org/",
        "@type": "Organization",
        "name": "Batidos Pitaya",
        "url": "https://batidospitaya.com",
        "logo": "https://talento.batidospitaya.com/assets/img/logo.png",
        "description": "Disfrutá los mejores batidos de pura fruta. Energía natural, hábitos positivos y experiencia WOW."
    }
    </script>

</body>

</html>