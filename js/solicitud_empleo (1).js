/*
    solicitud_empleo.js
    Lógica para navegación entre pestañas, autoguardado y validación.
*/

$(document).ready(function () {
    let currentStep = 1;
    const totalSteps = 6;

    // Inicializar Stepper
    updateStepper();

    // Navegación: Siguiente
    $('#btnNext').on('click', function () {
        if (currentStep < totalSteps) {
            if (validateCurrentSection()) {
                saveProgress().then(() => {
                    currentStep++;
                    updateStepper();
                    showSection(currentStep);
                });
            }
        }
    });

    // Navegación: Anterior
    $('#btnPrev').on('click', function () {
        if (currentStep > 1) {
            currentStep--;
            updateStepper();
            showSection(currentStep);
        }
    });

    // Guardar borrador manual
    $('#btnSaveDraft').on('click', function () {
        saveProgress(true);
    });

    // Navegación por click en los círculos del stepper
    $(document).on('click', '.step', function () {
        const clickedStep = parseInt($(this).data('step'));
        if (clickedStep === currentStep) return;

        if (clickedStep < currentStep) {
            // Ir a pasos anteriores directamente (siempre permitido)
            saveProgress().then(() => {
                currentStep = clickedStep;
                updateStepper();
                showSection(currentStep);
            });
        } else {
            // Ir a pasos adelante: guardar sin validación estricta
            saveProgress().then(() => {
                currentStep = clickedStep;
                updateStepper();
                showSection(currentStep);
            });
        }
    });

    function updateStepper() {
        $('.step').removeClass('active completed');
        $('.step').each(function () {
            const step = $(this).data('step');
            if (step === currentStep) {
                $(this).addClass('active');
            } else if (step < currentStep) {
                $(this).addClass('completed');
            }
        });

        // Actualizar línea de progreso
        const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
        $('.progress-fill').css('width', progress + '%');

        // Actualizar botones
        $('#btnPrev').prop('disabled', currentStep === 1);
        if (currentStep === totalSteps) {
            $('#btnNext').addClass('d-none');
            $('#btnSubmit').removeClass('d-none');
        } else {
            $('#btnNext').removeClass('d-none');
            $('#btnSubmit').addClass('d-none');
        }
    }

    function showSection(step) {
        $('.form-section').removeClass('active');
        $(`#section-${step}`).addClass('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateCurrentSection() {
        const section = $(`#section-${currentStep}`);
        let isValid = true;

        // Validación básica de requeridos en la sección actual
        section.find('[required]').each(function () {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos requeridos',
                text: 'Por favor completa todos los campos marcados antes de continuar.',
                confirmButtonColor: '#ff6b00'
            });
        }
        return isValid;
    }

    async function saveProgress(manual = false) {
        const formData = new FormData($('#formSolicitud')[0]);
        formData.append('action', 'save_progress');
        formData.append('current_step', currentStep);

        if (manual) {
            Swal.fire({
                title: 'Guardando...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
        }

        try {
            const response = await fetch('ajax/solicitud_empleo_handler.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (manual) {
                if (data.success) {
                    Swal.fire('¡Éxito!', 'Tu progreso ha sido guardado correctamente.', 'success');
                } else {
                    Swal.fire('Error', 'No se pudo guardar el progreso: ' + data.message, 'error');
                }
            }
            return data.success;
        } catch (error) {
            console.error('Error al guardar:', error);
            if (manual) Swal.fire('Error', 'Hubo un problema de conexión con el servidor.', 'error');
            return false;
        }
    }

    // Cálculo automático de edad
    $('input[name="fecha_nacimiento"]').on('change', function () {
        const fecha = new Date($(this).val());
        if (!isNaN(fecha.getTime())) {
            const hoy = new Date();
            let edad = hoy.getFullYear() - fecha.getFullYear();
            const mes = hoy.getMonth() - fecha.getMonth();
            if (mes < 0 || (mes === 0 && hoy.getDate() < fecha.getDate())) {
                edad--;
            }
            $('input[name="edad"]').val(edad);
        }
    });

    // Quitar clases de error al escribir
    $('.form-control, .form-select').on('input change', function () {
        $(this).removeClass('is-invalid');
    });

    // Limpiar flag de verificación de la URL para que el próximo refresh pida código
    if (window.location.search.includes('v=1')) {
        const url = new URL(window.location);
        url.searchParams.delete('v');
        window.history.replaceState({}, document.title, url.pathname + url.search);
    }

    // Ocultar Loader al cargar todo
    window.onload = function () {
        $('#loader').fadeOut('slow');
    };
});

// Función global para verificar el código de acceso (2FA)
async function verifyAccessCode() {
    const code = $('#2fa-code').val().trim();
    const errorDiv = $('#verification-error');

    if (code.length !== 6) {
        errorDiv.text('El código debe tener 6 dígitos.').removeClass('d-none');
        return;
    }

    const btn = $('.verification-card button');
    const originalText = btn.text();
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Verificando...');
    errorDiv.addClass('d-none');

    try {
        const formData = new FormData();
        formData.append('action', 'verify_code');
        formData.append('code', code);

        const response = await fetch(window.location.href, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Éxito: recargar la página con flag de verificación temporal
            const url = new URL(window.location.href);
            url.searchParams.set('v', '1');
            window.location.href = url.href;
        } else {
            errorDiv.text(data.message || 'Código incorrecto.').removeClass('d-none');
            $('#2fa-code').val('').focus();
        }
    } catch (error) {
        console.error('Error:', error);
        errorDiv.text('Error de conexión con el servidor.').removeClass('d-none');
    } finally {
        btn.prop('disabled', false).text(originalText);
    }
}

// Auto-submit al completar los 6 dígitos
$(document).on('input', '#2fa-code', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.length === 6) {
        verifyAccessCode();
    }
});

