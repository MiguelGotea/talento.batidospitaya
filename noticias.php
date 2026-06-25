<?php
// noticias.php - Página "Noticias"
$page_title = "Noticias y Novedades - Batidos Pitaya Nicaragua";
$page_description = "Mantente al día con las últimas noticias, aperturas de sucursales y eventos de Batidos Pitaya Nicaragua.";
$page_keywords = "noticias batidos pitaya, eventos batidos pitaya, novedades batidos pitaya";
$page_canonical = "noticias.php";
$active_tab = "noticias";

include 'layout_talento/header.php';

/* 
================================================================================
GUÍA DE INTEGRACIÓN CON BASE DE DATOS (FUTURA EXPANSIÓN)
================================================================================
Si en el futuro deseas que esta sección cargue dinámicamente desde la base de datos
del ERP, puedes seguir estos pasos:

1. Asegúrate de incluir la conexión en el core si no se ha cargado previamente:
   require_once 'core/database/conexion.php';

2. Crea una consulta SQL para traer las últimas noticias. Por ejemplo:
   $stmt = $conn->prepare("SELECT * FROM noticias_talento WHERE estado = 'publicado' ORDER BY fecha_publicacion DESC");
   $stmt->execute();
   $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);

3. Reemplaza el bloque HTML de abajo con un bucle foreach en PHP:
   <?php foreach ($noticias as $item): ?>
       <article class="noticia-card">
           <div class="noticia-img-placeholder">
               <?php if (!empty($item['imagen'])): ?>
                   <img src="uploads/noticias/<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['titulo']); ?>">
               <?php else: ?>
                   <div class="noticia-img-default">
                       <i class="bi bi-newspaper"></i>
                   </div>
               <?php endif; ?>
               <span class="noticia-badge"><?php echo htmlspecialchars($item['categoria']); ?></span>
           </div>
           <div class="noticia-content">
               <span class="noticia-date">
                   <i class="bi bi-calendar3"></i> 
                   <?php echo date('d M, Y', strtotime($item['fecha_publicacion'])); ?>
               </span>
               <h3 class="noticia-title"><?php echo htmlspecialchars($item['titulo']); ?></h3>
               <p class="noticia-excerpt"><?php echo htmlspecialchars($item['resumen']); ?></p>
           </div>
       </article>
   <?php endforeach; ?>
================================================================================
*/
?>

<!-- ==================== SECCIÓN: NOTICIAS ==================== -->
<div id="section-noticias" class="tab-section-content active-tab">
    <section class="noticias-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <span class="nosotros-subtitle-brand">Novedades y Comunidad</span>
                <h2 class="section-title-custom">Últimas Noticias</h2>
                <p class="section-desc-custom">
                    Descubre las últimas novedades, eventos de bienestar y expansiones de Batidos Pitaya en Nicaragua.
                </p>
            </div>

            <div class="noticias-grid">
                <!-- Noticia 1: Sucursal Estelí -->
                <article class="noticia-card">
                    <div class="noticia-img-placeholder">
                        <div class="noticia-img-gradient" style="background: linear-gradient(135deg, #1e5249 0%, #308375 100%);">
                            <i class="bi bi-shop-window"></i>
                        </div>
                        <span class="noticia-badge bg-primary">Expansión</span>
                    </div>
                    <div class="noticia-content">
                        <span class="noticia-date">
                            <i class="bi bi-calendar3"></i> 20 de Junio, 2026
                        </span>
                        <h3 class="noticia-title">Nueva Apertura: Sucursal Estelí</h3>
                        <p class="noticia-excerpt">
                            ¡Llevamos nuestra energía natural al norte! Nos alegra anunciar la apertura de nuestra nueva sucursal en el corazón de Estelí. Ven a disfrutar de la mejor fruta y la Experiencia WOW.
                        </p>
                    </div>
                </article>

                <!-- Noticia 2: Carrera Run 2026 -->
                <article class="noticia-card">
                    <div class="noticia-img-placeholder">
                        <div class="noticia-img-gradient" style="background: linear-gradient(135deg, #ff826e 0%, #ff523b 100%);">
                            <i class="bi bi-trophy"></i>
                        </div>
                        <span class="noticia-badge bg-warning text-dark">Bienestar</span>
                    </div>
                    <div class="noticia-content">
                        <span class="noticia-date">
                            <i class="bi bi-calendar3"></i> 15 de Mayo, 2026
                        </span>
                        <h3 class="noticia-title">Carrera Pitaya Run 2026</h3>
                        <p class="noticia-excerpt">
                            Nos preparamos para nuestra carrera anual de 5K y 10K. Únete a nuestro equipo y clientes en esta gran jornada de salud, deporte y sana diversión familiar. ¡Inscripciones abiertas pronto!
                        </p>
                    </div>
                </article>

                <!-- Noticia 3: Nuevos Sabores -->
                <article class="noticia-card">
                    <div class="noticia-img-placeholder">
                        <div class="noticia-img-gradient" style="background: linear-gradient(135deg, #a3cb38 0%, #1289a7 100%);">
                            <i class="bi bi-cup-straw"></i>
                        </div>
                        <span class="noticia-badge bg-info text-white">Lanzamiento</span>
                    </div>
                    <div class="noticia-content">
                        <span class="noticia-date">
                            <i class="bi bi-calendar3"></i> 08 de Abril, 2026
                        </span>
                        <h3 class="noticia-title">Lanzamiento: Línea de Invierno</h3>
                        <p class="noticia-excerpt">
                            Inicia la época de lluvias y con ella nuevas cosechas. Descubre nuestras mezclas exclusivas de temporada con frutas locales seleccionadas bajo altos estándares de calidad. ¡Frescura asegurada!
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>
</div>

<?php include 'layout_talento/footer.php'; ?>
