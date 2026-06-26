<?php
// unete.php - Página "Únete al Equipo" (Listado de Vacantes)
$page_title = "Trabaja en Batidos Pitaya Nicaragua - Vacantes Disponibles";
$page_description = "Encuentra empleo en Batidos Pitaya. Vacantes en Managua, Granada, Masaya. Únete a nuestro equipo de energía natural y experiencia WOW.";
$page_keywords = "empleo batidos pitaya, trabajo batidos pitaya nicaragua, vacantes batidos pitaya, empleo managua, trabajo managua, vacantes nicaragua 2026, trabajo pura fruta";
$page_canonical = "unete.php";
$active_tab = "unete";

include 'layout_talento/header.php';
?>

<!-- ==================== SECCIÓN: ÚNETE AL EQUIPO (Vacantes) ==================== -->
<div id="section-unete-equipo" class="tab-section-content active-tab">
    <!-- Filtros y Búsqueda (Opcional, oculto por defecto según diseño previo) -->
    <section class="filters-section" style="display: none;">
        <div class="container">
            <div class="filters-card">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" id="busqueda" class="form-control"
                                placeholder="Buscar por cargo o ubicación...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="filtroCategoria" class="form-select">
                            <option value="">Todas las categorías</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filtroUbicacion" class="form-select">
                            <option value="">Todas las ubicaciones</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="ordenamiento" class="form-select">
                            <option value="salario" selected>Mejor salario</option>
                            <option value="fecha">Más recientes</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tabs de Departamentos (Filtros de Categorías en frontend, mapeados a departamentos) -->
    <section class="categories-section">
        <div class="container">
            <div class="category-tabs-container">
                <div class="category-tabs" id="categoryTabs">
                    <button class="category-tab active" data-categoria="">
                        <i class="bi bi-grid-3x3-gap"></i>
                        Todas
                    </button>
                </div>
            </div>

            <!-- Estadísticas (Ocultas según requerimiento) -->
            <div class="stats-container d-none">
                <div class="stat-card-modern">
                    <div class="stat-icon">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label-small">Encontramos para ti</div>
                        <div class="stat-number-large" id="totalVacantes">0</div>
                        <div class="stat-label-main">Vacantes</div>
                    </div>
                </div>
                <div class="stat-card-modern">
                    <div class="stat-icon">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label-small">Actualizado hace 5 minutos</div>
                        <div class="stat-number-large" id="totalCategorias">0</div>
                        <div class="stat-label-main">Áreas de Trabajo</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Grid de Vacantes -->
    <section class="vacantes-section" id="vacantes">
        <div class="container">
            <!-- Loader -->
            <div id="loader" class="loader-container">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>

            <!-- Grid de Cards -->
            <div id="vacantesGrid" class="vacantes-grid">
                <!-- Las cards se cargarán dinámicamente aquí -->
            </div>

            <!-- Sin resultados -->
            <div id="sinResultados" class="sin-resultados" style="display: none;">
                <i class="bi bi-inbox"></i>
                <h3>No se encontraron vacantes</h3>
                <p>Intenta ajustar los filtros de búsqueda</p>
            </div>

            <!-- Paginación -->
            <div class="pagination-container mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0">Mostrar:</label>
                        <select class="form-select form-select-sm" id="registrosPorPagina" style="width: auto;">
                            <option value="12">12</option>
                            <option value="24" selected>24</option>
                            <option value="48">48</option>
                        </select>
                        <span class="mb-0">vacantes</span>
                    </div>
                    <div id="paginacion"></div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'layout_talento/footer.php'; ?>
