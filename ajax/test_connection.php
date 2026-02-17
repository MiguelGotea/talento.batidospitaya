<?php
/**
 * Test de conexión y queries básicas
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

try {
    echo "1. Intentando incluir conexion.php...\n";
    require_once '../core/database/conexion.php';
    echo "2. Conexión incluida exitosamente\n";

    if (!isset($conn)) {
        throw new Exception("Variable \$conn no está definida");
    }

    echo "3. Variable \$conn existe\n";

    // Test query simple
    $sql = "SELECT COUNT(*) as total FROM plazas_cargos WHERE visible_web = 1";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Error en query: " . $conn->error);
    }

    $row = $result->fetch_assoc();
    echo "4. Query ejecutada: " . $row['total'] . " plazas visibles\n";

    // Test join con sucursales
    $sql2 = "
        SELECT pc.id, nc.Nombre, s.nombre as sucursal
        FROM plazas_cargos pc
        INNER JOIN NivelesCargos nc ON nc.CodNivelesCargos = pc.cargo
        INNER JOIN sucursales s ON s.codigo = CAST(pc.sucursal AS CHAR)
        WHERE pc.visible_web = 1
        LIMIT 1
    ";

    $result2 = $conn->query($sql2);

    if (!$result2) {
        throw new Exception("Error en query con joins: " . $conn->error);
    }

    if ($result2->num_rows > 0) {
        $row2 = $result2->fetch_assoc();
        echo "5. Join exitoso: Plaza #" . $row2['id'] . " - " . $row2['Nombre'] . " en " . $row2['sucursal'] . "\n";
    } else {
        echo "5. Join ejecutado pero sin resultados\n";
    }

    echo "\n✅ TODAS LAS PRUEBAS PASARON\n";

    echo json_encode([
        'success' => true,
        'message' => 'Conexión y queries funcionando correctamente'
    ]);

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>