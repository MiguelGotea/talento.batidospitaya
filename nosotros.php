<?php
// nosotros.php - Página "Sobre Nosotros"
$page_title = "Sobre Nosotros - Batidos Pitaya Nicaragua";
$page_description = "Conoce la historia, valores y el propósito de Batidos Pitaya Nicaragua. Impulsamos energía natural y la Experiencia WOW.";
$page_keywords = "sobre nosotros batidos pitaya, historia batidos pitaya, valores batidos pitaya, proposito batidos pitaya";
$page_canonical = "nosotros.php";
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
                        En Batidos Pitaya creemos que llevar un estilo de vida saludable puede ser sencillo y delicioso. Elaboramos batidos de pura fruta e ingredientes naturales para acompañar a las personas con opciones prácticas, frescas y llenas de energía.
                    </p>

                    <p class="nosotros-paragraph">
                        Nos mueve el compromiso de crear experiencias memorables, actuando con integridad, innovación y un fuerte espíritu de trabajo en equipo. Cada interacción es una oportunidad para sorprender y generar un impacto positivo.
                    </p>

                    <p class="nosotros-paragraph">
                        Hoy seguimos creciendo con una visión clara: convertirnos en el referente de batidos de pura fruta en Centroamérica, impulsados por el talento de nuestra gente y la pasión por hacer las cosas bien.
                    </p>
                </div>

                <!-- Columna Derecha: Valores Corporativos y Propósito -->
                <div class="nosotros-col-valores">
                    <h3 class="valores-title">Nuestros Valores</h3>
                    <div class="valores-grid mb-4">
                        <!-- Valor 1 -->
                        <div class="valor-card">
                            <div class="valor-header">
                                <span class="valor-icon"><i class="bi bi-stars"></i></span>
                                <h4 class="valor-title-name">Factor WOW</h4>
                            </div>
                            <p class="valor-desc">En cada interacción, superando expectativas y creando momentos memorables.</p>
                        </div>
                        <!-- Valor 2 -->
                        <div class="valor-card">
                            <div class="valor-header">
                                <span class="valor-icon"><i class="bi bi-shield-check"></i></span>
                                <h4 class="valor-title-name">Integridad</h4>
                            </div>
                            <p class="valor-desc">En cada acción, guiados por la honestidad, la transparencia y la coherencia.</p>
                        </div>
                        <!-- Valor 3 -->
                        <div class="valor-card">
                            <div class="valor-header">
                                <span class="valor-icon"><i class="bi bi-people-fill"></i></span>
                                <h4 class="valor-title-name">Todos Ganamos</h4>
                            </div>
                            <p class="valor-desc">Sumando esfuerzos para que el crecimiento y el éxito sean de todos.</p>
                        </div>
                    </div>

                    <!-- Nuevo Apartado: Nuestro Propósito -->
                    <div class="proposito-container mt-4">
                        <h3 class="valores-title">Nuestro Propósito</h3>
                        <div class="proposito-card">
                            <div class="proposito-header">
                                <span class="proposito-icon"><i class="bi bi-heart-pulse-fill"></i></span>
                                <h4 class="proposito-title-name">Impulsar Bienestar y Felicidad</h4>
                            </div>
                            <p class="proposito-desc">
                                Inspirar hábitos saludables a través de la frescura de la fruta natural y crear momentos WOW que alegren el día de cada cliente, colaborador y comunidad en Nicaragua.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas de Impacto (Indicadores Destacados) -->
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

<?php include 'layout_talento/footer.php'; ?>
