/**
 * JavaScript Principal - Portal de Empleo Batidos Pitaya
 * talento.batidospitaya.com
 */

// Variables globales
let paginaActual = 1;
let registrosPorPagina = 24;
let totalPlazas = 0;
let plazasData = [];
let categoriasData = [];
let ubicacionesData = [];

// Filtros activos
let filtros = {
    categoria: '',
    ubicacion: '',
    busqueda: '',
    orden: 'salario',
    salario_min: 0,
    salario_max: 999999
};

// ==================== Inicialización ====================
$(document).ready(function () {
    cargarPlazas();
    inicializarEventos();
    inicializarQuickLinks();
});

/**
 * Inicializar event listeners
 */
function inicializarEventos() {
    // Búsqueda con debounce
    let busquedaTimeout;
    $('#busqueda').on('input', function () {
        clearTimeout(busquedaTimeout);
        busquedaTimeout = setTimeout(() => {
            filtros.busqueda = $(this).val();
            paginaActual = 1;
            cargarPlazas();
        }, 500);
    });

    // Filtros
    $('#filtroCategoria').on('change', function () {
        filtros.categoria = $(this).val();
        paginaActual = 1;
        cargarPlazas();
    });

    $('#filtroUbicacion').on('change', function () {
        filtros.ubicacion = $(this).val();
        paginaActual = 1;
        cargarPlazas();
    });

    $('#ordenamiento').on('change', function () {
        filtros.orden = $(this).val();
        paginaActual = 1;
        cargarPlazas();
    });

    // Registros por página
    $('#registrosPorPagina').on('change', function () {
        registrosPorPagina = parseInt($(this).val());
        paginaActual = 1;
        cargarPlazas();
    });

    // Botón postular en modal
    $('#btnPostularModal').on('click', function () {
        const plazaId = $(this).data('plaza-id');
        const cargoId = $(this).data('cargo-id');
        const sucursalId = $(this).data('sucursal-id');

        // Redirigir a formulario de postulación
        window.location.href = `postular.php?plaza=${plazaId}&cargo=${cargoId}&sucursal=${sucursalId}`;
    });
}

/**
 * Inicializar enlaces rápidos
 */
function inicializarQuickLinks() {
    $('.quick-link-card').on('click', function (e) {
        e.preventDefault();
        const categoria = $(this).data('categoria');

        // Actualizar filtro
        filtros.categoria = categoria;
        $('#filtroCategoria').val(categoria);

        // Activar tab correspondiente
        $('.category-tab').removeClass('active');
        $(`.category-tab:contains("${categoria}")`).addClass('active');

        // Cargar plazas
        paginaActual = 1;
        cargarPlazas();

        // Scroll a las vacantes
        $('html, body').animate({
            scrollTop: $('.vacantes-section').offset().top - 100
        }, 500);
    });
}


/**
 * Cargar plazas desde el servidor
 */
async function cargarPlazas() {
    mostrarLoader(true);

    try {
        const params = new URLSearchParams({
            categoria: filtros.categoria,
            ubicacion: filtros.ubicacion,
            busqueda: filtros.busqueda,
            orden: filtros.orden,
            salario_min: filtros.salario_min,
            salario_max: filtros.salario_max,
            pagina: paginaActual,
            por_pagina: registrosPorPagina
        });

        const response = await fetch(`ajax/get_plazas.php?${params}`);
        const data = await response.json();

        if (data.success) {
            plazasData = data.plazas;
            totalPlazas = data.total;
            categoriasData = data.categorias;
            ubicacionesData = data.ubicaciones;

            // Actualizar UI
            actualizarEstadisticas();
            renderizarCategorias();
            renderizarFiltros();
            renderizarPlazas();
            renderizarPaginacion(data.total_paginas);
            actualizarSchemaOrg();
        } else {
            mostrarError('Error al cargar las plazas');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarError('Error de conexión. Por favor, intenta de nuevo.');
    } finally {
        mostrarLoader(false);
    }
}

/**
 * Actualizar estadísticas del hero
 */
function actualizarEstadisticas() {
    // Animar contador de vacantes
    animarContador('#totalVacantes', totalPlazas);

    // Animar contador de categorías
    animarContador('#totalCategorias', categoriasData.length);

    // Actualizar texto de resultados
    const texto = totalPlazas === 0
        ? 'No se encontraron vacantes'
        : `Mostrando ${plazasData.length} de ${totalPlazas} vacantes`;
    $('#resultadosTexto').text(texto);
}

/**
 * Animar contador numérico
 */
function animarContador(selector, valorFinal) {
    const elemento = $(selector);
    const valorInicial = parseInt(elemento.text()) || 0;
    const duracion = 1000;
    const pasos = 30;
    const incremento = (valorFinal - valorInicial) / pasos;
    let contador = valorInicial;

    const intervalo = setInterval(() => {
        contador += incremento;
        if ((incremento > 0 && contador >= valorFinal) || (incremento < 0 && contador <= valorFinal)) {
            elemento.text(Math.round(valorFinal));
            clearInterval(intervalo);
        } else {
            elemento.text(Math.round(contador));
        }
    }, duracion / pasos);
}

/**
 * Renderizar tabs de categorías
 */
function renderizarCategorias() {
    const container = $('#categoryTabs');

    // Botón "Todas"
    let html = `
        <button class="category-tab ${filtros.categoria === '' ? 'active' : ''}" 
                onclick="filtrarPorCategoria('')">
            <i class="bi bi-grid-3x3-gap"></i>
            Todas
        </button>
    `;

    // Categorías dinámicas
    categoriasData.forEach(cat => {
        const icono = obtenerIconoCategoria(cat.nombre);
        html += `
            <button class="category-tab ${filtros.categoria === cat.nombre ? 'active' : ''}" 
                    onclick="filtrarPorCategoria('${cat.nombre}')">
                <i class="bi bi-${icono}"></i>
                ${cat.nombre} (${cat.count})
            </button>
        `;
    });

    container.html(html);
}

/**
 * Renderizar filtros de ubicación
 */
function renderizarFiltros() {
    // Filtro de ubicación
    let htmlUbicacion = '<option value="">Todas las ubicaciones</option>';
    ubicacionesData.forEach(ubi => {
        htmlUbicacion += `<option value="${ubi}" ${filtros.ubicacion === ubi ? 'selected' : ''}>${ubi}</option>`;
    });
    $('#filtroUbicacion').html(htmlUbicacion);

    // Filtro de categoría
    let htmlCategoria = '<option value="">Todas las categorías</option>';
    categoriasData.forEach(cat => {
        htmlCategoria += `<option value="${cat.nombre}" ${filtros.categoria === cat.nombre ? 'selected' : ''}>${cat.nombre}</option>`;
    });
    $('#filtroCategoria').html(htmlCategoria);
}

/**
 * Renderizar grid de plazas
 */
function renderizarPlazas() {
    const container = $('#vacantesGrid');
    const sinResultados = $('#sinResultados');

    if (plazasData.length === 0) {
        container.hide();
        sinResultados.show();
        return;
    }

    container.show();
    sinResultados.hide();

    let html = '';
    plazasData.forEach(plaza => {
        html += crearCardPlaza(plaza);
    });

    container.html(html);
}

/**
 * Crear card de plaza
 */
function crearCardPlaza(plaza) {
    const today = new Date();
    const formattedDate = `${String(today.getDate()).padStart(2, '0')}/${String(today.getMonth() + 1).padStart(2, '0')}/${today.getFullYear()}`;

    return `
        <div class="vacante-card">
            <div class="vigente-box">
                VIGENTE: ${formattedDate}
            </div>
            
            <div class="vacante-header">
                <span class="vacante-categoria">${plaza.especialidad_area}</span>
            </div>
            
            <h3 class="vacante-titulo">${plaza.cargo_nombre}</h3>
            
            <div class="vacante-ubicacion">
                <i class="bi bi-geo-alt-fill"></i>
                ${plaza.departamento}
            </div>

            <div class="vacante-actions">
                <button class="btn-vr-plaza" onclick="abrirBannerPlaza('${plaza.ruta_banner_cargo || ''}')">
                    <i class="bi bi-eye-fill"></i> Ver Plaza
                </button>
                
                <button class="btn-postular" onclick="postularDirecto(${plaza.id}, ${plaza.cargo_id}, ${plaza.sucursal_id})">
                    <i class="bi bi-send-fill"></i>
                    Postular Ahora
                </button>
            </div>
        </div>
    `;
}

/**
 * Abrir banner de la plaza al hacer click en la card
 */
function abrirBannerPlaza(rutaBanner) {
    if (!rutaBanner) {
        Swal.fire({
            icon: 'info',
            title: 'Información',
            text: 'Esta vacante no tiene un banner descriptivo disponible en este momento.',
            confirmButtonColor: '#51B8AC'
        });
        return;
    }

    const bannerUrl = `ajax/get_banner.php?archivo=${encodeURIComponent(rutaBanner)}`;

    const modalHtml = `
        <div id="modalBanner" class="banner-overlay" onclick="document.getElementById('modalBanner').remove()">
            <div class="banner-modal-content">
                <img src="${bannerUrl}" 
                     alt="Banner del puesto" 
                     class="banner-img"
                     onerror="handleBannerError(this)">
                <button class="banner-close-btn" onclick="event.stopPropagation(); document.getElementById('modalBanner').remove();">
                    &times;
                </button>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function handleBannerError(img) {
    const errorContainer = document.createElement('div');
    errorContainer.className = 'banner-error-msg';
    errorContainer.innerHTML = `
        <i class="bi bi-exclamation-triangle"></i>
        <h3>Banner no disponible</h3>
        <p>No se pudo visualizar la imagen descriptiva.</p>
    `;
    img.replaceWith(errorContainer);
}

/**
 * Crear contenido del modal de detalle
 */
function crearContenidoModal(plaza) {
    return `
        <div class="row">
            <div class="col-md-6 mb-3">
                <h6 class="text-muted">Ubicación</h6>
                <p><i class="bi bi-geo-alt-fill text-primary"></i> ${plaza.departamento}</p>
            </div>
            <div class="col-md-6 mb-3">
                <h6 class="text-muted">Salario Propuesto</h6>
                <p class="h5 text-success">${formatearMoneda(plaza.salario_propuesto)}</p>
            </div>
            <div class="col-md-6 mb-3">
                <h6 class="text-muted">Plazas Disponibles</h6>
                <p class="h5 text-primary">${plaza.plazas_disponibles}</p>
            </div>
            <div class="col-md-6 mb-3">
                <h6 class="text-muted">Área</h6>
                <p>${plaza.especialidad_area}</p>
            </div>
        </div>
        
        <hr>
        
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            <strong>Información:</strong> Al postular, deberás completar un formulario con tus datos personales y adjuntar tu CV en formato PDF.
        </div>
    `;
}

/**
 * Postular directamente (sin ver detalle)
 */
function postularDirecto(plazaId, cargoId, sucursalId) {
    window.location.href = `postular.php?plaza=${plazaId}&cargo=${cargoId}&sucursal=${sucursalId}`;
}

/**
 * Filtrar por categoría
 */
function filtrarPorCategoria(categoria) {
    filtros.categoria = categoria;
    paginaActual = 1;
    cargarPlazas();
}

/**
 * Renderizar paginación
 */
function renderizarPaginacion(totalPaginas) {
    const container = $('#paginacion');

    if (totalPaginas <= 1) {
        container.html('');
        return;
    }

    let html = '';

    // Botón anterior
    if (paginaActual > 1) {
        html += `<button class="page-btn" onclick="cambiarPagina(${paginaActual - 1})"><i class="bi bi-chevron-left"></i></button>`;
    }

    // Páginas
    for (let i = 1; i <= totalPaginas; i++) {
        if (i === 1 || i === totalPaginas || (i >= paginaActual - 2 && i <= paginaActual + 2)) {
            html += `<button class="page-btn ${i === paginaActual ? 'active' : ''}" onclick="cambiarPagina(${i})">${i}</button>`;
        } else if (i === paginaActual - 3 || i === paginaActual + 3) {
            html += `<span class="page-btn">...</span>`;
        }
    }

    // Botón siguiente
    if (paginaActual < totalPaginas) {
        html += `<button class="page-btn" onclick="cambiarPagina(${paginaActual + 1})"><i class="bi bi-chevron-right"></i></button>`;
    }

    container.html(html);
}

/**
 * Cambiar página
 */
function cambiarPagina(pagina) {
    paginaActual = pagina;
    cargarPlazas();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ==================== WhatsApp Float ====================

/**
 * Inicializar botón de WhatsApp
 */
$(document).ready(function () {
    const whatsappButton = $('#whatsappButton');
    const whatsappPopup = $('#whatsappPopup');
    const sendWhatsapp = $('#sendWhatsapp');
    const whatsappNumber = '50584092477'; // Número de reclutamiento

    // Toggle popup
    whatsappButton.on('click', function (e) {
        e.stopPropagation();
        whatsappPopup.toggle();
    });

    // Cerrar popup al hacer click fuera
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.whatsapp-float').length) {
            whatsappPopup.hide();
        }
    });

    // Enviar mensaje por WhatsApp
    sendWhatsapp.on('click', function () {
        const message = $('#whatsappMessage').val().trim();
        if (message) {
            const encodedMessage = encodeURIComponent(message);
            const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;
            window.open(whatsappUrl, '_blank');
            whatsappPopup.hide();
        }
    });

    // Enter para enviar
    $('#whatsappMessage').on('keypress', function (e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            sendWhatsapp.click();
        }
    });
});


/**
 * Actualizar Schema.org con plazas actuales
 */
function actualizarSchemaOrg() {
    const jobPostings = plazasData.slice(0, 10).map(plaza => ({
        "@type": "JobPosting",
        "title": plaza.cargo_nombre,
        "description": `Plaza disponible en ${plaza.departamento} - ${plaza.sucursal_nombre}`,
        "datePosted": plaza.fecha_publicacion,
        "employmentType": "FULL_TIME",
        "hiringOrganization": {
            "@type": "Organization",
            "name": "Batidos Pitaya",
            "sameAs": "https://batidospitaya.com"
        },
        "jobLocation": {
            "@type": "Place",
            "address": {
                "@type": "PostalAddress",
                "addressLocality": plaza.departamento,
                "addressCountry": "NI"
            }
        },
        "baseSalary": {
            "@type": "MonetaryAmount",
            "currency": "NIO",
            "value": {
                "@type": "QuantitativeValue",
                "value": plaza.salario_propuesto,
                "unitText": "MONTH"
            }
        }
    }));

    const schema = {
        "@context": "https://schema.org/",
        "@graph": jobPostings
    };

    $('#schemaJobPostings').text(JSON.stringify(schema, null, 2));
}

// ==================== Utilidades ====================

function mostrarLoader(mostrar) {
    if (mostrar) {
        $('#loader').show();
        $('#vacantesGrid').hide();
    } else {
        $('#loader').hide();
        $('#vacantesGrid').show();
    }
}

function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje,
        confirmButtonColor: '#51B8AC'
    });
}

function formatearMoneda(valor) {
    return new Intl.NumberFormat('es-NI', {
        style: 'currency',
        currency: 'NIO',
        minimumFractionDigits: 0
    }).format(valor);
}

function formatearFecha(fecha) {
    const date = new Date(fecha);
    const opciones = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('es-NI', opciones);
}

function obtenerIconoCategoria(categoria) {
    const iconos = {
        'Tecnología': 'laptop',
        'Administración': 'briefcase',
        'Ventas': 'cart',
        'Producción': 'gear',
        'Marketing': 'megaphone',
        'Recursos Humanos': 'people',
        'Finanzas': 'cash-stack',
        'Operaciones': 'diagram-3'
    };
    return iconos[categoria] || 'circle';
}

function obtenerIconoUrgencia(nivel) {
    const iconos = {
        1: 'info-circle',
        2: 'exclamation-circle',
        3: 'exclamation-triangle',
        4: 'exclamation-diamond'
    };
    return iconos[nivel] || 'info-circle';
}

function obtenerTextoUrgencia(nivel) {
    const textos = {
        1: 'Normal',
        2: 'Medio',
        3: 'Urgente',
        4: 'Crítico'
    };
    return textos[nivel] || 'Normal';
}

function obtenerColorUrgencia(nivel) {
    const colores = {
        1: 'success',
        2: 'warning',
        3: 'orange',
        4: 'danger'
    };
    return colores[nivel] || 'secondary';
}

// ==================== Email Protection ====================

/**
 * Desofuscar emails protegidos
 */
$(document).ready(function () {
    $('.email-protected').each(function () {
        const user = $(this).data('user');
        const domain = $(this).data('domain');
        if (user && domain) {
            const email = user + '@' + domain;
            $(this).html(`<a href="mailto:${email}" class="text-white">${email}</a>`);
        }
    });
});

