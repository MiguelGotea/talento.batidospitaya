<?php
// get_plazas.php

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

    // Query base - Eliminamos el filtro de categoría de la consulta SQL 
    // para poder contar todas las categorías en una sola pasada
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

    // Nota: El filtro de categoría se aplica ahora en el loop de PHP 
    // para mantener consistencia en los conteos de todos los tabs

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

    // Calcular plazas disponibles y agrupar resultados
    $plazas_temp = [];
    $categorias_conteo = [];
    $vistos_grupos = []; // Para agrupar tarjetas en el grid

    foreach ($plazas_base as $row) {
        // Determinar grupo de cargos para el cálculo de cubiertos
        $cargo_id = intval($row['cargo']);
        $es_vendedor = ($cargo_id == 2 || $cargo_id == 44 || $cargo_id == 45 || $cargo_id == 46 || $cargo_id == 47);
        $es_lider = ($cargo_id == 5 || $cargo_id == 43);

        $codigos_grupo = [$cargo_id];
        $cargo_id_estandar = $cargo_id;

        if ($es_vendedor) {
            $codigos_grupo = [2, 44, 45, 46, 47];
            $cargo_id_estandar = 2;
        } else if ($es_lider) {
            $codigos_grupo = [5, 43];
            $cargo_id_estandar = 5;
        }

        $in_clause = implode(',', array_map('intval', $codigos_grupo));

        // Calcular cantidad cubierta
        $sql_cubierta = "
            SELECT COUNT(DISTINCT anc.CodOperario) as total
            FROM AsignacionNivelesCargos anc
            INNER JOIN Contratos c ON anc.CodOperario = c.cod_operario
            WHERE anc.CodNivelesCargos IN ($in_clause)
              AND (anc.Sucursal = ? OR ? IS NULL OR ? = '0')
              AND anc.Fecha <= CURDATE()
              AND (anc.Fin IS NULL OR anc.Fin = '' OR anc.Fin >= CURDATE())
              AND c.Finalizado = 0
        ";

        $stmt_cubierta = $conn->prepare($sql_cubierta);
        $stmt_cubierta->execute([$row['sucursal'], $row['sucursal'], $row['sucursal']]);
        $cantidad_cubierta = $stmt_cubierta->fetch(PDO::FETCH_ASSOC)['total'];

        // Calcular plazas disponibles
        $plazas_disponibles = max(0, $row['cantidad_real'] - $cantidad_cubierta) + $row['cantidad_adicional'];

        // Solo procesar si hay plazas disponibles
        if ($plazas_disponibles > 0) {
            // Definir llave de agrupación
            // Si es Vendedor o Líder, agrupamos por cargo_estandar + departamento
            // Si es cualquier otro cargo, agrupamos por cargo + sucursal (tarjeta individual por tienda)
            if ($es_vendedor || $es_lider) {
                $group_key = $cargo_id_estandar . '_' . $row['departamento'];
            } else {
                $group_key = $cargo_id . '_' . $row['sucursal'];
            }

            $cat_nombre = !empty($row['departamento']) ? trim($row['departamento']) : 'Otros';

            if (!isset($vistos_grupos[$group_key])) {
                // Nueva tarjeta
                $vistos_grupos[$group_key] = [
                    'id' => intval($row['id']),
                    'cargo_id' => $cargo_id_estandar,
                    'cargo_original' => $cargo_id,
                    'cargo_nombre' => $row['cargo_nombre'],
                    'especialidad_area' => !empty($row['especialidad_area']) ? trim($row['especialidad_area']) : 'Otros',
                    'sucursal_id' => intval($row['sucursal']),
                    'sucursal_nombre' => ($es_vendedor || $es_lider) ? "Múltiples sucursales ({$row['departamento']})" : $row['sucursal_nombre'],
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

                // Incrementar conteo de categoría (solo una vez por tarjeta/grupo)
                if (!isset($categorias_conteo[$cat_nombre])) {
                    $categorias_conteo[$cat_nombre] = 0;
                }
                $categorias_conteo[$cat_nombre]++;
            } else {
                // Acumular plazas en tarjeta existente
                $vistos_grupos[$group_key]['plazas_disponibles'] += intval($plazas_disponibles);
                $vistos_grupos[$group_key]['cantidad_cubierta'] += intval($cantidad_cubierta);
                $vistos_grupos[$group_key]['cantidad_necesaria'] += intval($row['cantidad_real']);
                $vistos_grupos[$group_key]['cantidad_adicional'] += intval($row['cantidad_adicional']);
                
                // Mantener el nivel de urgencia más alto si difieren
                if (intval($row['nivel_urgencia']) > $vistos_grupos[$group_key]['nivel_urgencia']) {
                    $vistos_grupos[$group_key]['nivel_urgencia'] = intval($row['nivel_urgencia']);
                }
            }
        }
    }

    // Filtrar resultados por categoría (departamento) si se solicitó
    foreach ($vistos_grupos as $plaza) {
        if (empty($categoria) || $plaza['departamento'] === $categoria) {
            $plazas_temp[] = $plaza;
        }
    }

    // Ordenar resultados
    usort($plazas_temp, function ($a, $b) use ($orden) {
        if ($a['nivel_urgencia'] != $b['nivel_urgencia']) {
            return $b['nivel_urgencia'] - $a['nivel_urgencia'];
        }
        switch ($orden) {
            case 'fecha':
                return strtotime($b['fecha_publicacion']) - strtotime($a['fecha_publicacion']);
            case 'salario':
            default:
                if ($a['salario_propuesto'] != $b['salario_propuesto']) {
                    return $b['salario_propuesto'] - $a['salario_propuesto'];
                }
                return $b['id'] - $a['id'];
        }
    });

    $total_plazas_spots = 0;
    foreach ($plazas_temp as $p) {
        $total_plazas_spots += $p['plazas_disponibles'];
    }

    $total_vacantes_conteo = count($plazas_temp);
    $plazas_paginadas = array_slice($plazas_temp, $offset, $por_pagina);

    // Formatear categorías para la respuesta
    $categorias_final = [];
    ksort($categorias_conteo);
    foreach ($categorias_conteo as $nombre => $count) {
        $categorias_final[] = [
            'nombre' => $nombre,
            'count' => $count
        ];
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
        'total' => $total_plazas_spots, // Total de puestos (sumando cantidades)
        'total_vacantes' => $total_vacantes_conteo, // Total de filas en el grid
        'pagina' => $pagina,
        'por_pagina' => $por_pagina,
        'total_paginas' => ceil($total_vacantes_conteo / $por_pagina),
        'plazas' => $plazas_paginadas,
        'categorias' => $categorias_final,
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

