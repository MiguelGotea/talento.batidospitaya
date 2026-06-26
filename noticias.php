<?php
// noticias.php - Página "Noticias" (Dinámica)
$page_title = "Noticias y Novedades - Batidos Pitaya Nicaragua";
$page_description = "Mantente al día con las últimas noticias, aperturas de sucursales y eventos de Batidos Pitaya Nicaragua.";
$page_keywords = "noticias batidos pitaya, eventos batidos pitaya, novedades batidos pitaya, expansion pitaya";
$page_canonical = "noticias.php";
$active_tab = "noticias";

// Cargar la conexión
require_once 'core/database/conexion.php';

// Obtener las noticias publicadas
$noticias = [];
try {
    $stmt = $conn->prepare("SELECT id, titulo, resumen, imagen_principal, categoria, fecha_publicacion FROM noticias_talento WHERE estado = 'publicado' ORDER BY fecha_publicacion DESC, id DESC");
    $stmt->execute();
    $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Si la tabla no está creada aún, se maneja vacío
    $noticias = [];
}

// Helpers para cuando no hay imagen de portada (generar visual premium por categoría)
function obtenerGradientePorCategoria(string $cat): string {
    switch (mb_strtolower(trim($cat))) {
        case 'expansión':
        case 'expansion':
            return 'linear-gradient(135deg, #0E544C 0%, #51B8AC 100%)';
        case 'bienestar':
        case 'salud':
            return 'linear-gradient(135deg, #FF6B00 0%, #FFA800 100%)';
        case 'lanzamiento':
        case 'producto':
            return 'linear-gradient(135deg, #218838 0%, #74C043 100%)';
        default:
            return 'linear-gradient(135deg, #51B8AC 0%, #3d9a8f 100%)';
    }
}

function obtenerIconoPorCategoria(string $cat): string {
    switch (mb_strtolower(trim($cat))) {
        case 'expansión':
        case 'expansion':
            return 'bi-shop-window';
        case 'bienestar':
        case 'salud':
            return 'bi-heart-pulse-fill';
        case 'lanzamiento':
        case 'producto':
            return 'bi-cup-straw';
        default:
            return 'bi-newspaper';
    }
}

function formatearFechaEspanol(string $fecha): string {
    if (empty($fecha)) return '';
    $timestamp = strtotime($fecha);
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    $dia = date('d', $timestamp);
    $mesN = intval(date('m', $timestamp));
    $anio = date('Y', $timestamp);
    
    return "{$dia} de {$meses[$mesN]}, {$anio}";
}

include 'layout_talento/header.php';
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

            <?php if (!empty($noticias)): ?>
                <div class="noticias-grid">
                    <?php foreach ($noticias as $item): 
                        $fotoUrl = !empty($item['imagen_principal']) ? 'uploads/noticias/' . htmlspecialchars($item['imagen_principal']) : null;
                    ?>
                        <article class="noticia-card">
                            <div class="noticia-img-placeholder">
                                <?php if ($fotoUrl): ?>
                                    <img src="<?= $fotoUrl ?>" alt="<?= htmlspecialchars($item['titulo']) ?>" class="noticia-img" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <div class="noticia-img-gradient" style="background: <?= obtenerGradientePorCategoria($item['categoria']) ?>; display: none;">
                                        <i class="bi <?= obtenerIconoPorCategoria($item['categoria']) ?>"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="noticia-img-gradient" style="background: <?= obtenerGradientePorCategoria($item['categoria']) ?>;">
                                        <i class="bi <?= obtenerIconoPorCategoria($item['categoria']) ?>"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="noticia-badge bg-primary"><?= htmlspecialchars($item['categoria']) ?></span>
                            </div>
                            <div class="noticia-content">
                                <span class="noticia-date">
                                    <i class="bi bi-calendar3"></i> 
                                    <?= formatearFechaEspanol($item['fecha_publicacion']) ?>
                                </span>
                                <h3 class="noticia-title"><?= htmlspecialchars($item['titulo']) ?></h3>
                                <p class="noticia-excerpt"><?= htmlspecialchars($item['resumen']) ?></p>
                                
                                <div class="noticia-actions mt-3">
                                    <a href="noticia_detalle.php?id=<?= $item['id'] ?>" class="btn-leer-mas-noticia">
                                        Leer Más <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-journal-x fs-1 text-muted mb-3 d-block"></i>
                    <h3 class="text-muted">No hay noticias publicadas en este momento</h3>
                    <p class="text-muted">Próximamente compartiremos las novedades de la comunidad Batidos Pitaya.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include 'layout_talento/footer.php'; ?>
