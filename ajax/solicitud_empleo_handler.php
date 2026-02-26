<?php
// solicitud_empleo_handler.php
require_once '../core/database/conexion.php';
header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? '';
    $token = trim($_POST['token'] ?? '');

    if (empty($token)) {
        throw new Exception('Token inválido o ausente');
    }

    // Validar token
    $stmt = $conn->prepare("SELECT id FROM solicitud_empleo WHERE token = :token");
    $stmt->bindValue(':token', $token, PDO::PARAM_STR);
    $stmt->execute();
    $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$solicitud) {
        throw new Exception('Solicitud no encontrada');
    }

    $id = $solicitud['id'];

    if ($action === 'save_progress') {

        $conn->beginTransaction();

        // ── 1. CAMPOS TABLA PRINCIPAL ─────────────────────────────────────
        $mainFields = [
            // Sección 1 - Datos Personales
            'nombre1',
            'nombre2',
            'apellido1',
            'apellido2',
            'puesto_solicitado',
            'expectativa_salarial',
            'telefono_fijo',
            'telefono_celular',
            'direccion_actual',
            'lugar_nacimiento',
            'fecha_nacimiento',
            'edad',
            'nacionalidad',
            'correo_electronico',
            'peso',
            'estatura',
            'estado_civil',
            'tipo_sangre',
            'contacto_emergencia_nombre',
            'contacto_emergencia_telefono',
            'alergias',
            'posee_vehiculo',
            'vehiculo_modelo',
            // Sección 2 - Documentos ID
            'numero_cedula',
            'lugar_emision_cedula',
            'fecha_emision_cedula',
            'numero_inss',
            'numero_serie_licencia',
            // Sección 3 - Familiares en empresa
            'tiene_familiares_empresa',
            'familiar_empresa_nombre',
            'familiar_empresa_puesto',
            'familiar_empresa_lugar',
            // Sección 4 - Conocimientos (los campos de estudios actuales van en solicitud_empleo_estudios)
            'conocimientos_especializados',
            'idiomas',
            // Sección 5 - Aficiones, salud, info adicional
            'aficiones',
            'practica_deporte',
            'deportes_cuales',
            'padece_enfermedad',
            'enfermedades_cuales',
            'usa_lentes',
            'padece_capacidad_especial',
            'capacidad_especial_cual',
            'hospitalizado_ultimos_6_meses',
            'razon_hospitalizacion',
            'areas_interes',
            'tiene_pariente_amigo_empresa',
            'pariente_amigo_nombre',
            'pariente_amigo_puesto',
            'pariente_amigo_tipo',
            'ha_ocupado_cargo_publico',
            'cargo_publico_desempenado',
            'periodo_cargo_publico',
            'familiar_cargo_publico',
            'familiar_cargo_publico_nombre',
            'familiar_cargo_publico_periodo',
            'familiar_cargo_publico_cargo',
            'cargo_directivo_partido_politico',
            'partido_politico_nombre',
            'partido_politico_periodo',
        ];

        $pairs = [];
        $params = [':id' => $id];
        foreach ($mainFields as $field) {
            if (isset($_POST[$field])) {
                $pairs[] = "`$field` = :$field";
                $params[":$field"] = $_POST[$field] === '' ? null : $_POST[$field];
            }
        }

        if (!empty($pairs)) {
            $sqlUpd = "UPDATE solicitud_empleo SET " . implode(', ', $pairs) . ", updated_at = NOW() WHERE id = :id";
            $conn->prepare($sqlUpd)->execute($params);
        }

        // ── 2. FAMILIARES (padre, madre, cónyuge, tutor) ─────────────────
        // Elimina y re-inserta solo las filas con nombre
        $conn->prepare("DELETE FROM solicitud_empleo_familiares WHERE id_solicitud = :id")->execute([':id' => $id]);
        $stmtFam = $conn->prepare(
            "INSERT INTO solicitud_empleo_familiares
             (id_solicitud, parentesco, nombre_familiar, ocupacion_familiar, lugar_trabajo_familiar, depende_economicamente)
             VALUES (:id, :parentesco, :nombre, :ocupacion, :lugar, :depende)"
        );
        foreach (['padre', 'madre', 'conyuge', 'tutor'] as $p) {
            $nombre = trim($_POST["familiar_nombre_{$p}"] ?? '');
            if ($nombre !== '') {
                $stmtFam->execute([
                    ':id' => $id,
                    ':parentesco' => ucfirst($p === 'conyuge' ? 'Cónyuge' : $p),
                    ':nombre' => $nombre,
                    ':ocupacion' => $_POST["familiar_ocupacion_{$p}"] ?? null,
                    ':lugar' => $_POST["familiar_trabajo_{$p}"] ?? null,
                    ':depende' => ($_POST["familiar_depende_{$p}"] ?? 'No') === 'Si' ? 1 : 0,
                ]);
            }
        }

        // ── 3. HIJOS ──────────────────────────────────────────────────────
        $conn->prepare("DELETE FROM solicitud_empleo_hijos WHERE id_solicitud = :id")->execute([':id' => $id]);
        $stmtHijo = $conn->prepare(
            "INSERT INTO solicitud_empleo_hijos
             (id_solicitud, nombre_hijo, fecha_nacimiento_hijo, edad_hijo, sexo_hijo, estudios_hijo, depende_economicamente_hijo)
             VALUES (:id, :nombre, :fecha, :edad, :sexo, :estudios, :depende)"
        );
        for ($h = 1; $h <= 4; $h++) {
            $nombre = trim($_POST["hijo_nombre_{$h}"] ?? '');
            if ($nombre !== '') {
                $stmtHijo->execute([
                    ':id' => $id,
                    ':nombre' => $nombre,
                    ':fecha' => $_POST["hijo_fecha_nac_{$h}"] ?: null,
                    ':edad' => $_POST["hijo_edad_{$h}"] ?: null,
                    ':sexo' => $_POST["hijo_sexo_{$h}"] ?: null,
                    ':estudios' => $_POST["hijo_estudio_{$h}"] ?? null,
                    ':depende' => ($_POST["hijo_depende_{$h}"] ?? 'No') === 'Si' ? 1 : 0,
                ]);
            }
        }

        // ── 4. ESTUDIOS ───────────────────────────────────────────────────
        $conn->prepare("DELETE FROM solicitud_empleo_estudios WHERE id_solicitud = :id")->execute([':id' => $id]);
        // Insertar también la fila de "estudia actualmente" si procede
        $stmtEst = $conn->prepare(
            "INSERT INTO solicitud_empleo_estudios
             (id_solicitud, nivel_estudio, lugar_estudio, grado_cursado, año_estudio, titulo_obtenido,
              estudiando_actualmente, curso_actual, nivel_actual, horario_estudio, lugar_estudio_actual)
             VALUES (:id, :nivel, :lugar, :grado, :ano, :titulo, :estudiando, :curso, :nivel_act, :horario, :lugar_act)"
        );
        // Las claves generadas por PHP: preg_replace('/[^a-z0-9]/i','_', strtolower($nivel))
        $nivelesKeys = [
            'bachillerato' => 'Bachillerato',
            '_cnico_u_otros' => 'Técnico u Otros',
            'universitario' => 'Universitario',
            'post_universitario' => 'Post Universitario',
        ];
        $estudActualmente = intval($_POST['estudiando_actualmente'] ?? 0);
        $cursoActual = $_POST['curso_actual'] ?? null;
        $nivelActual = $_POST['nivel_actual'] ?? null;
        $horarioEstudio = $_POST['horario_estudio'] ?? null;
        $lugarEstActual = $_POST['lugar_estudio_actual'] ?? null;

        foreach ($nivelesKeys as $nk => $nivelNombre) {
            $lugar = trim($_POST["estudio_lugar_{$nk}"] ?? '');
            // Insertar si tiene datos O si es la última fila y estudia actualmente
            $esEstudiando = ($nk === 'universitario' || $nk === 'post_universitario') ? $estudActualmente : 0;
            if ($lugar !== '' || ($esEstudiando && $nk === 'universitario')) {
                $stmtEst->execute([
                    ':id' => $id,
                    ':nivel' => $nivelNombre,
                    ':lugar' => $lugar ?: null,
                    ':grado' => $_POST["estudio_grado_{$nk}"] ?? null,
                    ':ano' => $_POST["estudio_ano_{$nk}"] ?? null,
                    ':titulo' => $_POST["estudio_titulo_{$nk}"] ?? null,
                    ':estudiando' => $esEstudiando,
                    ':curso' => $esEstudiando ? $cursoActual : null,
                    ':nivel_act' => $esEstudiando ? $nivelActual : null,
                    ':horario' => $esEstudiando ? $horarioEstudio : null,
                    ':lugar_act' => $esEstudiando ? $lugarEstActual : null,
                ]);
            }
        }

        // ── 5. EXPERIENCIA LABORAL ────────────────────────────────────────
        $conn->prepare("DELETE FROM solicitud_empleo_experiencia WHERE id_solicitud = :id")->execute([':id' => $id]);
        $stmtExp = $conn->prepare(
            "INSERT INTO solicitud_empleo_experiencia
             (id_solicitud, tipo_empleo, empresa, direccion_empresa, telefono_empresa,
              jefe_inmediato, fecha_desde, fecha_hasta, puesto, sueldo)
             VALUES (:id, :tipo, :empresa, :dir, :tel, :jefe, :desde, :hasta, :puesto, :sueldo)"
        );
        foreach (['Ultimo', 'Penultimo', 'Antepenultimo'] as $tipo) {
            $tk = strtolower($tipo);
            $empresa = trim($_POST["exp_empresa_{$tk}"] ?? '');
            if ($empresa !== '') {
                $stmtExp->execute([
                    ':id' => $id,
                    ':tipo' => $tipo,
                    ':empresa' => $empresa,
                    ':dir' => $_POST["exp_direccion_{$tk}"] ?? null,
                    ':tel' => $_POST["exp_telefono_{$tk}"] ?? null,
                    ':jefe' => $_POST["exp_jefe_{$tk}"] ?? null,
                    ':desde' => $_POST["exp_desde_{$tk}"] ?: null,
                    ':hasta' => $_POST["exp_hasta_{$tk}"] ?: null,
                    ':puesto' => $_POST["exp_puesto_{$tk}"] ?? null,
                    ':sueldo' => $_POST["exp_sueldo_{$tk}"] ?: null,
                ]);
            }
        }

        // ── 6. REFERENCIAS ────────────────────────────────────────────────
        $conn->prepare("DELETE FROM solicitud_empleo_referencias WHERE id_solicitud = :id")->execute([':id' => $id]);
        $stmtRef = $conn->prepare(
            "INSERT INTO solicitud_empleo_referencias
             (id_solicitud, nombre_referencia, direccion_referencia, telefono_casa_referencia, telefono_oficina_referencia)
             VALUES (:id, :nombre, :dir, :tel_casa, :tel_ofi)"
        );
        for ($r = 1; $r <= 4; $r++) {
            $nombre = trim($_POST["ref_nombre_{$r}"] ?? '');
            if ($nombre !== '') {
                $stmtRef->execute([
                    ':id' => $id,
                    ':nombre' => $nombre,
                    ':dir' => $_POST["ref_direccion_{$r}"] ?? null,
                    ':tel_casa' => $_POST["ref_tel_casa_{$r}"] ?? null,
                    ':tel_ofi' => $_POST["ref_tel_oficina_{$r}"] ?? null,
                ]);
            }
        }

        // ── 7. ARCHIVOS ADJUNTOS ─────────────────────────────────────────
        $adjuntos = ['adjunto_cedula', 'adjunto_record_ley510', 'adjunto_constancia_judicial', 'adjunto_certificado_salud'];
        $uploadDir = __DIR__ . '/../uploads/adjuntos/';
        if (!is_dir($uploadDir))
            mkdir($uploadDir, 0775, true);

        $adjPairs = [];
        $adjParams = [':id' => $id];
        foreach ($adjuntos as $campo) {
            if (isset($_FILES[$campo]) && $_FILES[$campo]['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION));
                $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
                if (!in_array($ext, $allowed))
                    continue;
                if ($_FILES[$campo]['size'] > 5 * 1024 * 1024)
                    continue; // 5 MB
                $filename = $campo . '_' . $id . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES[$campo]['tmp_name'], $uploadDir . $filename)) {
                    $adjPairs[] = "`$campo` = :$campo";
                    $adjParams[":$campo"] = 'adjuntos/' . $filename;
                }
            }
        }
        if (!empty($adjPairs)) {
            $conn->prepare("UPDATE solicitud_empleo SET " . implode(', ', $adjPairs) . " WHERE id = :id")->execute($adjParams);
        }

        // ── 8. PORCENTAJE DE COMPLETITUD ─────────────────────────────────
        // Calculamos basado en los campos críticos rellenos
        $camposClave = [
            'nombre1',
            'apellido1',
            'telefono_celular',
            'correo_electronico',
            'direccion_actual',
            'numero_cedula',
            'fecha_nacimiento'
        ];
        $rellenos = 0;
        foreach ($camposClave as $c) {
            if (!empty($_POST[$c]))
                $rellenos++;
        }
        $porcentaje = round(($rellenos / count($camposClave)) * 100);
        $conn->prepare("UPDATE solicitud_empleo SET porcentaje_completitud = :p WHERE id = :id")
            ->execute([':p' => $porcentaje, ':id' => $id]);

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Progreso guardado', 'porcentaje' => $porcentaje]);

    } else {
        throw new Exception('Acción no permitida');
    }

} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction())
        $conn->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>