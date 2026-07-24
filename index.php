<?php
// index.php - Página "Sobre Nosotros" (Página Principal del Portal de Talento)
$page_title = "Sobre Nosotros - Batidos Pitaya Nicaragua";
$page_description = "Conoce la historia, valores y el propósito de Batidos Pitaya Nicaragua. Impulsamos energía natural y la Experiencia WOW.";
$page_keywords = "sobre nosotros batidos pitaya, historia batidos pitaya, valores batidos pitaya, proposito batidos pitaya";
$page_canonical = "";
$active_tab = "nosotros";

include 'layout_talento/header.php';
?>

<!-- ==================== SECCIÓN: SOBRE NOSOTROS ==================== -->
<div id="section-sobre-nosotros" class="tab-section-content active-tab">
    <section class="sobre-nosotros-section">
        <div class="container">
            <!-- Imagen Grupal Centrada en la Parte Superior -->
            <div class="nosotros-top-group-img">
                <div class="nosotros-group-container">
                    <!-- Skeleton visible mientras la imagen carga -->
                    <div class="nosotros-group-skeleton" id="grupo-svg-skeleton"></div>
                    <!-- data-src: la imagen NO se descarga hasta que el JS la active -->
                    <img id="grupo-svg"
                        data-src="<?php echo htmlspecialchars(obtener_config('imagen_nosotros', 'assets/img/grupo_pitaya.svg')); ?>"
                        alt="<?php echo htmlspecialchars(obtener_config('imagen_nosotros_alt', 'Líderes de Tienda Batidos Pitaya')); ?>"
                        class="nosotros-group-svg"
                        style="display:none;">
                    <div class="nosotros-group-badge" id="grupo-svg-badge" style="display:none;">
                        <i class="bi bi-stars"></i>
                        <span><?php echo htmlspecialchars(obtener_config('imagen_nosotros_badge', 'Líderes que hacen posible la Experiencia WOW')); ?></span>
                    </div>
                </div>
            </div>

            <div class="nosotros-grid">
                <!-- Columna Izquierda: Información Corporativa -->
                <div class="nosotros-col-text">
                    <span class="nosotros-subtitle-brand">¿Quiénes Somos?</span>
                    <h2 class="nosotros-title-main">Batidos Pitaya Nicaragua</h2>

                    <?php
                    // Cargar textos de nosotros
                    $nosotros_textos = [];
                    try {
                        $stmtTextos = $conn->query("SELECT clave, valor FROM talento_textos_nosotros");
                        while ($rowTxt = $stmtTextos->fetch()) {
                            $nosotros_textos[$rowTxt['clave']] = $rowTxt['valor'];
                        }
                    } catch (Exception $e) {
                        error_log("Error al obtener textos de nosotros: " . $e->getMessage());
                    }

                    function obtener_texto_nosotros($clave, $default = '') {
                        global $nosotros_textos;
                        return isset($nosotros_textos[$clave]) ? $nosotros_textos[$clave] : $default;
                    }
                    ?>

                    <p class="nosotros-paragraph">
                        <?php echo obtener_texto_nosotros('parrafo_1', 'En Batidos Pitaya creemos...'); ?>
                    </p>

                    <p class="nosotros-paragraph">
                        <?php echo obtener_texto_nosotros('parrafo_2', 'Nos mueve el compromiso...'); ?>
                    </p>

                    <p class="nosotros-paragraph">
                        <?php echo obtener_texto_nosotros('parrafo_3', 'Hoy seguimos creciendo...'); ?>
                    </p>
                </div>

                <!-- Columna Derecha: Valores Corporativos y Propósito -->
                <div class="nosotros-col-valores">
                    <h3 class="valores-title">Nuestros Valores</h3>
                    <div class="valores-grid mb-4">
                        <?php
                        try {
                            $stmtValores = $conn->query("SELECT icono, titulo, descripcion FROM talento_valores WHERE activo = 1 ORDER BY orden ASC, id ASC");
                            while ($val = $stmtValores->fetch()) {
                                ?>
                                <div class="valor-card">
                                    <div class="valor-header">
                                        <span class="valor-icon"><i class="bi <?php echo htmlspecialchars($val['icono']); ?>"></i></span>
                                        <h4 class="valor-title-name"><?php echo htmlspecialchars($val['titulo']); ?></h4>
                                    </div>
                                    <p class="valor-desc"><?php echo htmlspecialchars($val['descripcion']); ?></p>
                                </div>
                                <?php
                            }
                        } catch (Exception $e) {
                            error_log("Error al cargar valores: " . $e->getMessage());
                        }
                        ?>
                    </div>

                    <!-- Nuevo Apartado: Nuestro Propósito -->
                    <div class="proposito-container mt-4">
                        <h3 class="valores-title">Nuestro Propósito</h3>
                        <div class="proposito-card">
                            <div class="proposito-header">
                                <span class="proposito-icon"><i class="bi bi-heart-pulse-fill"></i></span>
                                <h4 class="proposito-title-name"><?php echo htmlspecialchars(obtener_texto_nosotros('proposito_titulo', 'Impulsar Bienestar y Felicidad')); ?></h4>
                            </div>
                            <p class="proposito-desc">
                                <?php echo obtener_texto_nosotros('proposito_desc', 'Inspirar hábitos saludables...'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas de Impacto (Indicadores Destacados con Imagen Lateral) -->
            <div class="corp-stats-wrapper">
                <!-- Columna Izquierda: Imagen Ilustrativa -->
                <div class="corp-stats-image-col">
                    <img src="<?php echo htmlspecialchars(obtener_config('imagen_stats_nosotros', 'assets/img/stats_nosotros.png')); ?>" 
                         alt="Batido Pitaya Natural" 
                         class="corp-stats-side-img">
                </div>

                <!-- Columna Derecha: Indicadores Grid -->
                <div class="corp-stats-grid">
                    <?php
                    try {
                        $stmtStats = $conn->query("SELECT id, icono, valor_numero, sufijo, etiqueta, color_fondo FROM talento_estadisticas WHERE activo = 1 ORDER BY orden ASC, id ASC");
                        $allStats = $stmtStats->fetchAll();
                        
                        $featuredStat = null;
                        $normalStats = [];
                        
                        // El primer stat en orden es siempre el destacado (fila completa).
                        // Los demás van en el subgrid de 4 en una fila.
                        foreach ($allStats as $index => $stat) {
                            if ($index === 0) {
                                $featuredStat = $stat;
                            } else {
                                $normalStats[] = $stat;
                            }
                        }
                        
                        // 1. Renderizar la destacada arriba
                        if ($featuredStat) {
                            $stat = $featuredStat;
                            $isSvg = (strpos($stat['icono'], 'svg:') === 0);
                            $colorFondo = !empty($stat['color_fondo']) ? $stat['color_fondo'] : '';
                            $cardStyle = '';
                            $cardClass = 'corp-stat-card corp-stat-featured';
                            if ($colorFondo) {
                                $cardStyle = "background:{$colorFondo}; --shadow-color:{$colorFondo};";
                                $cardClass .= ' has-custom-bg';
                            }
                            ?>
                            <div class="<?= $cardClass ?>"<?= $cardStyle ? " style=\"{$cardStyle}\"" : '' ?>>
                                <div class="corp-stat-icon-wrapper">
                                    <?php if ($stat['icono'] === 'img:pitaya_icon'): ?>
                                        <img src="assets/img/pitaya_icon.png" alt="Fruta Natural" style="width: 2.4rem; height: 2.4rem; object-fit: contain; vertical-align: middle; filter: drop-shadow(0 1px 3px rgba(0,0,0,0.15));">
                                    <?php elseif ($isSvg && $stat['icono'] === 'svg:pitaya'): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.8rem; height: 1.8rem; vertical-align: middle;">
                                            <path d="M12 2.5C7.2 2.5 3.5 6.5 3.5 11.5c0 4 2.5 7.5 6.5 8.5v1.5c0 .6.4 1 1 1s1-.4 1-1v-1.5c4-1 6.5-4.5 6.5-8.5 0-5-3.7-9-8.5-9zm3 8.5c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm-6 0c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm3 4c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm0-6c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm0 3c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1z" />
                                        </svg>
                                    <?php else: ?>
                                        <i class="bi <?php echo htmlspecialchars($stat['icono']); ?>"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="corp-stat-featured-text">
                                    <div class="corp-stat-label"><?php echo htmlspecialchars($stat['etiqueta']); ?></div>
                                    <div class="corp-stat-number" id="stat-<?php echo $stat['id']; ?>" data-target="<?php echo htmlspecialchars($stat['valor_numero']); ?>" data-suffix="<?php echo htmlspecialchars($stat['sufijo']); ?>">0</div>
                                </div>
                            </div>
                            <?php
                        }
                        
                        // 2. Renderizar las normales en el subgrid
                        if (!empty($normalStats)) {
                            ?>
                            <div class="corp-stats-subgrid">
                                <?php
                                foreach ($normalStats as $stat) {
                                    $isSvg = (strpos($stat['icono'], 'svg:') === 0);
                                    $colorFondo = !empty($stat['color_fondo']) ? $stat['color_fondo'] : '';
                                    $cardStyle = '';
                                    $cardClass = 'corp-stat-card';
                                    if ($colorFondo) {
                                        $cardStyle = "background:{$colorFondo}; --shadow-color:{$colorFondo};";
                                        $cardClass .= ' has-custom-bg';
                                    }
                                    ?>
                                    <div class="<?= $cardClass ?>"<?= $cardStyle ? " style=\"{$cardStyle}\"" : '' ?>>
                                        <div class="corp-stat-icon-wrapper">
                                            <?php if ($stat['icono'] === 'img:pitaya_icon'): ?>
                                                <img src="assets/img/pitaya_icon.png" alt="Fruta Natural" style="width: 2.4rem; height: 2.4rem; object-fit: contain; vertical-align: middle; filter: drop-shadow(0 1px 3px rgba(0,0,0,0.15));">
                                            <?php elseif ($isSvg && $stat['icono'] === 'svg:pitaya'): ?>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 1.8rem; height: 1.8rem; vertical-align: middle;">
                                                    <path d="M12 2.5C7.2 2.5 3.5 6.5 3.5 11.5c0 4 2.5 7.5 6.5 8.5v1.5c0 .6.4 1 1 1s1-.4 1-1v-1.5c4-1 6.5-4.5 6.5-8.5 0-5-3.7-9-8.5-9zm3 8.5c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm-6 0c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm3 4c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm0-6c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm0 3c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1z" />
                                                </svg>
                                            <?php else: ?>
                                                <i class="bi <?php echo htmlspecialchars($stat['icono']); ?>"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="corp-stat-number" id="stat-<?php echo $stat['id']; ?>" data-target="<?php echo htmlspecialchars($stat['valor_numero']); ?>" data-suffix="<?php echo htmlspecialchars($stat['sufijo']); ?>">0</div>
                                        <div class="corp-stat-label"><?php echo htmlspecialchars($stat['etiqueta']); ?></div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                        }
                    } catch (Exception $e) {
                        error_log("Error al cargar estadísticas: " . $e->getMessage());
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'layout_talento/footer.php'; ?>
