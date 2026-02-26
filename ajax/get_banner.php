<?php
/**
 * Proxy de imágenes de banner de puestos
 * Lee el archivo desde el filesystem del ERP y lo sirve directamente
 * para evitar problemas de CORS entre talento.batidospitaya.com y erp.batidospitaya.com
 */

// Validar parámetro
$archivo = isset($_GET['archivo']) ? trim($_GET['archivo']) : '';

if (empty($archivo)) {
    http_response_code(400);
    exit('Archivo no especificado');
}

// Sanitizar: solo permitir nombre de archivo simple (sin rutas, sin ../)
$archivo = basename($archivo);

// Solo permitir extensiones de imagen
$ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
$extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($ext, $extensionesPermitidas)) {
    http_response_code(403);
    exit('Tipo de archivo no permitido');
}

// Determinar ruta del filesystem
// En Hostinger: /home/u.../domains/talento.batidospitaya.com/public_html/ajax
// En Local: C:\...\VisualCode\talento.batidospitaya.com\ajax

$directoriosPosibles = [
    // Opción 1: Producción (Hostinger)
    dirname(__DIR__, 3) . '/erp.batidospitaya.com/public_html/modulos/reclutamiento/uploads/banner_puesto/',
    // Opción 2: Local (Si erp y talento están en la misma carpeta raíz como VisualCode/)
    dirname(__DIR__, 2) . '/erp.batidospitaya.com/modulos/reclutamiento/uploads/banner_puesto/',
    // Opción 3: Local con public_html
    dirname(__DIR__, 2) . '/erp.batidospitaya.com/public_html/modulos/reclutamiento/uploads/banner_puesto/',
];

$rutaCompleta = '';
foreach ($directoriosPosibles as $rutaBase) {
    if (file_exists($rutaBase . $archivo)) {
        $rutaCompleta = $rutaBase . $archivo;
        break;
    }
}

if (empty($rutaCompleta) || !file_exists($rutaCompleta)) {
    error_log("Banner no encontrado: " . $archivo . " - Buscado en: " . implode(', ', $directoriosPosibles));
    http_response_code(404);
    exit('Banner no encontrado');
}

// Determinar Content-Type
$mimeTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
];

$contentType = $mimeTypes[$ext] ?? 'image/png';

// Servir la imagen con cache de 1 hora
header('Content-Type: ' . $contentType);
header('Cache-Control: public, max-age=3600');
header('Content-Length: ' . filesize($rutaCompleta));
readfile($rutaCompleta);
exit;
?>