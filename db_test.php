<?php
require_once 'core/database/conexion.php';
try {
    $stmt = $conn->query("SELECT token, codigo_acceso, link_status, fecha_aplicacion FROM solicitud_empleo ORDER BY id DESC LIMIT 5");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
