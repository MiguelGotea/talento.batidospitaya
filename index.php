<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Básico -->
    <title>Empleos Batidos Pitaya Nicaragua - Vacantes</title>
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

        function gtag() {
            dataLayer.push(arguments);
        }
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

    <!-- ==================== BARRA DE NAVEGACIÓN (STICKY NAVBAR) ==================== -->
    <!-- Permite la navegación rápida y persistente entre la información de la empresa y las plazas -->
    <nav class="navbar-pitaya">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <img src="assets/img/logo.png" alt="Batidos Pitaya - Empleos" class="navbar-brand-img">
            </a>
            <div class="navbar-nav-tabs">
                <button class="nav-tab-btn" id="btn-tab-nosotros">
                    <!-- <i class="bi bi-info-circle"></i> --> Sobre Nosotros
                </button>
                <button class="nav-tab-btn active" id="btn-tab-unete">
                    <!-- <i class="bi bi-briefcase"></i> --> Únete al Equipo
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container position-relative">
            <div class="hero-content text-center">
                <!-- Logo oculto temporalmente en el hero — descomentar si se vuelve a necesitar
                <img src="assets/img/logo.png" alt="Batidos Pitaya - Empleos Nicaragua" class="hero-logo">
                -->
                <h1 class="hero-title-seo">Empleos en Batidos Pitaya Nicaragua - Vacantes Disponibles</h1>
                <!-- Subtítulo oculto temporalmente — descomentar si se vuelve a necesitar
                <p class="hero-subtitle">
                    Únete a nuestro equipo. Encuentra oportunidades laborales en Managua, Granada, Masaya y más ciudades
                    de Nicaragua.
                </p>
                -->
            </div>
        </div>
    </section>

    <!-- ==================== CONTENEDOR TABS: ÚNETE AL EQUIPO ==================== -->
    <!-- Contiene toda la lógica original de filtros y visualización de vacantes cargadas por AJAX -->
    <div id="section-unete-equipo" class="tab-section-content active-tab">
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
    </div>

    <!-- ==================== CONTENEDOR TABS: SOBRE NOSOTROS ==================== -->
    <div id="section-sobre-nosotros" class="tab-section-content">
        <section class="sobre-nosotros-section">
            <div class="container">
                <!-- Imagen Grupal Centrada en la Parte Superior -->
                <!-- La imagen se carga de forma diferida: solo descarga cuando el usuario abre esta pestaña -->
                <div class="nosotros-top-group-img">
                    <div class="nosotros-group-container">
                        <!-- Skeleton visible mientras la imagen carga -->
                        <div class="nosotros-group-skeleton" id="grupo-svg-skeleton"></div>
                        <!-- data-src: la imagen NO se descarga hasta que el JS la active -->
                        <img id="grupo-svg"
                             data-src="assets/img/grupo_pitaya.svg"
                             alt="Líderes de Tienda Batidos Pitaya"
                             class="nosotros-group-svg"
                             style="display:none;">
                        <div class="nosotros-group-badge" id="grupo-svg-badge" style="display:none;">
                            <i class="bi bi-stars"></i>
                            <span>Líderes que hacen posible la Experiencia WOW</span>
                        </div>
                    </div>
                </div>

                <div class="nosotros-grid">
                    <!-- Columna Izquierda: Información Corporativa -->
                    <div class="nosotros-col-text">
                        <span class="nosotros-subtitle-brand">¿Quiénes Somos?</span>
                        <h2 class="nosotros-title-main">Batidos Pitaya Nicaragua</h2>
                        
                        <p class="nosotros-paragraph">
                            Somos la marca líder de batidos 100% de pura fruta en Nicaragua, dedicados a ofrecer una alternativa saludable, deliciosa y llena de vitalidad. Desde nuestros inicios, nuestro propósito ha sido claro: <strong>impulsar la energía natural, fomentar hábitos de vida saludables y positivos, y brindar una experiencia WOW</strong> a cada persona que visita nuestras sucursales.
                        </p>
                        
                        <p class="nosotros-paragraph">
                            Nos sentimos orgullosos de contar con una amplia presencia a nivel nacional en las ciudades de Managua, Granada, Masaya, León, Estelí, Matagalpa y Rivas. En cada uno de nuestros puntos de venta, nuestro equipo se compromete a elaborar bebidas frescas de calidad insuperable con un estándar de servicio excepcional.
                        </p>
                    </div>

                    <!-- Columna Derecha: Valores Corporativos -->
                    <div class="nosotros-col-valores">
                        <h3 class="valores-title">Nuestros Valores</h3>
                        <div class="valores-grid">
                            <!-- Valor 1 -->
                            <div class="valor-card">
                                <div class="valor-header">
                                    <span class="valor-icon"><i class="bi bi-lightning-charge-fill"></i></span>
                                    <h4 class="valor-title-name">Energía Positiva</h4>
                                </div>
                                <p class="valor-desc">Transmitimos vitalidad, alegría y actitud positiva en todo lo que hacemos.</p>
                            </div>
                            <!-- Valor 2 -->
                            <div class="valor-card">
                                <div class="valor-header">
                                    <span class="valor-icon"><i class="bi bi-heart-pulse-fill"></i></span>
                                    <h4 class="valor-title-name">Hábitos Saludables</h4>
                                </div>
                                <p class="valor-desc">Promovemos un estilo de vida activo y balanceado mediante nutrición real.</p>
                            </div>
                            <!-- Valor 3 -->
                            <div class="valor-card">
                                <div class="valor-header">
                                    <span class="valor-icon"><i class="bi bi-emoji-laughing-fill"></i></span>
                                    <h4 class="valor-title-name">Experiencia WOW</h4>
                                </div>
                                <p class="valor-desc">Superamos las expectativas de nuestros clientes con un servicio y atención memorables.</p>
                            </div>
                            <!-- Valor 4 -->
                            <div class="valor-card">
                                <div class="valor-header">
                                    <span class="valor-icon"><i class="bi bi-award-fill"></i></span>
                                    <h4 class="valor-title-name">Calidad y Frescura</h4>
                                </div>
                                <p class="valor-desc">Utilizamos frutas nacionales de la más alta calidad, cosechadas con esmero.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Estadísticas de Impacto (Indicadores Destacados) -->
                <!-- data-target: número final | data-suffix: símbolo que se agrega al final (ej: +, %) -->
                <div class="corp-stats-grid">
                    <!-- Fundación -->
                    <div class="corp-stat-card">
                        <div class="corp-stat-icon-wrapper">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                        <div class="corp-stat-number" id="stat-fundacion" data-target="2016" data-suffix="">0</div>
                        <div class="corp-stat-label">Fundado en</div>
                    </div>
                    <!-- Sucursales -->
                    <div class="corp-stat-card">
                        <div class="corp-stat-icon-wrapper">
                            <i class="bi bi-shop"></i>
                        </div>
                        <div class="corp-stat-number" id="stat-sucursales" data-target="14" data-suffix="">0</div>
                        <div class="corp-stat-label">Sucursales</div>
                    </div>
                    <!-- Fruta Natural -->
                    <div class="corp-stat-card">
                        <div class="corp-stat-icon-wrapper">
                            <i class="bi bi-droplet-fill"></i>
                        </div>
                        <div class="corp-stat-number" id="stat-fruta" data-target="100" data-suffix="%">0%</div>
                        <div class="corp-stat-label">Fruta Natural</div>
                    </div>
                    <!-- Colaboradores -->
                    <div class="corp-stat-card">
                        <div class="corp-stat-icon-wrapper">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="corp-stat-number" id="stat-colaboradores" data-target="100" data-suffix="+">0+</div>
                        <div class="corp-stat-label">Colaboradores</div>
                    </div>
                </div>
            </div>
        </section>
    </div>

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
                    <h5 class="footer-title">Contacto</h5>
                    <ul class="footer-links">
                        <li><i class="bi bi-envelope"></i> <span class="email-protected" data-user="seleccion"
                                data-domain="batidospitaya.com"></span></li>
                        <li><i class="bi bi-telephone"></i> +505 8590 8544</li>
                        <!-- URL de búsqueda general en Google Maps — muestra todas las sucursales y sugiere la más cercana al usuario -->
                        <li><i class="bi bi-geo-alt"></i> <a href="https://www.google.com/maps/search/Batidos+Pitaya+Nicaragua/"
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
                        <a href="https://www.linkedin.com/company/batidospitaya/posts/?feedView=all" target="_blank"
                            rel="noopener noreferrer" class="social-link" title="LinkedIn"><i
                                class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <!-- Año removido intencionalmente del copyright -->
                <p>&copy; Batidos Pitaya. Todos los derechos reservados.</p>
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

    <!-- Botón Flotante de Aplicar -->
    <div class="apply-float" id="applyFloat">
        <button class="apply-float-btn" id="btn-apply-float">
            <i class="bi bi-briefcase-fill"></i>
            <span>Aplicar</span>
        </button>
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