<?php
// noticia_detalle.php - Detalle público de una noticia con galería de fotos
// Recibe: ?id=ID (ID del registro en noticias_talento)

$noticia_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($noticia_id <= 0) {
    header('Location: noticias.php');
    exit();
}

// Cargar la conexión
require_once 'core/database/conexion.php';

$noticia = null;
try {
    // 1. Obtener los detalles de la noticia
    $stmt = $conn->prepare("SELECT id, titulo, resumen, contenido, imagen_principal, categoria, fecha_publicacion, autor FROM noticias_talento WHERE id = :id AND estado = 'publicado' LIMIT 1");
    $stmt->bindValue(':id', $noticia_id, PDO::PARAM_INT);
    $stmt->execute();
    $noticia = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Manejo de error
}

if (!$noticia) {
    header('Location: noticias.php');
    exit();
}

// 2. Obtener las fotos asociadas a la galería
$fotos = [];
try {
    $stmtFotos = $conn->prepare("SELECT id, ruta_foto, descripcion FROM noticias_fotos_talento WHERE noticia_id = :noticia_id ORDER BY orden ASC, id ASC");
    $stmtFotos->bindValue(':noticia_id', $noticia_id, PDO::PARAM_INT);
    $stmtFotos->execute();
    $fotos = $stmtFotos->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $fotos = [];
}

// Helpers locales para fecha y visuales
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

$fechaPublicacion = formatearFechaEspanol($noticia['fecha_publicacion']);

// SEO
$page_title = htmlspecialchars($noticia['titulo']) . " — Noticias Batidos Pitaya Nicaragua";
$page_description = htmlspecialchars(mb_substr(strip_tags($noticia['resumen']), 0, 155));
$page_keywords = "noticia batidos pitaya, " . htmlspecialchars($noticia['categoria']) . ", eventos pitaya nicaragua";
$page_canonical = "noticia_detalle.php?id=" . $noticia_id;
$active_tab = "noticias";

include 'layout_talento/header.php';
?>

<!-- ==================== DETALLE DE NOTICIA ==================== -->
<div id="section-noticia-detalle" class="tab-section-content active-tab">
    <section class="noticia-detalle-section py-5">
        <div class="container">
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb vacante-breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="noticias.php"><i class="bi bi-newspaper"></i> Noticias</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= htmlspecialchars(mb_substr($noticia['titulo'], 0, 40)) ?><?= mb_strlen($noticia['titulo']) > 40 ? '...' : '' ?>
                    </li>
                </ol>
            </nav>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <article class="noticia-detalle-card">
                        
                        <!-- Encabezado -->
                        <header class="noticia-detalle-header text-center">
                            <span class="noticia-badge bg-primary mb-2 d-inline-block">
                                <?= htmlspecialchars($noticia['categoria']) ?>
                            </span>
                            <h1 class="noticia-detalle-titulo-h1 mb-3">
                                <?= htmlspecialchars($noticia['titulo']) ?>
                            </h1>
                            <div class="noticia-detalle-meta d-flex justify-content-center gap-3 flex-wrap">
                                <span class="meta-item">
                                    <i class="bi bi-calendar3"></i>
                                    <?= $fechaPublicacion ?>
                                </span>
                                <?php if (!empty($noticia['autor'])): ?>
                                <span class="meta-item">
                                    <i class="bi bi-person-fill"></i>
                                    Por <?= htmlspecialchars($noticia['autor']) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </header>

                        <!-- Portada / Banner principal -->
                        <div class="noticia-detalle-portada-wrapper my-4">
                            <?php 
                            $portadaUrl = !empty($noticia['imagen_principal']) ? 'uploads/noticias/' . htmlspecialchars($noticia['imagen_principal']) : null;
                            if ($portadaUrl): 
                            ?>
                                <img src="<?= $portadaUrl ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>" class="noticia-detalle-portada-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                <div class="noticia-detalle-portada-fallback" style="background: <?= obtenerGradientePorCategoria($noticia['categoria']) ?>; display: none;">
                                    <i class="bi <?= obtenerIconoPorCategoria($noticia['categoria']) ?>"></i>
                                </div>
                            <?php else: ?>
                                <div class="noticia-detalle-portada-fallback" style="background: <?= obtenerGradientePorCategoria($noticia['categoria']) ?>;">
                                    <i class="bi <?= obtenerIconoPorCategoria($noticia['categoria']) ?>"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Contenido -->
                        <section class="noticia-detalle-contenido py-3">
                            <?= $noticia['contenido'] ?>
                        </section>

                        <!-- Galería de Fotos Relacionadas -->
                        <?php if (!empty($fotos)): ?>
                            <footer class="noticia-detalle-galeria mt-5 pt-4 border-top">
                                <h3 class="galeria-titulo mb-4">
                                    <i class="bi bi-images"></i> Galería de Fotos
                                </h3>
                                <div class="row g-3">
                                    <?php foreach ($fotos as $f): 
                                        $fUrl = 'uploads/noticias/galeria/' . htmlspecialchars($f['ruta_foto']);
                                        $desc = htmlspecialchars($f['descripcion'] ?? '');
                                    ?>
                                        <div class="col-6 col-sm-4 col-md-3">
                                            <div class="galeria-item-card" onclick="verImagenLightbox('<?= $fUrl ?>', '<?= $desc ?>')">
                                                <img src="<?= $fUrl ?>" alt="<?= $desc ?: 'Imagen de galería' ?>" class="galeria-thumb-img" loading="lazy">
                                                <div class="galeria-item-hover">
                                                    <i class="bi bi-zoom-in"></i>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </footer>
                        <?php endif; ?>

                        <!-- Botón de regreso -->
                        <div class="text-center mt-5">
                            <a href="noticias.php" class="btn-leer-mas">
                                <i class="bi bi-arrow-left"></i> Volver a Noticias
                            </a>
                        </div>

                    </article>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
function verImagenLightbox(url, descripcion) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            imageUrl: url,
            imageAlt: descripcion || 'Foto de galería',
            title: descripcion || '',
            confirmButtonText: 'Cerrar',
            confirmButtonColor: '#51B8AC',
            background: '#ffffff',
            customClass: {
                popup: 'lightbox-swal-popup',
                image: 'lightbox-swal-image'
            }
        });
    } else {
        // Fallback básico si SweetAlert falla
        window.open(url, '_blank');
    }
}
</script>

<?php include 'layout_talento/footer.php'; ?>
