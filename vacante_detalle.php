<?php
// vacante_detalle.php — Detalle público de una vacante
// Recibe: ?plaza=ID (ID del registro en plazas_cargos)

$plaza_id = isset($_GET['plaza']) ? intval($_GET['plaza']) : 0;

if ($plaza_id <= 0) {
    header('Location: unete.php');
    exit();
}

// Obtener detalle antes de generar el <head> para el SEO dinámico
require_once 'core/database/conexion.php';

$detalle = null;
try {
    $sql = "
        SELECT
            pc.id,
            pc.cargo,
            nc.Nombre AS cargo_nombre,
            nc.especialidad_area,
            pc.sucursal,
            s.nombre AS sucursal_nombre,
            s.departamento,
            pc.salario_propuesto,
            pc.nivel_urgencia,
            pc.fecha_creacion,
            pc.descripcion,
            pc.responsabilidades,
            pc.requisitos,
            pc.habilidades,
            pc.ruta_banner_cargo
        FROM plazas_cargos pc
        INNER JOIN NivelesCargos nc ON nc.CodNivelesCargos = pc.cargo
        INNER JOIN sucursales s ON s.codigo = CAST(pc.sucursal AS CHAR)
        WHERE pc.id = :id AND pc.visible_web = 1 AND s.activa = 1
        LIMIT 1
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $plaza_id, PDO::PARAM_INT);
    $stmt->execute();
    $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Si falla la BD, redirigir
}

if (!$detalle) {
    header('Location: unete.php');
    exit();
}

// Obtener habilidades desde catálogo
$habilidadesNombres = [];
if (!empty($detalle['habilidades'])) {
    $ids = array_map('intval', array_filter(explode(',', $detalle['habilidades'])));
    if (!empty($ids)) {
        $inClause = implode(',', $ids);
        try {
            $stmtHab = $conn->query("SELECT nombre, categoria FROM habilidades_talento WHERE id IN ($inClause) AND activo = 1 ORDER BY categoria, nombre");
            $habilidadesNombres = $stmtHab->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {}
    }
}

// Preparar datos para la vista
$urgenciaMap = [1 => 'No urgente', 2 => 'Urgencia media', 3 => 'Urgente', 4 => 'Crítico'];
$urgenciaBadgeMap = [1 => 'success', 2 => 'warning', 3 => 'orange', 4 => 'danger'];
$urgenciaEmoji = [1 => '⚪', 2 => '🟡', 3 => '🟠', 4 => '🔴'];
$nivelUrgencia = intval($detalle['nivel_urgencia']);
$urgenciaTexto = $urgenciaMap[$nivelUrgencia] ?? 'No urgente';
$urgenciaBadge = $urgenciaBadgeMap[$nivelUrgencia] ?? 'secondary';

$responsabilidades = !empty($detalle['responsabilidades'])
    ? array_filter(array_map('trim', explode("\n", str_replace('|', "\n", $detalle['responsabilidades']))))
    : [];

$requisitos = !empty($detalle['requisitos'])
    ? array_filter(array_map('trim', explode("\n", str_replace('|', "\n", $detalle['requisitos']))))
    : [];

// Fecha legible
$fechaCreacion = !empty($detalle['fecha_creacion'])
    ? (new DateTime($detalle['fecha_creacion']))->format('d/m/Y')
    : date('d/m/Y');

// SEO
$page_title = htmlspecialchars($detalle['cargo_nombre']) . " — Vacante en Batidos Pitaya Nicaragua";
$page_description = !empty($detalle['descripcion'])
    ? htmlspecialchars(mb_substr($detalle['descripcion'], 0, 155))
    : "Aplica a la vacante de " . htmlspecialchars($detalle['cargo_nombre']) . " en Batidos Pitaya. Trabajá en un ambiente positivo y de crecimiento.";
$page_keywords = "vacante " . htmlspecialchars($detalle['cargo_nombre']) . ", empleo batidos pitaya, trabajo " . htmlspecialchars($detalle['departamento']);
$page_canonical = "vacante_detalle.php?plaza=" . $plaza_id;
$active_tab = "unete";

include 'layout_talento/header.php';
?>

<!-- ==================== DETALLE DE VACANTE ==================== -->
<div id="section-vacante-detalle" class="tab-section-content active-tab">
    <section class="vacante-detalle-section">
        <div class="container">

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb vacante-breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="unete.php"><i class="bi bi-house-fill"></i> Vacantes</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= htmlspecialchars($detalle['cargo_nombre']) ?>
                    </li>
                </ol>
            </nav>

            <div class="row g-4">
                <!-- Columna principal -->
                <div class="col-lg-8">
                    <div class="vacante-detalle-card">
                        <!-- Encabezado del cargo -->
                        <div class="vacante-detalle-header">
                            <span class="vacante-categoria mb-2 d-inline-block">
                                <?= htmlspecialchars($detalle['especialidad_area'] ?: 'Operaciones') ?>
                            </span>
                            <h1 class="vacante-detalle-titulo">
                                <?= htmlspecialchars($detalle['cargo_nombre']) ?>
                            </h1>
                            <div class="vacante-detalle-meta">
                                <span class="meta-item">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <?= htmlspecialchars($detalle['departamento']) ?>
                                </span>

                            </div>
                        </div>

                        <!-- Descripción del cargo -->
                        <?php if (!empty($detalle['descripcion'])): ?>
                        <div class="vacante-seccion">
                            <h2 class="vacante-seccion-titulo">
                                <i class="bi bi-person-lines-fill"></i> Sobre este cargo
                            </h2>
                            <div class="vacante-seccion-texto">
                                <?= nl2br($detalle['descripcion']) ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Responsabilidades -->
                        <?php if (!empty($responsabilidades)): ?>
                        <div class="vacante-seccion">
                            <h2 class="vacante-seccion-titulo">
                                <i class="bi bi-list-check"></i> Responsabilidades
                            </h2>
                            <ul class="vacante-lista">
                                <?php foreach ($responsabilidades as $item): ?>
                                    <li><?= htmlspecialchars($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <!-- Requisitos -->
                        <?php if (!empty($requisitos)): ?>
                        <div class="vacante-seccion">
                            <h2 class="vacante-seccion-titulo">
                                <i class="bi bi-clipboard-check-fill"></i> Requisitos
                            </h2>
                            <ul class="vacante-lista">
                                <?php foreach ($requisitos as $item): ?>
                                    <li><?= htmlspecialchars($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <!-- Habilidades -->
                        <?php if (!empty($habilidadesNombres)): ?>
                        <div class="vacante-seccion">
                            <h2 class="vacante-seccion-titulo">
                                <i class="bi bi-tags-fill"></i> Habilidades requeridas
                            </h2>
                            <div class="vacante-habilidades-grid">
                                <?php foreach ($habilidadesNombres as $hab): ?>
                                    <span class="vacante-habilidad-badge">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <?= htmlspecialchars($hab['nombre']) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Si no hay ningún detalle configurado -->
                        <?php if (empty($detalle['descripcion']) && empty($responsabilidades) && empty($requisitos) && empty($habilidadesNombres)): ?>
                        <div class="vacante-seccion">
                            <div class="alert alert-info d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle-fill fs-5"></i>
                                <span>Los detalles de esta vacante serán publicados próximamente. Podés aplicar directamente usando el botón de la derecha.</span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Columna lateral: Postulación -->
                <div class="col-lg-4">
                    <div class="vacante-sidebar">

                        <!-- Tarjeta de salario y postulación -->
                        <div class="sidebar-card">


                            <button
                                class="btn-postular w-100 mt-3"
                                id="btnPostularDetalle"
                                onclick="postularDesdeDetalle(<?= $plaza_id ?>, <?= intval($detalle['cargo']) ?>, <?= intval($detalle['sucursal']) ?>)">
                                <i class="bi bi-send-fill"></i>
                                Postular Ahora
                            </button>

                            <a href="unete.php" class="btn-leer-mas w-100 mt-2">
                                <i class="bi bi-arrow-left"></i>
                                Ver otras vacantes
                            </a>
                        </div>

                        <!-- Información general -->
                        <div class="sidebar-card mt-3">
                            <h3 class="sidebar-section-title">
                                <i class="bi bi-info-circle"></i> Información general
                            </h3>
                            <ul class="sidebar-info-list">
                                <li>
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span><strong>Ubicación:</strong> <?= htmlspecialchars($detalle['departamento']) ?></span>
                                </li>
                                <li>
                                    <i class="bi bi-briefcase-fill"></i>
                                    <span><strong>Área:</strong> <?= htmlspecialchars($detalle['especialidad_area'] ?: 'Operaciones') ?></span>
                                </li>

                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function postularDesdeDetalle(plazaId, cargoId, sucursalId) {
    // Redirigir al flujo de postulación estándar
    if (typeof postularDirecto === 'function') {
        postularDirecto(plazaId, cargoId, sucursalId);
    } else {
        // Fallback: redirigir a index.php con parámetros
        window.location.href = `unete.php?postular=${plazaId}&cargo=${cargoId}&sucursal=${sucursalId}`;
    }
}
</script>

<?php include 'layout_talento/footer.php'; ?>
