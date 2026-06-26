<?php
// equipo.php - Página "Nuestro Equipo" con carrusel de colaboradores
$page_title = "Nuestro Equipo - Batidos Pitaya Nicaragua";
$page_description = "Conoce al equipo que hace posible la Experiencia WOW en Batidos Pitaya. Personas apasionadas por el bienestar y la fruta natural en Nicaragua.";
$page_keywords = "equipo batidos pitaya, colaboradores batidos pitaya, personal batidos pitaya, trabajo en equipo nicaragua";
$page_canonical = "equipo.php";
$active_tab = "equipo";

// Cargar colaboradores desde la base de datos
require_once 'core/database/conexion.php';
$colaboradores = [];
try {
    $stmt = $conn->prepare(
        "SELECT id, nombre, cargo, departamento, testimonio, foto
         FROM colaboradores_talento
         WHERE activo = 1
         ORDER BY orden ASC, id ASC"
    );
    $stmt->execute();
    $colaboradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Si la tabla aún no existe (antes de aplicar el SQL), se muestra vacío
    $colaboradores = [];
}

// Helper: iniciales del nombre para el avatar placeholder
function obtenerIniciales(string $nombre): string {
    $partes = array_filter(explode(' ', trim($nombre)));
    $iniciales = '';
    foreach (array_slice($partes, 0, 2) as $p) {
        $iniciales .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    return $iniciales ?: '?';
}

// Colores para avatares placeholder (rotativo por índice)
$avatarColores = [
    ['bg' => '#51B8AC', 'txt' => '#ffffff'],
    ['bg' => '#0E544C', 'txt' => '#ffffff'],
    ['bg' => '#FF6B00', 'txt' => '#ffffff'],
    ['bg' => '#218838', 'txt' => '#ffffff'],
    ['bg' => '#3d9a8f', 'txt' => '#ffffff'],
    ['bg' => '#854d0e', 'txt' => '#ffffff'],
];

// Agrupar en slides de 3 colaboradores (para desktop)
$slidesDesktop = array_chunk($colaboradores, 3);
// Slides individuales para móvil
$slidesMobile  = $colaboradores;

include 'layout_talento/header.php';
?>

<!-- ==================== SECCIÓN: NUESTRO EQUIPO ==================== -->
<div id="section-nuestro-equipo" class="tab-section-content active-tab">

    <!-- Bloque superior: departamentos (existente) -->
    <section class="equipo-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <span class="nosotros-subtitle-brand">El Alma de la Empresa</span>
                <h2 class="section-title-custom">Nuestro Equipo</h2>
                <p class="section-desc-custom">
                    Personas apasionadas por inspirar hábitos positivos, promover el bienestar y brindar la mejor Experiencia WOW a toda Nicaragua.
                </p>
            </div>

            <div class="equipo-grid">
                <!-- Operaciones y Tiendas -->
                <div class="equipo-card">
                    <div class="equipo-card-inner">
                        <div class="equipo-icon-wrapper">
                            <i class="bi bi-shop"></i>
                        </div>
                        <h3 class="equipo-card-title">Operaciones y Tiendas</h3>
                        <p class="equipo-card-desc">
                            El frente de nuestra experiencia. Nuestros Líderes de Tienda y Vendedores que elaboran cada batido con fruta 100% natural, garantizando limpieza, frescura y la Experiencia WOW en cada visita.
                        </p>
                    </div>
                </div>

                <!-- Logística y Suministros -->
                <div class="equipo-card">
                    <div class="equipo-card-inner">
                        <div class="equipo-icon-wrapper">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h3 class="equipo-card-title">Logística y Suministros</h3>
                        <p class="equipo-card-desc">
                            La columna vertebral de la frescura. Encargados de la selección de frutas en el campo y de su transporte diario y óptimo hacia cada una de nuestras sucursales a nivel nacional.
                        </p>
                    </div>
                </div>

                <!-- Administración y Finanzas -->
                <div class="equipo-card">
                    <div class="equipo-card-inner">
                        <div class="equipo-icon-wrapper">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <h3 class="equipo-card-title">Administración y Finanzas</h3>
                        <p class="equipo-card-desc">
                            Estrategia y solidez. Profesionales responsables de la planificación financiera, gestión de recursos y soporte operativo para que todo nuestro sistema marche con armonía y eficiencia.
                        </p>
                    </div>
                </div>

                <!-- Talento y Cultura -->
                <div class="equipo-card">
                    <div class="equipo-card-inner">
                        <div class="equipo-icon-wrapper">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h3 class="equipo-card-title">Talento y Cultura</h3>
                        <p class="equipo-card-desc">
                            Atrayendo y potenciando a los mejores. Se enfocan en el desarrollo integral de nuestro personal, planes de carrera, capacitaciones y la preservación de nuestro gran clima laboral.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== CARRUSEL DE COLABORADORES ==================== -->
    <?php if (!empty($colaboradores)): ?>
    <section class="colaboradores-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title-custom">Nuestro Talento</h2>
                <p class="section-desc-custom">
                    Estas son las personas que dan vida a Batidos Pitaya todos los días. Conocelos de cerca.
                </p>
            </div>

            <!-- CARRUSEL DESKTOP: 3 tarjetas por slide -->
            <div id="carruselColaboradoresDesktop"
                 class="carousel slide colaboradores-carousel d-none d-md-block"
                 data-bs-ride="carousel"
                 data-bs-interval="5000">

                <div class="carousel-inner">
                    <?php foreach ($slidesDesktop as $slideIdx => $grupo): ?>
                    <div class="carousel-item <?= $slideIdx === 0 ? 'active' : '' ?>">
                        <div class="row g-4 justify-content-center">
                            <?php foreach ($grupo as $colIdx => $col):
                                $colorIdx = ($slideIdx * 3 + $colIdx) % count($avatarColores);
                                $color = $avatarColores[$colorIdx];
                                $iniciales = obtenerIniciales($col['nombre']);
                                $fotoUrl = !empty($col['foto'])
                                    ? htmlspecialchars('uploads/equipo/' . $col['foto'])
                                    : null;
                            ?>
                            <div class="col-md-4">
                                <div class="colaborador-card">
                                    <div class="colaborador-foto-wrapper">
                                        <?php if ($fotoUrl): ?>
                                            <img src="<?= $fotoUrl ?>"
                                                 alt="Foto de <?= htmlspecialchars($col['nombre']) ?>"
                                                 class="colaborador-foto"
                                                 loading="lazy"
                                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                            <div class="colaborador-avatar-placeholder"
                                                 style="background:<?= $color['bg'] ?>;color:<?= $color['txt'] ?>;display:none;">
                                                <?= $iniciales ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="colaborador-avatar-placeholder"
                                                 style="background:<?= $color['bg'] ?>;color:<?= $color['txt'] ?>;">
                                                <?= $iniciales ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="colaborador-info">
                                        <h4 class="colaborador-nombre"><?= htmlspecialchars($col['nombre']) ?></h4>
                                        <span class="colaborador-cargo"><?= htmlspecialchars($col['cargo']) ?></span>
                                        <?php if (!empty($col['testimonio'])): ?>
                                        <blockquote class="colaborador-testimonio">
                                            <i class="bi bi-quote colaborador-quote-icon"></i>
                                            <?= htmlspecialchars($col['testimonio']) ?>
                                        </blockquote>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Controles -->
                <?php if (count($slidesDesktop) > 1): ?>
                <button class="carousel-control-prev colaboradores-prev"
                        type="button"
                        data-bs-target="#carruselColaboradoresDesktop"
                        data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next colaboradores-next"
                        type="button"
                        data-bs-target="#carruselColaboradoresDesktop"
                        data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>

                <!-- Indicadores -->
                <div class="carousel-indicators colaboradores-indicators">
                    <?php foreach ($slidesDesktop as $i => $g): ?>
                    <button type="button"
                            data-bs-target="#carruselColaboradoresDesktop"
                            data-bs-slide-to="<?= $i ?>"
                            <?= $i === 0 ? 'class="active" aria-current="true"' : '' ?>
                            aria-label="Slide <?= $i + 1 ?>">
                    </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- CARRUSEL MÓVIL: 1 tarjeta por slide -->
            <div id="carruselColaboradoresMobile"
                 class="carousel slide colaboradores-carousel d-block d-md-none"
                 data-bs-ride="carousel"
                 data-bs-interval="4500">

                <div class="carousel-inner">
                    <?php foreach ($slidesMobile as $mIdx => $col):
                        $colorIdx = $mIdx % count($avatarColores);
                        $color = $avatarColores[$colorIdx];
                        $iniciales = obtenerIniciales($col['nombre']);
                        $fotoUrl = !empty($col['foto'])
                            ? htmlspecialchars('uploads/equipo/' . $col['foto'])
                            : null;
                    ?>
                    <div class="carousel-item <?= $mIdx === 0 ? 'active' : '' ?>">
                        <div class="colaborador-card">
                            <div class="colaborador-foto-wrapper">
                                <?php if ($fotoUrl): ?>
                                    <img src="<?= $fotoUrl ?>"
                                         alt="Foto de <?= htmlspecialchars($col['nombre']) ?>"
                                         class="colaborador-foto"
                                         loading="lazy"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <div class="colaborador-avatar-placeholder"
                                         style="background:<?= $color['bg'] ?>;color:<?= $color['txt'] ?>;display:none;">
                                        <?= $iniciales ?>
                                    </div>
                                <?php else: ?>
                                    <div class="colaborador-avatar-placeholder"
                                         style="background:<?= $color['bg'] ?>;color:<?= $color['txt'] ?>;">
                                        <?= $iniciales ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="colaborador-info">
                                <h4 class="colaborador-nombre"><?= htmlspecialchars($col['nombre']) ?></h4>
                                <span class="colaborador-cargo"><?= htmlspecialchars($col['cargo']) ?></span>
                                <?php if (!empty($col['testimonio'])): ?>
                                <blockquote class="colaborador-testimonio">
                                    <i class="bi bi-quote colaborador-quote-icon"></i>
                                    <?= htmlspecialchars($col['testimonio']) ?>
                                </blockquote>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Controles móvil -->
                <?php if (count($slidesMobile) > 1): ?>
                <button class="carousel-control-prev colaboradores-prev"
                        type="button"
                        data-bs-target="#carruselColaboradoresMobile"
                        data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next colaboradores-next"
                        type="button"
                        data-bs-target="#carruselColaboradoresMobile"
                        data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>

                <div class="carousel-indicators colaboradores-indicators">
                    <?php foreach ($slidesMobile as $mi => $c): ?>
                    <button type="button"
                            data-bs-target="#carruselColaboradoresMobile"
                            data-bs-slide-to="<?= $mi ?>"
                            <?= $mi === 0 ? 'class="active" aria-current="true"' : '' ?>
                            aria-label="Slide <?= $mi + 1 ?>">
                    </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </section>
    <?php endif; ?>

</div>

<?php include 'layout_talento/footer.php'; ?>
