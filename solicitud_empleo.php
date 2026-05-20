<?php
// solicitud_empleo.php - Formulario para postulantes aprobados
require_once 'core/database/conexion.php';
require_once 'core/helpers/config.php';
session_start();

$token = trim($_GET['t'] ?? '');
if (empty($token)) {
    header('Location: 404.php');
    exit();
}

// Validar que el token exista en la BD y obtener los datos ya guardados
$sqlToken = "SELECT se.*, pp.nombre, pp.correo, pp.telefono, nc.Nombre as nombre_cargo
             FROM solicitud_empleo se
             INNER JOIN postulacion_plaza pp ON se.id_postulacion = pp.id
             INNER JOIN NivelesCargos nc ON pp.cargo_aplicado = nc.CodNivelesCargos
             WHERE se.token = :token
             LIMIT 1";
$stmtToken = $conn->prepare($sqlToken);
$stmtToken->bindValue(':token', $token, PDO::PARAM_STR);
$stmtToken->execute();
$solicitud = $stmtToken->fetch(PDO::FETCH_ASSOC);

if (!$solicitud) {
    header('Location: 404.php');
    exit();
}

// ── CONTROL DE ACCESO (Vigencia del Link) ─────────────────────────
$is_disabled = ($solicitud['link_status'] ?? 'activo') === 'deshabilitado';
$is_submitted = !empty($solicitud['fecha_aplicacion']);

if ($is_disabled || $is_submitted) {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Acceso No Disponible</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css'>
        <style>
            body { background: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
            .error-card { max-width: 500px; width: 90%; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); text-align: center; }
            .error-icon { font-size: 4rem; color: #dc3545; margin-bottom: 20px; }
            h2 { font-weight: 700; color: #333; }
            p { color: #666; margin-bottom: 30px; }
            .btn-home { background: #000; color: white; border-radius: 100px; padding: 12px 30px; text-decoration: none; font-weight: 600; transition: all 0.3s; }
            .btn-home:hover { background: #333; color: white; transform: translateY(-3px); }
        </style>
    </head>
    <body>
        <div class='error-card'>
            <div class='error-icon'><i class='bi bi-shield-lock-fill'></i></div>
            <h2>Acceso No Disponible</h2>
            <p>Este link ya no está activo, ha expirado o la solicitud ya ha sido enviada correctamente.</p>
            <a href='https://batidospitaya.com' class='btn-home'>Volver al inicio</a>
        </div>
    </body>
    </html>";
    exit();
}

// ── VERIFICACIÓN DE CÓDIGO 2FA ────────────────────────────────────
// Si es una carga de página normal (GET) y no trae el flag de verificación reciente
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['v'])) {
    unset($_SESSION['verified_token_' . $token]);
}

$is_verified = (isset($_SESSION['verified_token_' . $token]) && $_SESSION['verified_token_' . $token] === true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'verify_code') {
    header('Content-Type: application/json');
    $inputCode = trim($_POST['code'] ?? '');

    if ($inputCode == $solicitud['codigo_acceso']) {
        $_SESSION['verified_token_' . $token] = true;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Código de verificación incorrecto.']);
    }
    exit();
}
// ──────────────────────────────────────────────────────────────────

$id_solicitud = $solicitud['id'];

// ── OBTENER DATOS RELACIONADOS ────────────────────────────────────

// Familiares
$familiares = [];
$stmtFam = $conn->prepare("SELECT * FROM solicitud_empleo_familiares WHERE id_solicitud = :id");
$stmtFam->execute([':id' => $id_solicitud]);
while ($f = $stmtFam->fetch(PDO::FETCH_ASSOC)) {
    $parentescoKey = strtolower(str_replace(['ó', 'ñ'], ['o', 'n'], $f['parentesco']));
    $familiares[$parentescoKey] = $f;
}

// Hijos
$hijos = [];
$stmtHijos = $conn->prepare("SELECT * FROM solicitud_empleo_hijos WHERE id_solicitud = :id LIMIT 4");
$stmtHijos->execute([':id' => $id_solicitud]);
$hijos = $stmtHijos->fetchAll(PDO::FETCH_ASSOC);

// Estudios
$estudios = [];
$stmtEst = $conn->prepare("SELECT * FROM solicitud_empleo_estudios WHERE id_solicitud = :id");
$stmtEst->execute([':id' => $id_solicitud]);
while ($e = $stmtEst->fetch(PDO::FETCH_ASSOC)) {
    $nivelKey = str_replace(' ', '_', mb_strtolower(preg_replace('/[^a-z0-9 ]/i', '', $e['nivel_estudio'])));
    $estudios[$nivelKey] = $e;
}

// Experiencia
$experiencias = [];
$stmtExp = $conn->prepare("SELECT * FROM solicitud_empleo_experiencia WHERE id_solicitud = :id");
$stmtExp->execute([':id' => $id_solicitud]);
while ($ex = $stmtExp->fetch(PDO::FETCH_ASSOC)) {
    $experiencias[strtolower($ex['tipo_empleo'])] = $ex;
}

// Referencias
$referencias = [];
$stmtRef = $conn->prepare("SELECT * FROM solicitud_empleo_referencias WHERE id_solicitud = :id LIMIT 4");
$stmtRef->execute([':id' => $id_solicitud]);
$referencias = $stmtRef->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Empleo - Batidos Pitaya</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/global.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/solicitud_empleo.css?v=<?php echo time(); ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="loader-wrapper" id="loader">
        <div class="loader"></div>
    </div>

    <!-- Modal de Verificación 2FA -->
    <?php if (!$is_verified): ?>
        <div id="verification-overlay" class="verification-overlay">
            <div class="verification-card animate__animated animate__zoomIn">
                <div class="verification-icon">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h3>Verificación de Seguridad</h3>
                <p>Por favor, introduce el código de 6 dígitos que te proporcionó el equipo de Reclutamiento.</p>

                <div class="code-input-group">
                    <input type="text" maxlength="6" id="2fa-code" placeholder="000000" inputmode="numeric">
                </div>

                <button type="button" class="btn btn-primary w-100 mt-4" onclick="verifyAccessCode()">
                    Verificar Acceso
                </button>

                <div id="verification-error" class="alert alert-danger mt-3 d-none small"></div>
            </div>
        </div>
        <style>
            .verification-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(15, 23, 42, 0.95);
                backdrop-filter: blur(10px);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .verification-card {
                background: white;
                padding: 40px;
                border-radius: 24px;
                max-width: 450px;
                width: 100%;
                text-align: center;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            }

            .verification-icon {
                font-size: 3rem;
                color: #3b82f6;
                margin-bottom: 20px;
            }

            .code-input-group input {
                font-size: 2.5rem;
                text-align: center;
                letter-spacing: 12px;
                font-weight: 700;
                color: #1e293b;
                border-radius: 12px;
                border: 2px solid #e2e8f0;
                width: 100%;
                padding: 10px;
                font-family: 'Outfit', sans-serif;
            }

            .code-input-group input:focus {
                border-color: #3b82f6;
                outline: none;
                box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            }
        </style>
    <?php endif; ?>

    <header class="form-header">
        <div class="container">
            <div class="header-content">
                <img src="assets/img/logo.png" alt="Batidos Pitaya" class="logo">
                <div class="header-info">
                    <h1>Solicitud de Empleo</h1>
                    <p>Completa tu información para el proceso de contratación</p>
                </div>
            </div>
        </div>
    </header>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <!-- Stepper -->
                <div class="stepper-container mb-5">
                    <div class="stepper">
                        <div class="step active" data-step="1">
                            <div class="step-icon"><i class="bi bi-person"></i></div>
                            <div class="step-label">Personal</div>
                        </div>
                        <div class="step" data-step="2">
                            <div class="step-icon"><i class="bi bi-card-text"></i></div>
                            <div class="step-label">ID</div>
                        </div>
                        <div class="step" data-step="3">
                            <div class="step-icon"><i class="bi bi-people"></i></div>
                            <div class="step-label">Familia</div>
                        </div>
                        <div class="step" data-step="4">
                            <div class="step-icon"><i class="bi bi-mortarboard"></i></div>
                            <div class="step-label">Estudios</div>
                        </div>
                        <div class="step" data-step="5">
                            <div class="step-icon"><i class="bi bi-briefcase"></i></div>
                            <div class="step-label">Experiencia</div>
                        </div>
                        <div class="step" data-step="6">
                            <div class="step-icon"><i class="bi bi-file-earmark-arrow-up"></i></div>
                            <div class="step-label">Documentos</div>
                        </div>
                    </div>
                    <div class="progress-line">
                        <div class="progress-fill" style="width: 0%;"></div>
                    </div>
                </div>

                <form id="formSolicitud" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                    <!-- Sección 1: Datos Personales -->
                    <section class="form-section active" id="section-1">
                        <div class="section-title-wrapper">
                            <h2 class="section-title"><i class="bi bi-person-fill me-2"></i>Datos Personales</h2>
                            <p class="section-subtitle">Información general de contacto e identidad</p>
                        </div>

                        <div class="section-content">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Primer Nombre</label>
                                    <input type="text" class="form-control" name="nombre1" required
                                        value="<?php echo htmlspecialchars($solicitud['nombre1'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Segundo Nombre</label>
                                    <input type="text" class="form-control" name="nombre2"
                                        value="<?php echo htmlspecialchars($solicitud['nombre2'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Primer Apellido</label>
                                    <input type="text" class="form-control" name="apellido1" required
                                        value="<?php echo htmlspecialchars($solicitud['apellido1'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Segundo Apellido</label>
                                    <input type="text" class="form-control" name="apellido2"
                                        value="<?php echo htmlspecialchars($solicitud['apellido2'] ?? ''); ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Puesto Solicitado</label>
                                    <input type="text" class="form-control" name="puesto_solicitado" required
                                        value="<?php echo htmlspecialchars($solicitud['puesto_solicitado'] ?? $solicitud['nombre_cargo'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Expectativa Salarial (NIO)</label>
                                    <input type="number" class="form-control" name="expectativa_salarial" step="0.01"
                                        value="<?php echo htmlspecialchars($solicitud['expectativa_salarial'] ?? ''); ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Teléfono Fijo</label>
                                    <input type="tel" class="form-control" name="telefono_fijo"
                                        value="<?php echo htmlspecialchars($solicitud['telefono_fijo'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Teléfono Celular</label>
                                    <input type="tel" class="form-control" name="telefono_celular" required
                                        value="<?php echo htmlspecialchars($solicitud['telefono_celular'] ?? $solicitud['telefono'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" name="correo_electronico" required
                                        value="<?php echo htmlspecialchars($solicitud['correo_electronico'] ?? $solicitud['correo'] ?? ''); ?>">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Dirección Actual</label>
                                    <textarea class="form-control" name="direccion_actual" rows="2"
                                        required><?php echo htmlspecialchars($solicitud['direccion_actual'] ?? ''); ?></textarea>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" name="fecha_nacimiento" required
                                        value="<?php echo htmlspecialchars($solicitud['fecha_nacimiento'] ?? ''); ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Edad</label>
                                    <input type="number" class="form-control" name="edad" readonly
                                        value="<?php echo htmlspecialchars($solicitud['edad'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Lugar de Nacimiento</label>
                                    <input type="text" class="form-control" name="lugar_nacimiento"
                                        value="<?php echo htmlspecialchars($solicitud['lugar_nacimiento'] ?? ''); ?>">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Peso (lb)</label>
                                    <input type="number" class="form-control" name="peso" step="0.1"
                                        value="<?php echo htmlspecialchars($solicitud['peso'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Estatura (m)</label>
                                    <input type="number" class="form-control" name="estatura" step="0.01"
                                        value="<?php echo htmlspecialchars($solicitud['estatura'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Estado Civil</label>
                                    <select class="form-select" name="estado_civil">
                                        <option value="">Seleccione...</option>
                                        <option value="Soltero" <?php echo ($solicitud['estado_civil'] ?? '') === 'Soltero' ? 'selected' : ''; ?>>Soltero(a)</option>
                                        <option value="Casado" <?php echo ($solicitud['estado_civil'] ?? '') === 'Casado' ? 'selected' : ''; ?>>Casado(a)</option>
                                        <option value="Unión Libre" <?php echo ($solicitud['estado_civil'] ?? '') === 'Unión Libre' ? 'selected' : ''; ?>>Unión Libre</option>
                                        <option value="Divorciado" <?php echo ($solicitud['estado_civil'] ?? '') === 'Divorciado' ? 'selected' : ''; ?>>Divorciado(a)</option>
                                        <option value="Viudo" <?php echo ($solicitud['estado_civil'] ?? '') === 'Viudo' ? 'selected' : ''; ?>>Viudo(a)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tipo de Sangre</label>
                                    <input type="text" class="form-control" name="tipo_sangre"
                                        value="<?php echo htmlspecialchars($solicitud['tipo_sangre'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- SECCIÓN 2: Documentos de Identificación -->
                    <section class="form-section" id="section-2">
                        <div class="section-title-wrapper">
                            <h2 class="section-title"><i class="bi bi-card-text me-2"></i>Documentos de Identificación
                            </h2>
                            <p class="section-subtitle">II. Información de tus documentos oficiales</p>
                        </div>
                        <div class="section-content">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">N° Cédula</label>
                                    <input type="text" class="form-control" name="numero_cedula"
                                        value="<?php echo htmlspecialchars($solicitud['numero_cedula'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Lugar y Fecha de Emisión</label>
                                    <input type="text" class="form-control" name="lugar_emision_cedula"
                                        placeholder="Ej: Managua, 01/01/2020"
                                        value="<?php echo htmlspecialchars($solicitud['lugar_emision_cedula'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha de Emisión Cédula</label>
                                    <input type="date" class="form-control" name="fecha_emision_cedula"
                                        value="<?php echo htmlspecialchars($solicitud['fecha_emision_cedula'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">N° INSS</label>
                                    <input type="text" class="form-control" name="numero_inss"
                                        value="<?php echo htmlspecialchars($solicitud['numero_inss'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">N° Serie Licencia</label>
                                    <input type="text" class="form-control" name="numero_serie_licencia"
                                        value="<?php echo htmlspecialchars($solicitud['numero_serie_licencia'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- SECCIÓN 3: Datos Familiares -->
                    <section class="form-section" id="section-3">
                        <div class="section-title-wrapper">
                            <h2 class="section-title"><i class="bi bi-people me-2"></i>Datos Familiares</h2>
                            <p class="section-subtitle">III. Información de tus familiares directos e hijos</p>
                        </div>
                        <div class="section-content">
                            <h6 class="fw-bold mb-3">Grupo Familiar</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered align-middle" id="tablaFamiliares">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Parentesco</th>
                                            <th>Nombre</th>
                                            <th>Ocupación</th>
                                            <th>Lugar de Trabajo / Tel.</th>
                                            <th>¿Depende de Ud.?</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (['Padre', 'Madre', 'Cónyuge', 'Tutor'] as $parentesco): ?>
                                            <?php
                                            $pk = strtolower(str_replace(['ó', 'ñ'], ['o', 'n'], $parentesco));
                                            $fam = $familiares[$pk] ?? [];
                                            ?>
                                            <tr>
                                                <td class="text-muted fw-semibold" style="width:100px">
                                                    <?php echo $parentesco; ?>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm"
                                                        name="familiar_nombre_<?php echo $pk; ?>"
                                                        value="<?php echo htmlspecialchars($fam['nombre_familiar'] ?? ''); ?>">
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm"
                                                        name="familiar_ocupacion_<?php echo $pk; ?>"
                                                        value="<?php echo htmlspecialchars($fam['ocupacion_familiar'] ?? ''); ?>">
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm"
                                                        name="familiar_trabajo_<?php echo $pk; ?>"
                                                        value="<?php echo htmlspecialchars($fam['lugar_trabajo_familiar'] ?? ''); ?>">
                                                </td>
                                                <td class="text-center">
                                                    <select class="form-select form-select-sm"
                                                        name="familiar_depende_<?php echo $pk; ?>">
                                                        <option value="No" <?php echo ($fam['depende_economicamente'] ?? 0) == 0 ? 'selected' : ''; ?>>No</option>
                                                        <option value="Si" <?php echo ($fam['depende_economicamente'] ?? 0) == 1 ? 'selected' : ''; ?>>Sí</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <h6 class="fw-bold mb-3">Hijos</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered align-middle" id="tablaHijos">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Fecha Nac.</th>
                                            <th>Edad</th>
                                            <th>Sexo</th>
                                            <th>Estudio</th>
                                            <th>¿Depende de Ud.?</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for ($h = 0; $h < 4; $h++): ?>
                                            <?php $hijo = $hijos[$h] ?? []; ?>
                                            <tr>
                                                <td><input type="text" class="form-control form-control-sm"
                                                        name="hijo_nombre_<?php echo $h + 1; ?>"
                                                        value="<?php echo htmlspecialchars($hijo['nombre_hijo'] ?? ''); ?>">
                                                </td>
                                                <td><input type="date" class="form-control form-control-sm"
                                                        name="hijo_fecha_nac_<?php echo $h + 1; ?>"
                                                        value="<?php echo htmlspecialchars($hijo['fecha_nacimiento_hijo'] ?? ''); ?>">
                                                </td>
                                                <td><input type="number" class="form-control form-control-sm"
                                                        name="hijo_edad_<?php echo $h + 1; ?>" min="0" max="99"
                                                        value="<?php echo htmlspecialchars($hijo['edad_hijo'] ?? ''); ?>">
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm"
                                                        name="hijo_sexo_<?php echo $h + 1; ?>">
                                                        <option value="">-</option>
                                                        <option value="M" <?php echo ($hijo['sexo_hijo'] ?? '') === 'M' ? 'selected' : ''; ?>>M</option>
                                                        <option value="F" <?php echo ($hijo['sexo_hijo'] ?? '') === 'F' ? 'selected' : ''; ?>>F</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm"
                                                        name="hijo_estudio_<?php echo $h + 1; ?>"
                                                        value="<?php echo htmlspecialchars($hijo['estudios_hijo'] ?? ''); ?>">
                                                </td>
                                                <td class="text-center">
                                                    <select class="form-select form-select-sm"
                                                        name="hijo_depende_<?php echo $h + 1; ?>">
                                                        <option value="No" <?php echo ($hijo['depende_economicamente_hijo'] ?? 0) == 0 ? 'selected' : ''; ?>>No</option>
                                                        <option value="Si" <?php echo ($hijo['depende_economicamente_hijo'] ?? 0) == 1 ? 'selected' : ''; ?>>Sí</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">¿Tiene familiares dentro de la empresa?</label>
                                    <select class="form-select" name="tiene_familiares_empresa">
                                        <option value="0" <?php echo ($solicitud['tiene_familiares_empresa'] ?? 0) == 0 ? 'selected' : ''; ?>>No</option>
                                        <option value="1" <?php echo ($solicitud['tiene_familiares_empresa'] ?? 0) == 1 ? 'selected' : ''; ?>>Sí</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nombre del familiar</label>
                                    <input type="text" class="form-control" name="familiar_empresa_nombre"
                                        value="<?php echo htmlspecialchars($solicitud['familiar_empresa_nombre'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Dónde trabaja</label>
                                    <input type="text" class="form-control" name="familiar_empresa_lugar"
                                        value="<?php echo htmlspecialchars($solicitud['familiar_empresa_lugar'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- SECCIÓN 4: Estudios y Conocimientos -->
                    <section class="form-section" id="section-4">
                        <div class="section-title-wrapper">
                            <h2 class="section-title"><i class="bi bi-mortarboard me-2"></i>Estudios y Conocimientos
                            </h2>
                            <p class="section-subtitle">IV & V. Nivel educativo y conocimientos especializados</p>
                        </div>
                        <div class="section-content">
                            <h6 class="fw-bold mb-3">IV. Estudios</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nivel</th>
                                            <th>Nombre del Lugar de Estudios</th>
                                            <th>Grado Cursado</th>
                                            <th>Año</th>
                                            <th>Título o Diploma</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (['Bachillerato', 'Técnico u Otros', 'Universitario', 'Post Universitario'] as $nivel): ?>
                                            <?php
                                            $nk = str_replace(' ', '_', mb_strtolower(preg_replace('/[^a-z0-9 ]/i', '', $nivel)));
                                            $est = $estudios[$nk] ?? [];
                                            ?>
                                            <tr>
                                                <td class="text-muted fw-semibold" style="min-width:130px">
                                                    <?php echo $nivel; ?>
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm"
                                                        name="estudio_lugar_<?php echo $nk; ?>"
                                                        value="<?php echo htmlspecialchars($est['lugar_estudio'] ?? ''); ?>">
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm"
                                                        name="estudio_grado_<?php echo $nk; ?>"
                                                        value="<?php echo htmlspecialchars($est['grado_cursado'] ?? ''); ?>">
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm"
                                                        name="estudio_ano_<?php echo $nk; ?>" style="width:80px"
                                                        value="<?php echo htmlspecialchars($est['año_estudio'] ?? ''); ?>">
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm"
                                                        name="estudio_titulo_<?php echo $nk; ?>"
                                                        value="<?php echo htmlspecialchars($est['titulo_obtenido'] ?? ''); ?>">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row g-3 mb-4">
                                <?php
                                // Buscar si algún registro de estudios indica que está estudiando actualmente
                                $estActual = [];
                                foreach ($estudios as $e) {
                                    if (($e['estudiando_actualmente'] ?? 0) == 1) {
                                        $estActual = $e;
                                        break;
                                    }
                                }
                                ?>
                                <div class="col-md-3">
                                    <label class="form-label">¿Estudia actualmente?</label>
                                    <select class="form-select" name="estudiando_actualmente">
                                        <option value="0" <?php echo (($estActual['estudiando_actualmente'] ?? 0) == 0) ? 'selected' : ''; ?>>No</option>
                                        <option value="1" <?php echo (($estActual['estudiando_actualmente'] ?? 0) == 1) ? 'selected' : ''; ?>>Sí</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Curso o Carrera</label>
                                    <input type="text" class="form-control" name="curso_actual"
                                        value="<?php echo htmlspecialchars($estActual['curso_actual'] ?? ''); ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Nivel</label>
                                    <input type="text" class="form-control" name="nivel_actual"
                                        value="<?php echo htmlspecialchars($estActual['nivel_actual'] ?? ''); ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Horario</label>
                                    <input type="text" class="form-control" name="horario_estudio"
                                        value="<?php echo htmlspecialchars($estActual['horario_estudio'] ?? ''); ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Dónde</label>
                                    <input type="text" class="form-control" name="lugar_estudio_actual"
                                        value="<?php echo htmlspecialchars($estActual['lugar_estudio_actual'] ?? ''); ?>">
                                </div>
                            </div>

                            <h6 class="fw-bold mb-3">V. Conocimientos Especializados</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Contables, Finanzas, Paquetes utilitarios, etc.</label>
                                    <textarea class="form-control" name="conocimientos_especializados" rows="3"
                                        placeholder="Detalle sus conocimientos..."><?php echo htmlspecialchars($solicitud['conocimientos_especializados'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">¿Qué idiomas lee, escribe y habla además del
                                        español?</label>
                                    <textarea class="form-control" name="idiomas" rows="3"
                                        placeholder="Ej: Inglés (avanzado), Francés (básico)..."><?php echo htmlspecialchars($solicitud['idiomas'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- SECCIÓN 5: Experiencia, Salud y Referencias -->
                    <section class="form-section" id="section-5">
                        <div class="section-title-wrapper">
                            <h2 class="section-title"><i class="bi bi-briefcase me-2"></i>Experiencia, Salud y
                                Referencias</h2>
                            <p class="section-subtitle">VI, VII, VIII & IX. Aficiones, salud, experiencia laboral y
                                referencias</p>
                        </div>
                        <div class="section-content">

                            <h6 class="fw-bold mb-2">VI. Aficiones (a qué dedica su tiempo libre)</h6>
                            <textarea class="form-control mb-4" name="aficiones" rows="2"
                                placeholder="Ej: Lectura, deporte, música..."><?php echo htmlspecialchars($solicitud['aficiones'] ?? ''); ?></textarea>

                            <h6 class="fw-bold mb-3">VII. Estado de Salud</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label">¿Practica algún deporte?</label>
                                    <select class="form-select" name="practica_deporte">
                                        <option value="0" <?php echo ($solicitud['practica_deporte'] ?? 0) == 0 ? 'selected' : ''; ?>>No</option>
                                        <option value="1" <?php echo ($solicitud['practica_deporte'] ?? 0) == 1 ? 'selected' : ''; ?>>Sí</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">¿Cuáles?</label>
                                    <input type="text" class="form-control" name="deportes_cuales"
                                        value="<?php echo htmlspecialchars($solicitud['deportes_cuales'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">¿Padece alguna enfermedad?</label>
                                    <select class="form-select" name="padece_enfermedad">
                                        <option value="0" <?php echo ($solicitud['padece_enfermedad'] ?? 0) == 0 ? 'selected' : ''; ?>>No</option>
                                        <option value="1" <?php echo ($solicitud['padece_enfermedad'] ?? 0) == 1 ? 'selected' : ''; ?>>Sí</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">¿Cuáles?</label>
                                    <input type="text" class="form-control" name="enfermedades_cuales"
                                        value="<?php echo htmlspecialchars($solicitud['enfermedades_cuales'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">¿Usa lentes?</label>
                                    <select class="form-select" name="usa_lentes">
                                        <option value="0" <?php echo ($solicitud['usa_lentes'] ?? 0) == 0 ? 'selected' : ''; ?>>No</option>
                                        <option value="1" <?php echo ($solicitud['usa_lentes'] ?? 0) == 1 ? 'selected' : ''; ?>>Sí</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">¿Padece alguna capacidad especial?</label>
                                    <select class="form-select" name="padece_capacidad_especial">
                                        <option value="0" <?php echo ($solicitud['padece_capacidad_especial'] ?? 0) == 0 ? 'selected' : ''; ?>>No</option>
                                        <option value="1" <?php echo ($solicitud['padece_capacidad_especial'] ?? 0) == 1 ? 'selected' : ''; ?>>Sí</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">¿Cuál?</label>
                                    <input type="text" class="form-control" name="capacidad_especial_cual"
                                        value="<?php echo htmlspecialchars($solicitud['capacidad_especial_cual'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">¿Hospitalizado últimos 6 meses?</label>
                                    <select class="form-select" name="hospitalizado_ultimos_6_meses">
                                        <option value="0" <?php echo ($solicitud['hospitalizado_ultimos_6_meses'] ?? 0) == 0 ? 'selected' : ''; ?>>No</option>
                                        <option value="1" <?php echo ($solicitud['hospitalizado_ultimos_6_meses'] ?? 0) == 1 ? 'selected' : ''; ?>>Sí</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">¿Por qué? (si fue hospitalizado)</label>
                                    <input type="text" class="form-control" name="razon_hospitalizacion"
                                        value="<?php echo htmlspecialchars($solicitud['razon_hospitalizacion'] ?? ''); ?>">
                                </div>
                            </div>

                            <h6 class="fw-bold mb-3">VIII. Experiencia Laboral</h6>
                            <?php foreach (['Ultimo' => 'Último Empleo', 'Penultimo' => 'Penúltimo Empleo', 'Antepenultimo' => 'Antepenúltimo Empleo'] as $tipo => $label): ?>
                                <?php
                                $tk = strtolower($tipo);
                                $exp = $experiencias[$tk] ?? [];
                                ?>
                                <div class="card bg-light border-0 mb-3">
                                    <div class="card-body">
                                        <h6 class="fw-semibold mb-3 text-primary"><?php echo $label; ?></h6>
                                        <div class="row g-3">
                                            <div class="col-md-6"><label class="form-label">Empresa</label>
                                                <input type="text" class="form-control"
                                                    name="exp_empresa_<?php echo $tk; ?>"
                                                    value="<?php echo htmlspecialchars($exp['empresa'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-6"><label class="form-label">Dirección</label>
                                                <input type="text" class="form-control"
                                                    name="exp_direccion_<?php echo $tk; ?>"
                                                    value="<?php echo htmlspecialchars($exp['direccion'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-4"><label class="form-label">Teléfono</label>
                                                <input type="tel" class="form-control"
                                                    name="exp_telefono_<?php echo $tk; ?>"
                                                    value="<?php echo htmlspecialchars($exp['telefono'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-4"><label class="form-label">Jefe Inmediato</label>
                                                <input type="text" class="form-control" name="exp_jefe_<?php echo $tk; ?>"
                                                    value="<?php echo htmlspecialchars($exp['jefe_inmediato'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-2"><label class="form-label">Desde</label>
                                                <input type="date" class="form-control" name="exp_desde_<?php echo $tk; ?>"
                                                    value="<?php echo htmlspecialchars($exp['fecha_inicio'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-2"><label class="form-label">Hasta</label>
                                                <input type="date" class="form-control" name="exp_hasta_<?php echo $tk; ?>"
                                                    value="<?php echo htmlspecialchars($exp['fecha_fin'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-6"><label class="form-label">Puesto</label>
                                                <input type="text" class="form-control" name="exp_puesto_<?php echo $tk; ?>"
                                                    value="<?php echo htmlspecialchars($exp['puesto_desempenado'] ?? ''); ?>">
                                            </div>
                                            <div class="col-md-6"><label class="form-label">Sueldo (C$)</label>
                                                <input type="number" class="form-control"
                                                    name="exp_sueldo_<?php echo $tk; ?>" step="0.01"
                                                    value="<?php echo htmlspecialchars($exp['sueldo'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">¿Cuáles son sus áreas de interés para
                                    trabajar?</label>
                                <textarea class="form-control" name="areas_interes"
                                    rows="2"><?php echo htmlspecialchars($solicitud['areas_interes'] ?? ''); ?></textarea>
                            </div>

                            <h6 class="fw-bold mb-3">Información Adicional</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">¿Tiene pariente o amigo en la empresa?</label>
                                    <select class="form-select" name="tiene_pariente_amigo_empresa">
                                        <option value="0" <?php echo ($solicitud['tiene_pariente_amigo_empresa'] ?? 0) == 0 ? 'selected' : ''; ?>>No</option>
                                        <option value="1" <?php echo ($solicitud['tiene_pariente_amigo_empresa'] ?? 0) == 1 ? 'selected' : ''; ?>>Sí</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" name="pariente_amigo_nombre"
                                        value="<?php echo htmlspecialchars($solicitud['pariente_amigo_nombre'] ?? ''); ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Puesto</label>
                                    <input type="text" class="form-control" name="pariente_amigo_puesto"
                                        value="<?php echo htmlspecialchars($solicitud['pariente_amigo_puesto'] ?? ''); ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tipo</label>
                                    <select class="form-select" name="pariente_amigo_tipo">
                                        <option value="">-</option>
                                        <option value="Pariente" <?php echo ($solicitud['pariente_amigo_tipo'] ?? '') === 'Pariente' ? 'selected' : ''; ?>>Pariente</option>
                                        <option value="Amigo" <?php echo ($solicitud['pariente_amigo_tipo'] ?? '') === 'Amigo' ? 'selected' : ''; ?>>Amigo</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">¿Ha ocupado cargo público (últimos 8 años)?</label>
                                    <select class="form-select" name="ha_ocupado_cargo_publico">
                                        <option value="0" <?php echo ($solicitud['ha_ocupado_cargo_publico'] ?? 0) == 0 ? 'selected' : ''; ?>>No</option>
                                        <option value="1" <?php echo ($solicitud['ha_ocupado_cargo_publico'] ?? 0) == 1 ? 'selected' : ''; ?>>Sí</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Cargo desempeñado</label>
                                    <input type="text" class="form-control" name="cargo_publico_desempenado"
                                        value="<?php echo htmlspecialchars($solicitud['cargo_publico_desempenado'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Período</label>
                                    <input type="text" class="form-control" name="periodo_cargo_publico"
                                        value="<?php echo htmlspecialchars($solicitud['periodo_cargo_publico'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">¿Familiar ocupa cargo público?</label>
                                    <select class="form-select" name="familiar_cargo_publico">
                                        <option value="0" <?php echo ($solicitud['familiar_cargo_publico'] ?? 0) == 0 ? 'selected' : ''; ?>>No</option>
                                        <option value="1" <?php echo ($solicitud['familiar_cargo_publico'] ?? 0) == 1 ? 'selected' : ''; ?>>Sí</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nombre del funcionario</label>
                                    <input type="text" class="form-control" name="familiar_cargo_publico_nombre"
                                        value="<?php echo htmlspecialchars($solicitud['familiar_cargo_publico_nombre'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Período</label>
                                    <input type="text" class="form-control" name="familiar_cargo_publico_periodo"
                                        value="<?php echo htmlspecialchars($solicitud['familiar_cargo_publico_periodo'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Cargo desempeñado</label>
                                    <input type="text" class="form-control" name="familiar_cargo_publico_cargo"
                                        value="<?php echo htmlspecialchars($solicitud['familiar_cargo_publico_cargo'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">¿Cargo directivo en partido político?</label>
                                    <select class="form-select" name="cargo_directivo_partido_politico">
                                        <option value="0" <?php echo ($solicitud['cargo_directivo_partido_politico'] ?? 0) == 0 ? 'selected' : ''; ?>>No</option>
                                        <option value="1" <?php echo ($solicitud['cargo_directivo_partido_politico'] ?? 0) == 1 ? 'selected' : ''; ?>>Sí</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Nombre del partido/funcionario</label>
                                    <input type="text" class="form-control" name="partido_politico_nombre"
                                        value="<?php echo htmlspecialchars($solicitud['partido_politico_nombre'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Período</label>
                                    <input type="text" class="form-control" name="partido_politico_periodo"
                                        value="<?php echo htmlspecialchars($solicitud['partido_politico_periodo'] ?? ''); ?>">
                                </div>
                            </div>

                            <h6 class="fw-bold mb-3">IX. Referencias <small class="text-muted fw-normal">(no incluya
                                    parientes)</small></h6>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Dirección</th>
                                            <th>Tel. Casa/Celular</th>
                                            <th>Tel. Oficina</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for ($r = 0; $r < 4; $r++): ?>
                                            <?php $ref = $referencias[$r] ?? []; ?>
                                            <tr>
                                                <td><input type="text" class="form-control form-control-sm"
                                                        name="ref_nombre_<?php echo $r + 1; ?>"
                                                        value="<?php echo htmlspecialchars($ref['nombre_referencia'] ?? ''); ?>">
                                                </td>
                                                <td><input type="text" class="form-control form-control-sm"
                                                        name="ref_direccion_<?php echo $r + 1; ?>"
                                                        value="<?php echo htmlspecialchars($ref['direccion_referencia'] ?? ''); ?>">
                                                </td>
                                                <td><input type="tel" class="form-control form-control-sm"
                                                        name="ref_tel_casa_<?php echo $r + 1; ?>"
                                                        value="<?php echo htmlspecialchars($ref['telefono_casa_referencia'] ?? ''); ?>">
                                                </td>
                                                <td><input type="tel" class="form-control form-control-sm"
                                                        name="ref_tel_oficina_<?php echo $r + 1; ?>"
                                                        value="<?php echo htmlspecialchars($ref['telefono_oficina_referencia'] ?? ''); ?>">
                                                </td>
                                            </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    <!-- SECCIÓN 6: Documentos Adjuntos -->
                    <section class="form-section" id="section-6">
                        <div class="section-title-wrapper">
                            <h2 class="section-title"><i class="bi bi-file-earmark-arrow-up me-2"></i>Documentos
                                Adjuntos</h2>
                            <p class="section-subtitle">Sube tus documentos de respaldo para finalizar la solicitud</p>
                        </div>
                        <div class="section-content">
                            <div class="alert alert-info small">
                                <i class="bi bi-info-circle me-1"></i>
                                Sube los documentos en formato PDF o imagen (JPG, PNG). Tamaño máximo: 5 MB por archivo.
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Fotocopia de Cédula</label>
                                    <?php if (!empty($solicitud['adjunto_cedula'])): ?>
                                        <div class="alert alert-success py-2 small mb-2"><i
                                                class="bi bi-check-circle me-1"></i>Archivo ya subido. <a
                                                href="uploads/adjuntos/<?php echo htmlspecialchars($solicitud['adjunto_cedula']); ?>"
                                                target="_blank">Ver</a></div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="adjunto_cedula"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Record Policial (Ley 510)</label>
                                    <?php if (!empty($solicitud['adjunto_record_ley510'])): ?>
                                        <div class="alert alert-success py-2 small mb-2"><i
                                                class="bi bi-check-circle me-1"></i>Archivo ya subido. <a
                                                href="uploads/adjuntos/<?php echo htmlspecialchars($solicitud['adjunto_record_ley510']); ?>"
                                                target="_blank">Ver</a></div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="adjunto_record_ley510"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Constancia Judicial</label>
                                    <?php if (!empty($solicitud['adjunto_constancia_judicial'])): ?>
                                        <div class="alert alert-success py-2 small mb-2"><i
                                                class="bi bi-check-circle me-1"></i>Archivo ya subido. <a
                                                href="uploads/adjuntos/<?php echo htmlspecialchars($solicitud['adjunto_constancia_judicial']); ?>"
                                                target="_blank">Ver</a></div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="adjunto_constancia_judicial"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Certificado de Salud</label>
                                    <?php if (!empty($solicitud['adjunto_certificado_salud'])): ?>
                                        <div class="alert alert-success py-2 small mb-2"><i
                                                class="bi bi-check-circle me-1"></i>Archivo ya subido. <a
                                                href="uploads/adjuntos/<?php echo htmlspecialchars($solicitud['adjunto_certificado_salud']); ?>"
                                                target="_blank">Ver</a></div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="adjunto_certificado_salud"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>
                            <div class="alert alert-warning mt-4 small">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                <strong>Declaración:</strong> Al finalizar, declaro que toda la información de esta
                                solicitud es verdadera y autorizo a que sea investigada para los fines que la empresa
                                estime convenientes.
                            </div>
                        </div>
                    </section>

                    <!-- Botonera de Navegación -->
                    <div class="form-navigation d-flex justify-content-between align-items-center flex-wrap gap-3 mt-5">
                        <button type="button" class="btn btn-outline-secondary btn-lg btn-nav" id="btnPrev" disabled>
                            <i class="bi bi-chevron-left"></i> Anterior
                        </button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light btn-lg btn-nav" id="btnSaveDraft">
                                <i class="bi bi-save"></i> Guardar Progreso
                            </button>
                            <button type="button" class="btn btn-primary btn-lg btn-nav" id="btnNext">
                                Siguiente <i class="bi bi-chevron-right"></i>
                            </button>
                            <button type="submit" class="btn btn-success btn-lg btn-nav d-none" id="btnSubmit">
                                <i class="bi bi-check-circle"></i> Finalizar Solicitud
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="form-footer py-4 mt-auto">
        <div class="container text-center">
            <p class="mb-0">©
                <?php echo date('Y'); ?> Batidos Pitaya. Todos los derechos reservados.
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/solicitud_empleo.js?v=<?php echo time(); ?>"></script>
</body>

</html>