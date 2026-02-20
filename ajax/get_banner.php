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

// Ruta del filesystem en Hostinger
// __DIR__ = /home/u839374897/domains/talento.batidospitaya.com/public_html/ajax
// dirname(__DIR__, 3) = /home/u839374897/domains
$rutaBase = dirname(__DIR__, 3) . '/erp.batidospitaya.com/public_html/modulos/reclutamiento/banner_puesto/';

$rutaCompleta = $rutaBase . $archivo;

if (!file_exists($rutaCompleta)) {
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