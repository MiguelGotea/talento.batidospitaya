<?php
/**
 * Endpoint AJAX: Obtener plazas disponibles
 * Portal de Empleo Público - talento.batidospitaya.com
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json; charset=utf-8');

try {
    require_once '../core/database/conexion.php';

    // Parámetros de filtrado
    $categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
    $ubicacion = isset($_GET['ubicacion']) ? trim($_GET['ubicacion']) : '';
    $salario_min = isset($_GET['salario_min']) ? floatval($_GET['salario_min']) : 0;
    $salario_max = isset($_GET['salario_max']) ? floatval($_GET['salario_max']) : 999999;
    $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
    $orden = isset($_GET['orden']) ? $_GET['orden'] : 'urgencia';

    // Paginación
    $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
    $por_pagina = isset($_GET['por_pagina']) ? min(100, max(1, intval($_GET['por_pagina']))) : 25;
    $offset = ($pagina - 1) * $por_pagina;

    // Query base
    $sql = "
        SELECT 
            pc.id,
            pc.cargo,
            nc.Nombre as cargo_nombre,
            nc.especialidad_area,
            pc.sucursal,
            s.id as sucursal_id,
            s.nombre as sucursal_nombre,
            s.departamento,
            pc.cantidad_real,
            pc.cantidad_adicional,
            pc.salario_propuesto,
            pc.nivel_urgencia,
            pc.fecha_creacion,
            pc.ruta_banner_cargo
        FROM plazas_cargos pc
        INNER JOIN NivelesCargos nc ON nc.CodNivelesCargos = pc.cargo
        INNER JOIN sucursales s ON s.codigo = CAST(pc.sucursal AS CHAR)
        WHERE pc.visible_web = 1
          AND s.activa = 1
    ";

    $params = [];

    if (!empty($categoria)) {
        $sql .= " AND nc.especialidad_area = ?";
        $params[] = $categoria;
    }

    if (!empty($ubicacion)) {
        $sql .= " AND s.departamento = ?";
        $params[] = $ubicacion;
    }

    if ($salario_min > 0) {
        $sql .= " AND pc.salario_propuesto >= ?";
        $params[] = $salario_min;
    }

    if ($salario_max < 999999) {
        $sql .= " AND pc.salario_propuesto <= ?";
        $params[] = $salario_max;
    }

    if (!empty($busqueda)) {
        $sql .= " AND (nc.Nombre LIKE ? OR s.nombre LIKE ? OR s.departamento LIKE ?)";
        $busqueda_param = "%{$busqueda}%";
        $params[] = $busqueda_param;
        $params[] = $busqueda_param;
        $params[] = $busqueda_param;
    }

    // Ejecutar query con PDO
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $plazas_base = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcular plazas disponibles para cada una
    $plazas_temp = [];
    foreach ($plazas_base as $row) {
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
        $stmt_cubierta->execute([$row['cargo'], $row['sucursal_id']]);
        $cantidad_cubierta = $stmt_cubierta->fetch(PDO::FETCH_ASSOC)['total'];

        // Calcular plazas disponibles
        $plazas_disponibles = $row['cantidad_real'] + $row['cantidad_adicional'] - $cantidad_cubierta;

        // Solo incluir si hay plazas disponibles
        if ($plazas_disponibles > 0) {
            $plazas_temp[] = [
                'id' => intval($row['id']),
                'cargo_id' => intval($row['cargo']),
                'cargo_nombre' => $row['cargo_nombre'],
                'especialidad_area' => $row['especialidad_area'] ?? 'General',
                'sucursal_id' => intval($row['sucursal']),
                'sucursal_nombre' => $row['sucursal_nombre'],
                'departamento' => $row['departamento'],
                'plazas_disponibles' => intval($plazas_disponibles),
                'salario_propuesto' => floatval($row['salario_propuesto']),
                'nivel_urgencia' => intval($row['nivel_urgencia']),
                'fecha_publicacion' => $row['fecha_creacion'],
                'cantidad_cubierta' => intval($cantidad_cubierta),
                'cantidad_necesaria' => intval($row['cantidad_real']),
                'cantidad_adicional' => intval($row['cantidad_adicional']),
                'ruta_banner_cargo' => $row['ruta_banner_cargo'] ?? ''
            ];
        }
    }

    // Ordenar resultados
    usort($plazas_temp, function ($a, $b) use ($orden) {
        switch ($orden) {
            case 'fecha':
                return strtotime($b['fecha_publicacion']) - strtotime($a['fecha_publicacion']);
            case 'salario':
            default:
                return $b['salario_propuesto'] - $a['salario_propuesto'];
        }
    });

    $total_plazas = count($plazas_temp);
    $plazas = array_slice($plazas_temp, $offset, $por_pagina);

    // Obtener categorías
    $sql_categorias = "
        SELECT nc.especialidad_area, COUNT(DISTINCT pc.cargo) as count
        FROM plazas_cargos pc
        INNER JOIN NivelesCargos nc ON nc.CodNivelesCargos = pc.cargo
        INNER JOIN sucursales s ON s.codigo = CAST(pc.sucursal AS CHAR)
        WHERE pc.visible_web = 1 AND s.activa = 1
        GROUP BY nc.especialidad_area
        ORDER BY nc.especialidad_area
    ";

    $categorias = [];
    $stmt_cat = $conn->query($sql_categorias);
    while ($row = $stmt_cat->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($row['especialidad_area'])) {
            $categorias[] = [
                'nombre' => $row['especialidad_area'],
                'count' => intval($row['count'])
            ];
        }
    }

    // Obtener ubicaciones
    $sql_ubicaciones = "
        SELECT DISTINCT s.departamento
        FROM plazas_cargos pc
        INNER JOIN sucursales s ON s.codigo = CAST(pc.sucursal AS CHAR)
        WHERE pc.visible_web = 1 AND s.activa = 1
        ORDER BY s.departamento
    ";

    $ubicaciones = [];
    $stmt_ubi = $conn->query($sql_ubicaciones);
    while ($row = $stmt_ubi->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($row['departamento'])) {
            $ubicaciones[] = $row['departamento'];
        }
    }

    // Respuesta
    echo json_encode([
        'success' => true,
        'total' => $total_plazas,
        'pagina' => $pagina,
        'por_pagina' => $por_pagina,
        'total_paginas' => ceil($total_plazas / $por_pagina),
        'plazas' => $plazas,
        'categorias' => $categorias,
        'ubicaciones' => $ubicaciones
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener plazas disponibles',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>