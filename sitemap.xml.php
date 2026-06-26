<?php
/**
 * Sitemap XML Dinámico
 * Portal de Empleo Público - talento.batidospitaya.com
 */

header('Content-Type: application/xml; charset=utf-8');
require_once 'core/database/conexion.php';

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">

    <!-- Página principal de Empleo -->
    <url>
        <loc>https://talento.batidospitaya.com/</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Sobre Nosotros (ahora es la página principal) -->
    <url>
        <loc>https://talento.batidospitaya.com/index.php</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>

    <!-- Únete al Equipo (Vacantes) -->
    <url>
        <loc>https://talento.batidospitaya.com/unete.php</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    <!-- Nuestro Equipo -->
    <url>
        <loc>https://talento.batidospitaya.com/equipo.php</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Noticias -->
    <url>
        <loc>https://talento.batidospitaya.com/noticias.php</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Página de postulación -->
    <url>
        <loc>https://talento.batidospitaya.com/postular.php</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    <?php
    // Obtener plazas activas para incluir en sitemap
    $sql = "
        SELECT DISTINCT pc.id, pc.fecha_creacion, pc.fecha_actualizacion
        FROM plazas_cargos pc
        INNER JOIN sucursales s ON s.codigo = CAST(pc.sucursal AS CHAR)
        WHERE pc.visible_web = 1
          AND s.activa = 1
        ORDER BY pc.fecha_creacion DESC
        LIMIT 100
    ";

    $result = $conn->query($sql);

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $lastmod = $row['fecha_actualizacion'] ?? $row['fecha_creacion'];
        $lastmodFormatted = date('Y-m-d', strtotime($lastmod));
        ?>
        <url>
            <loc>https://talento.batidospitaya.com/vacante_detalle.php?plaza=<?php echo $row['id']; ?></loc>
            <lastmod><?php echo $lastmodFormatted; ?></lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
        <?php
    }
    ?>

</urlset>