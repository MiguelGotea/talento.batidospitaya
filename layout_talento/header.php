<?php
// layout_talento/header.php
// Plantilla de cabecera compartida para el portal de Talento de Batidos Pitaya
require_once 'core/database/conexion.php';

// Obtener configuraciones globales del portal
$global_config = [];
try {
    $stmtConfig = $conn->query("SELECT clave, valor FROM talento_configuracion");
    while ($rowConfig = $stmtConfig->fetch()) {
        $global_config[$rowConfig['clave']] = $rowConfig['valor'];
    }
} catch (Exception $e) {
    error_log("Error al cargar configuración de talento: " . $e->getMessage());
}

function obtener_config($clave, $default = '') {
    global $global_config;
    return isset($global_config[$clave]) ? $global_config[$clave] : $default;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Dinámico -->
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Empleos Batidos Pitaya Nicaragua - Vacantes'; ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? htmlspecialchars($page_description) : 'Encuentra empleo en Batidos Pitaya. Únete a nuestro equipo de energía natural y experiencia WOW.'; ?>">
    <meta name="keywords" content="<?php echo isset($page_keywords) ? htmlspecialchars($page_keywords) : 'empleo batidos pitaya, trabajo batidos pitaya nicaragua, vacantes batidos pitaya'; ?>">
    <meta name="author" content="Batidos Pitaya">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://talento.batidospitaya.com/<?php echo isset($page_canonical) ? htmlspecialchars($page_canonical) : ''; ?>">

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
    <meta property="og:title" content="<?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Trabaja en Batidos Pitaya'; ?>">
    <meta property="og:description" content="<?php echo isset($page_description) ? htmlspecialchars($page_description) : 'Impulsando energía natural, hábitos positivos y experiencia WOW.'; ?>">
    <meta property="og:image" content="https://talento.batidospitaya.com/assets/img/og-image.jpg">
    <meta property="og:url" content="https://talento.batidospitaya.com/<?php echo isset($page_canonical) ? htmlspecialchars($page_canonical) : ''; ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Batidos Pitaya - Portal de Empleo">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Trabaja en Batidos Pitaya'; ?>">
    <meta name="twitter:description" content="<?php echo isset($page_description) ? htmlspecialchars($page_description) : 'Impulsando energía natural, hábitos positivos y experiencia WOW.'; ?>">
    <meta name="twitter:image" content="https://talento.batidospitaya.com/assets/img/og-image.jpg">

    <!-- Favicon -->
    <link rel="icon" href="assets/img/favicon.png" type="image/png">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/global.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/stats-modern.css?v=<?php echo time(); ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- ===== Personalización Visual Dinámica (desde talento_configuracion) ===== -->
    <?php
    $colorMarca      = htmlspecialchars(obtener_config('color_marca',      '#51B8AC'));
    $colorMarcaHover = htmlspecialchars(obtener_config('color_marca_hover', '#0E544C'));
    $colorHeader     = htmlspecialchars(obtener_config('color_header',     '#0E544C'));
    $colorFooter     = htmlspecialchars(obtener_config('color_footer',     '#0E544C'));
    $colorFondo      = htmlspecialchars(obtener_config('color_fondo',      '#ffffff'));
    $colorTexto      = htmlspecialchars(obtener_config('color_texto',      '#1a1a2e'));
    $imagenFondo     = obtener_config('imagen_fondo', '');
    $fondoOpacidad   = obtener_config('imagen_fondo_opacidad', '0.08');
    $fondoRepetir    = obtener_config('imagen_fondo_repetir',  'no-repeat');
    $fondoSize       = obtener_config('imagen_fondo_size',     'cover');
    ?>
    <style>
        :root {
            --color-principal:   <?= $colorMarca ?>;
            --color-hover:       <?= $colorMarcaHover ?>;
            --color-header:      <?= $colorHeader ?>;
            --color-footer:      <?= $colorFooter ?>;
            --color-fondo-pagina: <?= $colorFondo ?>;
            --color-fondo:        <?= $colorFondo ?>; /* Sincroniza la variable global con la BD */
            --color-texto-base:  <?= $colorTexto ?>;
        }
        html, body {
            overflow-x: hidden;
            width: 100%;
        }
        body {
            background-color: <?= $colorFondo ?>;
            color: <?= $colorTexto ?>;
        }
        <?php if (!empty($imagenFondo)): ?>
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url('<?= htmlspecialchars($imagenFondo) ?>');
            background-size: <?= htmlspecialchars($fondoSize) ?>;
            background-repeat: <?= htmlspecialchars($fondoRepetir) ?>;
            background-position: center;
            opacity: <?= floatval($fondoOpacidad) ?>;
            pointer-events: none;
            z-index: 0;
            will-change: transform;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            transform: translate3d(0,0,0);
            -webkit-transform: translate3d(0,0,0);
        }
        body > * { position: relative; z-index: 1; }
        
        /* Forzar transparencia para permitir ver el fondo de pantalla */
        .sobre-nosotros-section,
        .equipo-section,
        .noticias-section,
        .tab-section-content {
            background-color: transparent !important;
            background: transparent !important;
        }
        <?php endif; ?>
    </style>
</head>

<body>

    <!-- SEO H1 Oculto para motores de búsqueda -->
    <h1 class="visually-hidden">Empleos en Batidos Pitaya Nicaragua - Vacantes Disponibles</h1>

    <!-- ==================== BARRA DE NAVEGACIÓN (STICKY NAVBAR) ==================== -->
    <nav class="navbar-pitaya">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <img src="assets/img/logo.png" alt="Batidos Pitaya - Empleos" class="navbar-brand-img">
            </a>
            
            <!-- Contenedor del menú y acciones -->
            <div class="navbar-menu-container">
                <!-- Envoltorio de pestañas (es un menú flotante en móviles) -->
                <div class="navbar-nav-tabs-wrapper" id="navbarTabsWrapper">
                    <div class="navbar-nav-tabs" id="navbarTabs">
                        <a href="index.php" class="nav-tab-btn <?php echo (isset($active_tab) && $active_tab === 'nosotros') ? 'active' : ''; ?>" id="btn-tab-nosotros">
                            Sobre Nosotros
                        </a>
                        <?php /* Beneficios: oculto temporalmente — descomentar para activar */ ?>
                        <?php if (false): ?>
                        <a href="beneficios.php" class="nav-tab-btn <?php echo (isset($active_tab) && $active_tab === 'beneficios') ? 'active' : ''; ?>" id="btn-tab-beneficios">
                            Beneficios
                        </a>
                        <?php endif; ?>
                        <a href="equipo.php" class="nav-tab-btn <?php echo (isset($active_tab) && $active_tab === 'equipo') ? 'active' : ''; ?>" id="btn-tab-equipo">
                            Nuestro Equipo
                        </a>
                        <a href="unete.php" class="nav-tab-btn <?php echo (isset($active_tab) && $active_tab === 'unete') ? 'active' : ''; ?>" id="btn-tab-unete">
                            Únete al Equipo
                        </a>
                        <?php /* Noticias: oculto temporalmente — descomentar para activar */ ?>
                        <?php if (false): ?>
                        <a href="noticias.php" class="nav-tab-btn <?php echo (isset($active_tab) && $active_tab === 'noticias') ? 'active' : ''; ?>" id="btn-tab-noticias">
                            Noticias
                        </a>
                        <?php endif; ?>
                    </div>
                </div>



                <!-- Botón de Aplicar (se muestra condicionalmente) -->
                <a href="unete.php#vacantes" class="btn-aplicar-header" id="btn-aplicar-header" <?php echo (isset($active_tab) && $active_tab === 'unete') ? 'style="display: none;"' : ''; ?>>
                    <i class="bi bi-briefcase-fill"></i> Aplicar
                </a>

                <!-- Botón toggle hamburguesa para móviles -->
                <button class="navbar-toggle-btn" id="navbarToggleBtn" aria-label="Menú de Navegación">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div>
    </nav>
