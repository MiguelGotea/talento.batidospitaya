<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postular a Plaza - Batidos Pitaya</title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon -->
    <link rel="icon" href="/assets/img/favicon.png" type="image/png">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/global.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/css/postulacion.css?v=<?php echo time(); ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <!-- Header -->
    <header class="postulacion-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="/unete" class="btn-volver">
                    <i class="bi bi-arrow-left"></i> Volver a vacantes
                </a>
                <div class="logo">
                    <img src="/assets/img/logo.png" alt="Batidos Pitaya" style="height: 48px;">
                </div>
            </div>
        </div>
    </header>

    <!-- Formulario de Postulación -->
    <section class="formulario-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <!-- Información de la Plaza -->
                    <div class="plaza-info-card mb-4" id="plazaInfo">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card del Formulario -->
                    <div class="formulario-card">
                        <div class="formulario-header">
                            <!-- <h2 class="formulario-titulo">
                                <i class="bi bi-file-person"></i>
                                Postular a esta Plaza
                            </h2> -->
                            <p class="formulario-subtitulo">
                                Completa tus datos para iniciar el proceso de selección
                            </p>
                            <div class="mt-3">
                                <a href="https://wa.me/50588520629" target="_blank" rel="noopener noreferrer" class="btn btn-success btn-sm d-inline-flex align-items-center gap-2" style="background-color: #25D366; border-color: #25D366;">
                                    <i class="bi bi-whatsapp"></i> ¿Tienes dudas? Escríbenos por WhatsApp
                                </a>
                            </div>
                        </div>

                        <form id="formPostulacion" enctype="multipart/form-data">
                            <!-- Campos ocultos -->
                            <input type="hidden" id="plazaId" name="plaza_id">
                            <input type="hidden" id="cargoId" name="cargo_id">
                            <input type="hidden" id="sucursalId" name="sucursal_id">

                            <!-- Información Personal -->
                            <div class="form-section">
                                <h5 class="section-title">
                                    <i class="bi bi-person-circle"></i>
                                    Información Personal
                                </h5>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="nombre" class="form-label">Nombre Completo <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required
                                            placeholder="Ej: Juan Pérez García">
                                        <div class="invalid-feedback">Por favor ingresa tu nombre completo</div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="direccion" class="form-label">Dirección <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="direccion" name="direccion" required
                                            placeholder="Ej: Bo. San Juan, 2c al sur, Managua">
                                        <div class="invalid-feedback">Por favor ingresa tu dirección</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="correo" class="form-label">Correo Electrónico <small
                                                class="text-muted">(Opcional)</small></label>
                                        <input type="email" class="form-control" id="correo" name="correo"
                                            placeholder="correo@ejemplo.com">
                                        <div class="invalid-feedback">Ingresa un correo válido</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="telefono" class="form-label">Celular WhatsApp <span
                                                class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="telefono" name="telefono" required
                                            placeholder="+505 8888-8888">
                                        <div class="invalid-feedback">Ingresa un teléfono válido</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Experiencia y Aspiraciones -->
                            <div class="form-section">
                                <h5 class="section-title">
                                    <i class="bi bi-briefcase"></i>
                                    Experiencia y Aspiraciones
                                </h5>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="experiencia" class="form-label">Experiencia Laboral <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control" id="experiencia" name="experiencia" rows="4"
                                            required
                                            placeholder="Describe brevemente tu experiencia laboral relevante..."></textarea>
                                        <div class="invalid-feedback">Por favor describe tu experiencia</div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="aspiracion" class="form-label">Aspiración Salarial (Córdobas) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="aspiracion" name="aspiracion"
                                            required min="0" step="0.01" placeholder="Ej: 15000">
                                        <div class="invalid-feedback">Ingresa tu aspiración salarial</div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="comentario" class="form-label">Comentarios Adicionales
                                            (Opcional)</label>
                                        <textarea class="form-control" id="comentario" name="comentario" rows="3"
                                            placeholder="¿Por qué quieres trabajar en Batidos Pitaya?"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Currículum Vitae -->
                            <div class="form-section">
                                <h5 class="section-title">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                    Currículum Vitae
                                </h5>

                                <div class="upload-zone" id="uploadZone">
                                    <input type="file" id="cv" name="cv" accept=".pdf" hidden>
                                    <div class="upload-content">
                                        <i class="bi bi-cloud-upload upload-icon"></i>
                                        <h6 class="upload-title">Arrastra tu CV aquí</h6>
                                        <p class="upload-subtitle">o haz clic para seleccionar</p>
                                        <small class="text-muted">Solo archivos PDF, máximo 25MB</small>
                                    </div>
                                    <div class="upload-preview" id="uploadPreview" style="display: none;">
                                        <i class="bi bi-file-pdf-fill text-danger" style="font-size: 3rem;"></i>
                                        <div class="file-info">
                                            <p class="file-name mb-1" id="fileName"></p>
                                            <small class="file-size text-muted" id="fileSize"></small>
                                        </div>
                                        <button type="button" class="btn-remove-file" id="btnRemoveFile">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="invalid-feedback" id="cvError"></div>
                            </div>

                            <!-- Términos y Condiciones -->
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="terminos" required>
                                <label class="form-check-label" for="terminos">
                                    Acepto que mis datos sean procesados para fines de reclutamiento <span
                                        class="text-danger">*</span>
                                </label>
                                <div class="invalid-feedback">Debes aceptar los términos</div>
                            </div>

                            <!-- Botones -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="btnEnviar">
                                    <i class="bi bi-send-fill"></i>
                                    Enviar Postulación
                                </button>
                                <a href="/unete" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i>
                                    Cancelar
                                </a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <footer class="footer-simple">
        <div class="container text-center">
            <p class="mb-2">
                <i class="bi bi-envelope"></i> seleccion@batidospitaya.com |
                <i class="bi bi-telephone"></i> <a href="https://wa.me/50588520629" target="_blank" rel="noopener noreferrer" class="text-secondary text-decoration-none">+505 8852 0629</a>
            </p>
            <p>&copy; Batidos Pitaya. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/js/postulacion.js?v=<?php echo time(); ?>"></script>

</body>

</html>