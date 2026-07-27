<?php
// noticia_detalle.php - Detalle público de una noticia con galería de fotos
// Recibe: ?id=ID (ID del registro en noticias_talento)

$noticia_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($noticia_id <= 0) {
    header('Location: /noticias');
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
    header('Location: /noticias');
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
$page_canonical = "noticia/" . $noticia_id;
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
                        <a href="/noticias"><i class="bi bi-newspaper"></i> Noticias</a>
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

                        <!-- Portada / Banner principal o Carrusel de Fotos -->
                        <?php 
                        $portadaUrl = !empty($noticia['imagen_principal']) ? '/uploads/noticias/' . htmlspecialchars($noticia['imagen_principal']) : null;
                        $fotosAdicionales = !empty($fotos);
                        ?>
                        
                        <?php if ($fotosAdicionales): 
                            // Compilamos todas las imágenes del carrusel: portada primero, luego galería
                            $imagenesCarrusel = [];
                            if ($portadaUrl) {
                                $imagenesCarrusel[] = [
                                    'url' => $portadaUrl,
                                    'desc' => $noticia['titulo']
                                ];
                            }
                            foreach ($fotos as $f) {
                                $imagenesCarrusel[] = [
                                    'url' => '/uploads/noticias/galeria/' . htmlspecialchars($f['ruta_foto']),
                                    'desc' => htmlspecialchars($f['descripcion'] ?? '')
                                ];
                            }
                        ?>
                            <div id="carruselNoticiaDetalle" class="carousel slide noticia-detalle-portada-wrapper my-4" data-bs-ride="carousel" data-bs-interval="5000">
                                <!-- Indicadores -->
                                <?php if (count($imagenesCarrusel) > 1): ?>
                                    <div class="carousel-indicators">
                                        <?php foreach ($imagenesCarrusel as $index => $img): ?>
                                            <button type="button" data-bs-target="#carruselNoticiaDetalle" data-bs-slide-to="<?= $index ?>" <?= $index === 0 ? 'class="active" aria-current="true"' : '' ?> aria-label="Slide <?= $index + 1 ?>"></button>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Slides -->
                                <div class="carousel-inner h-100">
                                    <?php foreach ($imagenesCarrusel as $index => $img): 
                                        $safeDesc = htmlspecialchars(str_replace("'", "\'", $img['desc'] ?? ''), ENT_QUOTES);
                                        $isActive  = ($index === 0);
                                    ?>
                                        <div class="carousel-item h-100 <?= $isActive ? 'active' : '' ?>" onclick="verImagenLightbox('<?= $img['url'] ?>', '<?= $safeDesc ?>')">
                                            <?php if ($isActive): ?>
                                                <!-- Primera imagen: carga inmediata -->
                                                <img src="<?= $img['url'] ?>" alt="<?= $safeDesc ?>" class="noticia-detalle-portada-img">
                                            <?php else: ?>
                                                <!-- Imágenes siguientes: lazy load -->
                                                <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                                     data-src="<?= $img['url'] ?>"
                                                     alt="<?= $safeDesc ?>"
                                                     class="noticia-detalle-portada-img lazy-carousel-img">
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Controles -->
                                <?php if (count($imagenesCarrusel) > 1): ?>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carruselNoticiaDetalle" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Anterior</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carruselNoticiaDetalle" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Siguiente</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <!-- Portada estática tradicional (sin fotos adicionales) -->
                            <div class="noticia-detalle-portada-wrapper my-4">
                                <?php if ($portadaUrl): 
                                    $safeTitle = htmlspecialchars(str_replace("'", "\'", $noticia['titulo'] ?? ''), ENT_QUOTES);
                                ?>
                                    <img src="<?= $portadaUrl ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>" class="noticia-detalle-portada-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';" onclick="verImagenLightbox('<?= $portadaUrl ?>', '<?= $safeTitle ?>')">
                                    <div class="noticia-detalle-portada-fallback" style="background: <?= obtenerGradientePorCategoria($noticia['categoria']) ?>; display: none;">
                                        <i class="bi <?= obtenerIconoPorCategoria($noticia['categoria']) ?>"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="noticia-detalle-portada-fallback" style="background: <?= obtenerGradientePorCategoria($noticia['categoria']) ?>;">
                                        <i class="bi <?= obtenerIconoPorCategoria($noticia['categoria']) ?>"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Contenido (con saltos de línea nl2br) -->
                        <section class="noticia-detalle-contenido py-3">
                            <?= nl2br($noticia['contenido']) ?>
                        </section>


                        <!-- Botones de acción -->
                        <div class="text-center mt-5 d-flex justify-content-center gap-3 flex-wrap">
                            <a href="/noticias" class="btn-leer-mas">
                                <i class="bi bi-arrow-left"></i> Volver a Noticias
                            </a>
                            <button id="btnCompartirNoticia" class="btn-compartir" onclick="copiarEnlaceNoticia()" title="Copiar enlace">
                                <i class="bi bi-share" id="iconoCompartir"></i>
                                <span id="textoCompartir">Copiar enlace</span>
                            </button>
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
        window.open(url, '_blank');
    }
}

// Lazy loading para imágenes del carrusel
(function () {
    const carrusel = document.getElementById('carruselNoticiaDetalle');
    if (!carrusel) return;

    carrusel.addEventListener('slide.bs.carousel', function (e) {
        const slidingTo = e.relatedTarget;
        if (!slidingTo) return;
        const lazyImg = slidingTo.querySelector('img.lazy-carousel-img');
        if (lazyImg && lazyImg.dataset.src) {
            lazyImg.src = lazyImg.dataset.src;
            lazyImg.removeAttribute('data-src');
            lazyImg.classList.remove('lazy-carousel-img');
        }
    });
})();

// Copiar enlace al portapapeles
function copiarEnlaceNoticia() {
    const btn    = document.getElementById('btnCompartirNoticia');
    const icono  = document.getElementById('iconoCompartir');
    const texto  = document.getElementById('textoCompartir');

    navigator.clipboard.writeText(window.location.href).then(function () {
        // Feedback visual
        icono.className  = 'bi bi-check-lg';
        texto.textContent = '¡Enlace copiado!';
        btn.classList.add('btn-compartir--copiado');

        setTimeout(function () {
            icono.className  = 'bi bi-share';
            texto.textContent = 'Copiar enlace';
            btn.classList.remove('btn-compartir--copiado');
        }, 2000);
    }).catch(function () {
        // Fallback para navegadores sin soporte (muy raro hoy en día)
        prompt('Copia el enlace:', window.location.href);
    });
}
</script>

<?php include 'layout_talento/footer.php'; ?>
