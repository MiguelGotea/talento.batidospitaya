<?php
/**
 * Endpoint AJAX: Obtener detalle de plaza
 * Portal de Empleo Público - talento.batidospitaya.com
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../core/database/conexion.php';

try {
    // Validar parámetro ID
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('ID de plaza no especificado');
    }

    $plaza_id = intval($_GET['id']);

    // Obtener información de la plaza
    $sql = "
        SELECT 
            pc.id,
            pc.cargo,
            nc.Nombre as cargo_nombre,
            nc.especialidad_area,
            pc.sucursal,
            s.id as sucursal_id,
            s.nombre as sucursal_nombre,
            IF(s.codigo IN ('18', '6'), s.departamento, s.nombre) as departamento,
            pc.cantidad_real,
            pc.cantidad_adicional,
            pc.salario_propuesto,
            pc.nivel_urgencia,
            pc.fecha_creacion,
            pc.visible_web
        FROM plazas_cargos pc
        INNER JOIN NivelesCargos nc ON nc.CodNivelesCargos = pc.cargo
        INNER JOIN sucursales s ON s.codigo = CAST(pc.sucursal AS CHAR)
        WHERE pc.id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$plaza_id]);
    $plaza = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$plaza) {
        throw new Exception('Plaza no encontrada');
    }

    // Calcular cantidad cubierta
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
    $stmt_cubierta->execute([$plaza['cargo'], $plaza['sucursal_id']]);
    $cantidad_cubierta = $stmt_cubierta->fetch(PDO::FETCH_ASSOC)['total'];

    // Calcular plazas disponibles
    $plazas_disponibles = $plaza['cantidad_real'] + $plaza['cantidad_adicional'] - $cantidad_cubierta;

    // Convertir nivel de urgencia a texto
    $niveles_urgencia = [
        1 => 'Normal',
        2 => 'Medio',
        3 => 'Urgente',
        4 => 'Crítico'
    ];

    $nivel_urgencia_texto = $niveles_urgencia[$plaza['nivel_urgencia']] ?? 'Normal';

    // Preparar respuesta
    $response = [
        'id' => intval($plaza['id']),
        'cargo_id' => intval($plaza['cargo']),
        'cargo_nombre' => $plaza['cargo_nombre'],
        'especialidad_area' => $plaza['especialidad_area'] ?? 'General',
        'sucursal_id' => intval($plaza['sucursal']),
        'sucursal_nombre' => $plaza['sucursal_nombre'],
        'departamento' => $plaza['departamento'],
        'plazas_disponibles' => intval($plazas_disponibles),
        'salario_propuesto' => floatval($plaza['salario_propuesto']),
        'nivel_urgencia' => intval($plaza['nivel_urgencia']),
        'nivel_urgencia_texto' => $nivel_urgencia_texto,
        'fecha_publicacion' => $plaza['fecha_creacion'],
        'cantidad_cubierta' => intval($cantidad_cubierta),
        'cantidad_necesaria' => intval($plaza['cantidad_real']),
        'cantidad_adicional' => intval($plaza['cantidad_adicional']),
        'visible_web' => $plaza['visible_web']
    ];

    echo json_encode([
        'success' => true,
        'plaza' => $response
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>