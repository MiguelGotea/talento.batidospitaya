<?php
// beneficios.php - Página de Beneficios Corporativos
$page_title = "Beneficios de Trabajar con Nosotros - Batidos Pitaya Nicaragua";
$page_description = "Descubre los beneficios, el plan de carrera, clima laboral y ventajas de unirte al equipo de Batidos Pitaya Nicaragua.";
$page_keywords = "beneficios batidos pitaya, trabajar en batidos pitaya, clima laboral batidos pitaya, plan de carrera nicaragua";
$page_canonical = "beneficios";
$active_tab = "beneficios";

include 'layout_talento/header.php';
?>

<!-- ==================== SECCIÓN: BENEFICIOS ==================== -->
<div id="section-beneficios" class="tab-section-content active-tab">
    
    <!-- Hero Banner Premium -->
    <section class="beneficios-hero py-5">
        <div class="container text-center py-4">
            <span class="nosotros-subtitle-brand"><?php echo htmlspecialchars(obtener_config('hero_beneficios_sub', '¿Por qué unirte a nosotros?')); ?></span>
            <h2 class="section-title-custom"><?php echo htmlspecialchars(obtener_config('hero_beneficios_titulo', 'La Experiencia Batidos Pitaya')); ?></h2>
            <p class="section-desc-custom mx-auto" style="max-width: 700px;">
                <?php echo htmlspecialchars(obtener_config('hero_beneficios_desc', 'No solo ofrecemos un empleo...')); ?>
            </p>
        </div>
    </section>

    <!-- Grid de Beneficios Clave -->
    <section class="beneficios-grid-section pb-5">
        <div class="container">
            <div class="row g-4">
                <?php
                try {
                    $stmtBeneficios = $conn->query("SELECT icono, color_tema, titulo, descripcion FROM talento_beneficios WHERE activo = 1 ORDER BY orden ASC, id ASC");
                    while ($ben = $stmtBeneficios->fetch()) {
                        ?>
                        <div class="col-md-4">
                            <div class="beneficio-card">
                                <div class="beneficio-icon-box bg-<?php echo htmlspecialchars($ben['color_tema']); ?>-soft">
                                    <i class="bi <?php echo htmlspecialchars($ben['icono']); ?> text-<?php echo htmlspecialchars($ben['color_tema']); ?>"></i>
                                </div>
                                <h3 class="beneficio-title-h3"><?php echo htmlspecialchars($ben['titulo']); ?></h3>
                                <p class="beneficio-text">
                                    <?php echo htmlspecialchars($ben['descripcion']); ?>
                                </p>
                            </div>
                        </div>
                        <?php
                    }
                } catch (Exception $e) {
                    error_log("Error al cargar beneficios: " . $e->getMessage());
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Sección de Cultura de Bienestar -->
    <section class="cultura-bienestar-section py-5 bg-light-brand">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="cultura-badge mb-3">Nuestra Cultura</div>
                    <h2 class="section-title-custom text-start mb-4"><?php echo htmlspecialchars(obtener_config('cultura_titulo', '¿Qué significa ser parte del equipo?')); ?></h2>
                    <p class="lead mb-4">
                        <?php echo htmlspecialchars(obtener_config('cultura_subtitulo', 'En Batidos Pitaya...')); ?>
                    </p>
                    
                    <div class="cultura-list">
                        <?php
                        try {
                            $stmtCultura = $conn->query("SELECT titulo, descripcion FROM talento_cultura WHERE activo = 1 ORDER BY orden ASC, id ASC");
                            while ($cult = $stmtCultura->fetch()) {
                                ?>
                                <div class="cultura-item d-flex gap-3 mb-4">
                                    <div class="cultura-item-icon">
                                        <i class="bi bi-check-circle-fill text-teal"></i>
                                    </div>
                                    <div>
                                        <h4 class="h5 fw-bold text-header mb-1"><?php echo htmlspecialchars($cult['titulo']); ?></h4>
                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($cult['descripcion']); ?></p>
                                    </div>
                                </div>
                                <?php
                            }
                        } catch (Exception $e) {
                            error_log("Error al cargar ítems de cultura: " . $e->getMessage());
                        }
                        ?>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="cultura-banner-card">
                        <div class="cultura-banner-content text-center text-white py-5 px-4">
                            <i class="bi bi-quote fs-1 d-block mb-3"></i>
                            <blockquote class="fs-4 fw-light italic mb-4">
                                "<?php echo htmlspecialchars(obtener_config('cultura_cita', 'El mejor beneficio...')); ?>"
                            </blockquote>
                            <span class="d-block fw-bold tracking-wide text-uppercase">Equipo Batidos Pitaya</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Llamado a la Acción (CTA) -->
    <section class="beneficios-cta-section py-5 text-center">
        <div class="container py-4">
            <h2 class="section-title-custom mb-3">¿Listo para dar el siguiente paso?</h2>
            <p class="section-desc-custom mx-auto mb-4" style="max-width: 600px;">
                Revisa nuestras vacantes disponibles hoy mismo y postúlate para iniciar tu camino hacia el éxito.
            </p>
            <a href="index.php#vacantes" class="btn-leer-mas" style="padding: 0.8rem 2.5rem; font-size: 1.1rem;">
                Ver Oportunidades <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </section>

</div>

<?php include 'layout_talento/footer.php'; ?>
