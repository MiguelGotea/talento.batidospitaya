<?php
// equipo.php - Página "Nuestro Equipo"
$page_title = "Nuestro Equipo - Batidos Pitaya Nicaragua";
$page_description = "Conoce al equipo que hace posible la Experiencia WOW en Batidos Pitaya. Profesionales dedicados a promover bienestar en Nicaragua.";
$page_keywords = "equipo batidos pitaya, colaboradores batidos pitaya, personal batidos pitaya";
$page_canonical = "equipo.php";
$active_tab = "equipo";

include 'layout_talento/header.php';

/* 
================================================================================
GUÍA DE INTEGRACIÓN CON BASE DE DATOS (FUTURA EXPANSIÓN)
================================================================================
Si en el futuro deseas que esta sección cargue dinámicamente desde la base de datos
del ERP, puedes seguir estos pasos:

1. Asegúrate de incluir la conexión en el core si no se ha cargado previamente:
   require_once 'core/database/conexion.php';

2. Crea una consulta SQL para traer los departamentos o colaboradores. Por ejemplo:
   $stmt = $conn->prepare("SELECT * FROM equipo_talento ORDER BY orden ASC");
   $stmt->execute();
   $equipo = $stmt->fetchAll(PDO::FETCH_ASSOC);

3. Reemplaza el bloque HTML de abajo con un bucle foreach en PHP:
   <?php foreach ($equipo as $dept): ?>
       <div class="equipo-card">
           <div class="equipo-card-inner">
               <div class="equipo-icon-wrapper">
                   <i class="bi bi-<?php echo htmlspecialchars($dept['icono']); ?>"></i>
               </div>
               <h3 class="equipo-card-title"><?php echo htmlspecialchars($dept['nombre_departamento']); ?></h3>
               <p class="equipo-card-desc"><?php echo htmlspecialchars($dept['descripcion']); ?></p>
           </div>
       </div>
   <?php endforeach; ?>
================================================================================
*/
?>

<!-- ==================== SECCIÓN: NUESTRO EQUIPO ==================== -->
<div id="section-nuestro-equipo" class="tab-section-content active-tab">
    <section class="equipo-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <span class="nosotros-subtitle-brand">El Alma de la Empresa</span>
                <h2 class="section-title-custom">Nuestro Equipo</h2>
                <p class="section-desc-custom">
                    Personas apasionadas por inspirar hábitos positivos, promover el bienestar y brindar la mejor Experiencia WOW a toda Nicaragua.
                </p>
            </div>

            <div class="equipo-grid">
                <!-- Tarjeta 1: Operaciones y Tiendas -->
                <div class="equipo-card">
                    <div class="equipo-card-inner">
                        <div class="equipo-icon-wrapper">
                            <i class="bi bi-shop"></i>
                        </div>
                        <h3 class="equipo-card-title">Operaciones y Tiendas</h3>
                        <p class="equipo-card-desc">
                            El frente de nuestra experiencia. Nuestros Líderes de Tienda y Operarios que elaboran cada batido con fruta 100% natural, garantizando limpieza, frescura y la Experiencia WOW en cada visita.
                        </p>
                    </div>
                </div>

                <!-- Tarjeta 2: Logística y Suministros -->
                <div class="equipo-card">
                    <div class="equipo-card-inner">
                        <div class="equipo-icon-wrapper">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h3 class="equipo-card-title">Logística y Suministros</h3>
                        <p class="equipo-card-desc">
                            La columna vertebral de la frescura. Encargados de la selección de frutas en el campo y de su transporte diario y óptimo hacia cada una de nuestras sucursales a nivel nacional.
                        </p>
                    </div>
                </div>

                <!-- Tarjeta 3: Administración y Finanzas -->
                <div class="equipo-card">
                    <div class="equipo-card-inner">
                        <div class="equipo-icon-wrapper">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <h3 class="equipo-card-title">Administración y Finanzas</h3>
                        <p class="equipo-card-desc">
                            Estrategia y solidez. Profesionales responsables de la planificación financiera, gestión de recursos y soporte operativo para que todo nuestro sistema marche con armonía y eficiencia.
                        </p>
                    </div>
                </div>

                <!-- Tarjeta 4: Talento y Cultura -->
                <div class="equipo-card">
                    <div class="equipo-card-inner">
                        <div class="equipo-icon-wrapper">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h3 class="equipo-card-title">Talento y Cultura</h3>
                        <p class="equipo-card-desc">
                            Atrayendo y potenciando a los mejores. Se enfocan en el desarrollo integral de nuestro personal, planes de carrera, capacitaciones y la preservación de nuestro gran clima laboral.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'layout_talento/footer.php'; ?>
