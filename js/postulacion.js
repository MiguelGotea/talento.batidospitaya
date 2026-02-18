/**
 * JavaScript del Formulario de Postulación
 * talento.batidospitaya.com
 */

let plazaData = null;
let archivoCV = null;

// ==================== Inicialización ====================
$(document).ready(function () {
    cargarDatosPlaza();
    inicializarUploadZone();
    inicializarFormulario();
});

/**
 * Cargar datos de la plaza desde URL params
 */
async function cargarDatosPlaza() {
    const urlParams = new URLSearchParams(window.location.search);
    const plazaId = urlParams.get('plaza');
    const cargoId = urlParams.get('cargo');
    const sucursalId = urlParams.get('sucursal');

    if (!plazaId || !cargoId) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se especificó la plaza a postular',
            confirmButtonColor: '#51B8AC'
        }).then(() => {
            window.location.href = 'index.php';
        });
        return;
    }

    // Guardar IDs en campos ocultos
    $('#plazaId').val(plazaId);
    $('#cargoId').val(cargoId);
    $('#sucursalId').val(sucursalId || '');

    try {
        const response = await fetch(`ajax/get_plaza_detalle.php?id=${plazaId}`);
        const data = await response.json();

        if (data.success) {
            plazaData = data.plaza;
            mostrarInfoPlaza();
        } else {
            throw new Error(data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar la información de la plaza',
            confirmButtonColor: '#51B8AC'
        }).then(() => {
            window.location.href = 'index.php';
        });
    }
}

/**
 * Mostrar información de la plaza
 */
function mostrarInfoPlaza() {
    const html = `
        <div class="plaza-info-titulo">${plazaData.cargo_nombre}</div>
        <div class="plaza-info-detalles">
            <div class="detalle-item">
                <i class="bi bi-geo-alt-fill"></i>
                <span>${plazaData.departamento} - ${plazaData.sucursal_nombre}</span>
            </div>
            <div class="detalle-item">
                <i class="bi bi-cash-coin"></i>
                <span>${formatearMoneda(plazaData.salario_propuesto)}</span>
            </div>
            <div class="detalle-item">
                <i class="bi bi-people-fill"></i>
                <span>${plazaData.plazas_disponibles} ${plazaData.plazas_disponibles === 1 ? 'plaza' : 'plazas'}</span>
            </div>
            <div class="detalle-item">
                <i class="bi bi-tag-fill"></i>
                <span>${plazaData.especialidad_area}</span>
            </div>
        </div>
    `;
    $('#plazaInfo').html(html);
}

/**
 * Inicializar zona de upload
 */
function inicializarUploadZone() {
    const uploadZone = $('#uploadZone');
    const fileInput = $('#cv');

    // Click en zona de upload
    uploadZone.on('click', function (e) {
        // Prevenir que el click en el preview abra el selector
        if ($(e.target).closest('#uploadPreview').length) {
            return;
        }

        // Prevenir que el click en el input file cause loop
        if (e.target === fileInput[0]) {
            return;
        }

        // Abrir selector de archivos usando el método nativo
        fileInput[0].click();
    });

    // Cambio de archivo
    fileInput.on('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            validarYMostrarArchivo(file);
        }
    });

    // Drag & Drop
    uploadZone.on('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dragover');
    });

    uploadZone.on('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
    });

    uploadZone.on('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');

        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            validarYMostrarArchivo(file);
        }
    });

    // Botón remover archivo
    $('#btnRemoveFile').on('click', function (e) {
        e.stopPropagation();
        removerArchivo();
    });
}

/**
 * Validar y mostrar archivo seleccionado
 */
function validarYMostrarArchivo(file) {
    // Validar tipo
    if (file.type !== 'application/pdf') {
        mostrarErrorCV('Solo se permiten archivos PDF');
        return;
    }

    // Validar tamaño (10MB)
    const maxSize = 10 * 1024 * 1024; // 10MB en bytes
    if (file.size > maxSize) {
        mostrarErrorCV('El archivo no debe superar los 10MB');
        return;
    }

    // Archivo válido
    archivoCV = file;
    mostrarPreviewArchivo(file);
    limpiarErrorCV();
}

/**
 * Mostrar preview del archivo
 */
function mostrarPreviewArchivo(file) {
    $('#fileName').text(file.name);
    $('#fileSize').text(formatearTamanio(file.size));
    $('.upload-content').hide();
    $('#uploadPreview').show();
}

/**
 * Remover archivo
 */
function removerArchivo() {
    archivoCV = null;
    $('#cv').val('');
    $('#uploadPreview').hide();
    $('.upload-content').show();
    limpiarErrorCV();
}

/**
 * Mostrar error de CV
 */
function mostrarErrorCV(mensaje) {
    $('#cvError').text(mensaje).show();
    $('#uploadZone').addClass('is-invalid');
}

/**
 * Limpiar error de CV
 */
function limpiarErrorCV() {
    $('#cvError').text('').hide();
    $('#uploadZone').removeClass('is-invalid');
}

/**
 * Inicializar formulario
 */
function inicializarFormulario() {
    const form = $('#formPostulacion');

    // Validación en tiempo real
    form.find('input, textarea, select').on('blur', function () {
        validarCampo($(this));
    });

    // Submit del formulario
    form.on('submit', function (e) {
        e.preventDefault();

        if (validarFormulario()) {
            enviarPostulacion();
        }
    });
}

/**
 * Validar campo individual
 */
function validarCampo(campo) {
    const valor = campo.val().trim();
    const tipo = campo.attr('type');
    const requerido = campo.prop('required');

    let valido = true;

    if (requerido && !valor) {
        valido = false;
    } else if (tipo === 'email' && valor) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        valido = emailRegex.test(valor);
    } else if (tipo === 'tel' && valor) {
        // Validación simple: al menos 8 dígitos
        const telRegex = /\d{8,}/;
        valido = telRegex.test(valor);
    } else if (tipo === 'number' && valor) {
        valido = !isNaN(valor) && parseFloat(valor) >= 0;
    }

    if (valido) {
        campo.removeClass('is-invalid').addClass('is-valid');
    } else {
        campo.removeClass('is-valid').addClass('is-invalid');
    }

    return valido;
}

/**
 * Validar formulario completo
 */
function validarFormulario() {
    let valido = true;
    const form = $('#formPostulacion');

    // Validar campos requeridos
    form.find('input[required], textarea[required], select[required]').each(function () {
        if (!validarCampo($(this))) {
            valido = false;
        }
    });

    // Validar CV
    if (!archivoCV) {
        mostrarErrorCV('Debes adjuntar tu CV en formato PDF');
        valido = false;
    }

    // Validar términos
    if (!$('#terminos').is(':checked')) {
        $('#terminos').addClass('is-invalid');
        valido = false;
    } else {
        $('#terminos').removeClass('is-invalid');
    }

    if (!valido) {
        Swal.fire({
            icon: 'warning',
            title: 'Formulario incompleto',
            text: 'Por favor completa todos los campos requeridos',
            confirmButtonColor: '#51B8AC'
        });
    }

    return valido;
}

/**
 * Enviar postulación
 */
async function enviarPostulacion() {
    const btnEnviar = $('#btnEnviar');
    btnEnviar.prop('disabled', true).addClass('btn-loading');

    try {
        // Crear FormData
        const formData = new FormData();
        formData.append('plaza_id', $('#plazaId').val());
        formData.append('cargo_id', $('#cargoId').val());
        formData.append('sucursal_id', $('#sucursalId').val());
        formData.append('nombre', $('#nombre').val().trim());
        formData.append('correo', $('#correo').val().trim());
        formData.append('telefono', $('#telefono').val().trim());
        formData.append('experiencia', $('#experiencia').val().trim());
        formData.append('aspiracion', $('#aspiracion').val());
        formData.append('comentario', $('#comentario').val().trim());
        formData.append('cv', archivoCV);

        // Enviar
        const response = await fetch('ajax/guardar_postulacion.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Éxito
            Swal.fire({
                icon: 'success',
                title: '¡Postulación enviada!',
                html: `
                    <p>Tu postulación ha sido recibida exitosamente.</p>
                    <p class="mb-0"><strong>Número de referencia:</strong> ${data.postulacion_id}</p>
                    <p class="text-muted small">Nos pondremos en contacto contigo pronto.</p>
                `,
                confirmButtonColor: '#51B8AC',
                confirmButtonText: 'Ver más vacantes'
            }).then(() => {
                window.location.href = 'index.php';
            });
        } else {
            throw new Error(data.error || 'Error al enviar la postulación');
        }

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'No se pudo enviar la postulación. Por favor, intenta de nuevo.',
            confirmButtonColor: '#51B8AC'
        });
    } finally {
        btnEnviar.prop('disabled', false).removeClass('btn-loading');
    }
}

// ==================== Utilidades ====================

function formatearMoneda(valor) {
    return new Intl.NumberFormat('es-NI', {
        style: 'currency',
        currency: 'NIO',
        minimumFractionDigits: 0
    }).format(valor);
}

function formatearTamanio(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}
