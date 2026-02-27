<?php
/**
 * Endpoint AJAX: Guardar postulación
 * Portal de Empleo Público - talento.batidospitaya.com
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../core/database/conexion.php';

// Configurar zona horaria Nicaragua
date_default_timezone_set('America/Managua');

// Configuración: ¿Bloquear postulaciones duplicadas? (mismo cargo y sucursal en 30 días)
$BLOQUEAR_DUPLICADOS = false; // Cambiar a false para desactivar la validación

try {
    // Validar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Validar campos requeridos
    $camposRequeridos = ['plaza_id', 'cargo_id', 'nombre', 'direccion', 'telefono', 'experiencia', 'aspiracion'];
    foreach ($camposRequeridos as $campo) {
        if (!isset($_POST[$campo]) || trim($_POST[$campo]) === '') {
            throw new Exception("El campo {$campo} es requerido");
        }
    }

    // Validar archivo CV
    if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Debes adjuntar tu CV en formato PDF');
    }

    // Sanitizar datos
    $plaza_id = intval($_POST['plaza_id']);
    $cargo_id = intval($_POST['cargo_id']);
    $sucursal_id = !empty($_POST['sucursal_id']) ? intval($_POST['sucursal_id']) : null;
    $nombre = trim($_POST['nombre']);
    $direccion = trim($_POST['direccion']);
    $telefono = preg_replace('/\D/', '', $_POST['telefono']); // Solo números
    $experiencia = trim($_POST['experiencia']);
    $aspiracion = floatval($_POST['aspiracion']);

    // Validar nombre (al menos 2 palabras)
    $palabrasNombre = explode(' ', preg_replace('/\s+/', ' ', $nombre));
    if (count($palabrasNombre) < 2) {
        throw new Exception('El nombre completo debe tener al menos dos palabras');
    }

    // Validar teléfono (8 dígitos)
    if (strlen($telefono) !== 8) {
        throw new Exception('El teléfono debe tener exactamente 8 números');
    }
    $comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : null;

    // Correo es opcional; validar formato solo si fue enviado
    $correoRaw = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    if ($correoRaw !== '') {
        $correo = filter_var($correoRaw, FILTER_VALIDATE_EMAIL);
        if (!$correo) {
            throw new Exception('Correo electrónico inválido');
        }
    } else {
        $correo = null;
    }

    // Validar aspiración salarial
    if ($aspiracion <= 0) {
        throw new Exception('Aspiración salarial inválida');
    }

    // Validar archivo CV
    $archivo = $_FILES['cv'];
    $nombreArchivo = $archivo['name'];
    $tipoArchivo = $archivo['type'];
    $tamanioArchivo = $archivo['size'];
    $tmpArchivo = $archivo['tmp_name'];

    // Validar tipo PDF
    $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
    if ($extension !== 'pdf' || $tipoArchivo !== 'application/pdf') {
        throw new Exception('Solo se permiten archivos PDF');
    }

    // Validar tamaño (10MB)
    $maxTamanio = 10 * 1024 * 1024;
    if ($tamanioArchivo > $maxTamanio) {
        throw new Exception('El archivo no debe superar los 10MB');
    }

    // Verificar que la plaza existe y está disponible
    $sql_plaza = "
        SELECT pc.id, pc.cargo, pc.visible_web, pc.cantidad_real, pc.cantidad_adicional,
               s.id as sucursal_id
        FROM plazas_cargos pc
        INNER JOIN sucursales s ON s.codigo = CAST(pc.sucursal AS CHAR)
        WHERE pc.id = ? AND pc.cargo = ?
    ";

    $stmt_plaza = $conn->prepare($sql_plaza);
    $stmt_plaza->execute([$plaza_id, $cargo_id]);
    $plaza = $stmt_plaza->fetch(PDO::FETCH_ASSOC);

    if (!$plaza) {
        throw new Exception('Plaza no encontrada');
    }

    if (!$plaza['visible_web']) {
        throw new Exception('Esta plaza no está disponible para postulaciones');
    }

    // Calcular plazas disponibles
    $sql_cubierta = "
        SELECT COUNT(*) as total
        FROM AsignacionNivelesCargos anc
        JOIN sucursales s2 ON s2.codigo = CAST(anc.Sucursal AS CHAR)
        WHERE anc.CodNivelesCargos = ?
          AND s2.id = ?
          AND anc.Fecha <= CURDATE()
          AND (anc.Fin IS NULL OR anc.Fin >= CURDATE())
    ";

    $stmt_cubierta = $conn->prepare($sql_cubierta);
    $stmt_cubierta->execute([$cargo_id, $plaza['sucursal_id']]);
    $cantidad_cubierta = $stmt_cubierta->fetch(PDO::FETCH_ASSOC)['total'];

    $plazas_disponibles = $plaza['cantidad_real'] + $plaza['cantidad_adicional'] - $cantidad_cubierta;

    if ($plazas_disponibles <= 0) {
        throw new Exception('Esta plaza ya no tiene vacantes disponibles');
    }

    // Verificar duplicados si la opción está activa
    if ($BLOQUEAR_DUPLICADOS) {
        $sql_duplicado = "
            SELECT id FROM postulacion_plaza
            WHERE correo = ? 
            AND cargo_aplicado = ? 
            AND sucursal_aplicada = ?
            AND fecha_postulacion >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ";

        $stmt_dup = $conn->prepare($sql_duplicado);
        $stmt_dup->execute([$correo, $cargo_id, $sucursal_id]);

        if ($stmt_dup->fetch()) {
            throw new Exception('Ya has aplicado a esta plaza recientemente');
        }
    }

    // Crear directorio de uploads si no existe
    $uploadDir = '../uploads/cv/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generar nombre único
    $nombreUnico = uniqid('cv_') . '_' . time() . '.pdf';
    $rutaDestino = $uploadDir . $nombreUnico;

    // Mover archivo
    if (!move_uploaded_file($tmpArchivo, $rutaDestino)) {
        throw new Exception('Error al guardar el archivo CV');
    }

    // Obtener IP y User Agent
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    // Insertar postulación
    $sql_insert = "
        INSERT INTO postulacion_plaza (
            nombre, direccion, correo, telefono, ruta_cv, comentario,
            aspiracion_salarial, experiencia_laboral,
            status, cargo_aplicado, sucursal_aplicada,
            fecha_postulacion, ip_postulacion, user_agent
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'solicitado', ?, ?, NOW(), ?, ?)
    ";

    $stmt = $conn->prepare($sql_insert);
    $resultado = $stmt->execute([
        $nombre,
        $direccion,
        $correo,
        $telefono,
        $rutaDestino,
        $comentario,
        $aspiracion,
        $experiencia,
        $cargo_id,
        $sucursal_id,
        $ip,
        $userAgent
    ]);

    if (!$resultado) {
        // Si falla, eliminar archivo
        if (file_exists($rutaDestino)) {
            unlink($rutaDestino);
        }
        throw new Exception('Error al guardar la postulación');
    }

    $postulacion_id = $conn->lastInsertId();

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Postulación enviada exitosamente',
        'postulacion_id' => $postulacion_id
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>