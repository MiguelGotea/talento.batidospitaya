# Plan de Implementación — Portal de Talento (talento.batidospitaya.com)

> Documento actualizado con las notas de la Gerencia de Marketing y Operaciones (Junio 2026).
> Las fases están ordenadas cronológicamente. La Fase 1 ya fue completada.

---

## FASE 1 — Arquitectura Base y Navegación ✅ COMPLETADA

Esta fase estableció la estructura modular del portal, la navegación responsiva y los filtros de vacantes por departamento.

### Lo que se hizo

#### Layout compartido
- **`layout_talento/header.php`** — Cabecera con navbar centrada, botón Aplicar y hamburguesa móvil
- **`layout_talento/footer.php`** — Footer, modal de detalle, scripts jQuery/Bootstrap/SweetAlert2

#### Páginas creadas / modificadas
- **`index.php`** — Únete al Equipo: listado de vacantes con filtros por departamento
- **`nosotros.php`** — Sobre Nosotros: SVG grupal, ¿Quiénes Somos?, Nuestros Valores, Nuestro Propósito, estadísticas animadas
- **`equipo.php`** — Nuestro Equipo: tarjetas estáticas de departamento con guía de BD comentada
- **`noticias.php`** — Noticias: 3 tarjetas de muestra con guía de BD comentada

#### Estilos y Scripts
- **`css/global.css`** — Navbar centrada absolute, hover animado `cubic-bezier`, dropdown móvil, estilos de todas las secciones, botones flotantes eliminados
- **`js/main.js`** — Menú hamburguesa toggle (clic+hover), carga condicional por página, ícono `geo-alt` para departamentos
- **`ajax/get_plazas.php`** — Filtrado y agrupación por `s.departamento` en lugar de `especialidad_area`
- **`sitemap.xml.php`** — Actualizado con las 4 páginas del portal

---

## CORRECCIONES PENDIENTES FASE 1 — Menú Móvil

### Fix: El ícono hamburguesa no actúa como toggle real de cierre

**Problema reportado:** Al abrir el menú en móvil con el ícono de 3 líneas, las pestañas solo se contraen si el usuario hace scroll hacia abajo o hace clic fuera. No hay forma visual de cerrarlo intencionalmente, ni indicación de que el menú está abierto.

**Comportamiento esperado:**
- Menú **cerrado** → ícono `bi-list` (3 líneas)
- Menú **abierto** → ícono `bi-x` (X de cierre), mismo botón
- Clic en el botón **siempre** alterna el estado (toggle real)
- Clic fuera del menú o tecla `Escape` también cierra y regresa a `bi-list`

**Archivos a modificar:**
- **[MODIFY]** `js/main.js` → en `inicializarMenuMovil()`, al hacer toggle de `.menu-fixed` también hacer `toggleBtn.find('i').toggleClass('bi-list bi-x')`. Al cerrar por clic exterior o Escape, forzar `bi-list`.
- **[MODIFY]** `css/global.css` → cuando el botón tiene clase activa (menú abierto), aplicar fondo semitransparente blanco al botón para refuerzo visual del estado abierto.

---

## FASE 2 — Enriquecimiento de Contenido ⏳ PENDIENTE


> **Origen:** Notas de la Gerencia de Marketing y Operaciones.
> **Prioridad:** Alta. Puede implementarse en partes (2A, 2B, 2C).

---

### 2A — Tarjetas de Vacante Mejoradas + Página de Detalle

Las tarjetas actualmente muestran: ícono de ubicación, título y botones.
Las notas solicitan una vista de resumen más rica y una página de detalle completa.

> [!IMPORTANT]
> Esta mejora **requiere nuevas columnas en la base de datos**. Se debe crear y aplicar el script SQL antes de tocar el código. Ver sección de BD más abajo.

**Referencia visual:** https://bolsadetrabajo.mcdgente.com/trabajo/Oa

> [!NOTE]
> La descripción, responsabilidades, requisitos y habilidades de cada plaza también deben poder **editarse desde el ERP** en `postulacion_panel_control.php`, sin agregar columnas visibles a la tabla (para no saturar la interfaz). Ver sección "ERP: Panel de Control" más abajo.

#### Vista de tarjeta (resumen en el grid)
- Ícono de ubicación + departamento ← ya existe
- Título de la plaza ← ya existe
- Descripción breve del cargo (texto corto, ~100 caracteres)
- Responsabilidades principales (lista corta, 2-3 ítems)
- Requerimientos básicos (lista corta)
- Botón **"LEER MÁS"** al lado del botón actual de postular

#### Vista de detalle de vacante (`vacante_detalle.php`)
Nueva página a crear. Se accede al hacer clic en "Leer Más". Campos a mostrar:
- Nombre del puesto
- Descripción completa del cargo
- Responsabilidades (lista detallada)
- Requisitos / Requerimientos
- Habilidades (lista predefinida que el admin de RRHH selecciona al publicar)
- Información general (tipo contrato, horario, salario, etc.)
- Botón de postulación

#### Archivos a crear / modificar
- **[NEW]** `vacante_detalle.php` — Página pública de detalle, recibe `?plaza=ID` por GET
- **[MODIFY]** `js/main.js` — Botón "Leer Más" redirige a `vacante_detalle.php?plaza=ID`
- **[MODIFY]** `ajax/get_plazas.php` — Incluir nuevos campos en el payload

#### Script SQL requerido (`013_plazas_detalle_talento.sql`)

```sql
-- Agregar columnas de detalle público a plazas_cargos
ALTER TABLE plazas_cargos
  ADD COLUMN descripcion TEXT NULL
    COMMENT 'Descripción pública del cargo para el portal de talento',
  ADD COLUMN responsabilidades TEXT NULL
    COMMENT 'Lista de responsabilidades separadas por |',
  ADD COLUMN requisitos TEXT NULL
    COMMENT 'Lista de requisitos separados por |',
  ADD COLUMN habilidades TEXT NULL
    COMMENT 'IDs de habilidades predefinidas separados por coma';

-- Catálogo de habilidades predefinidas (selección del admin RRHH)
CREATE TABLE IF NOT EXISTS habilidades_talento (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  categoria VARCHAR(60) DEFAULT NULL,
  activo TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Catálogo de habilidades predefinidas para vacantes del portal de talento';
```

> [!NOTE]
> Guardar como `013_plazas_detalle_talento.sql` en `\GitHub Pitaya\SQL\` y aplicar en Hostinger antes de activar el código.

---

### 2A (bis) — ERP: Panel de Control de Plazas (`postulacion_panel_control.php`)

Esta sección es la **contrapartida interna** de la Fase 2A. Permite que el admin de RRHH agregue la información de detalle de cada plaza desde el ERP.

**Contexto del archivo actual:**
- Las tablas de Tiendas, Administrativo y CDS se generan dinámicamente por JS (`postulacion_panel_control.js`)
- Ya existen columnas de: Cargo, Obligatorio, Plaza Temporal, Personal Contratado, Mostrar en Web, Urgencia, Especialidad, Banner, Perfil de Puesto
- La columna **Banner** ya usa exactamente el patrón propuesto: encabezado de texto corto + un botón-ícono por fila (`width: 80px`)
- El archivo JS construye los `<tr>` dinámicamente en el cliente vía AJAX

**Propuesta: 2 columnas nuevas con encabezado de ícono — mismo patrón que "Banner"**

Agregar dos columnas nuevas al final de cada tabla (Tiendas, Administrativo, CDS), con el mismo ancho y estilo que la columna Banner existente:

```
| Cargo | Obligatorio | Contratado | Web | Urgencia | Especialidad | [📄] | [🏷️] | Banner | Activo |
                                                                      ↑55px   ↑55px
```

| Columna nueva | Encabezado `<th>` | Celda `<td>` | Acción |
|---|---|---|---|
| **Descripción** | `<i class="bi bi-chat-text" title="Descripción del cargo">` | Ícono-botón por fila | Abre modal de descripción, responsabilidades y requisitos |
| **Habilidades** | `<i class="bi bi-tags" title="Habilidades requeridas">` | Ícono-botón por fila | Abre modal de selección de habilidades predefinidas |

**Ancho de columnas:** `style="width: 55px;"` — solo ocupa el espacio del ícono, igual que Banner.

**Indicador de estado en el ícono de cada fila:**
- Sin datos → `text-muted` (gris claro) — el admin sabe que aún falta completar
- Con datos → `text-primary` / `text-success` (azul / verde con relleno) — ya documentado
- El color se determina al renderizar las filas desde los datos AJAX

**Flujo del modal de Descripción (`bi-chat-text`):**
1. Clic en ícono de la fila → se abre `#modalDescripcionCargo`
2. El modal carga los valores actuales vía AJAX (`ajax/get_detalle_plaza.php?plaza_id=X`)
3. Campos editables:
   - Descripción breve del cargo (textarea, ~3 líneas)
   - Responsabilidades (textarea, una por línea — el portal las mostrará como lista)
   - Requisitos (textarea, una por línea — mismo tratamiento)
4. Botón "Guardar" → AJAX POST a `ajax/guardar_detalle_plaza.php`
5. Toast de confirmación + ícono de la fila cambia a color activo, modal se cierra

**Flujo del modal de Habilidades (`bi-tags`):**
1. Clic en ícono de la fila → se abre `#modalHabilidadesCargo`
2. El modal carga el catálogo desde `ajax/get_habilidades_catalogo.php` y marca las ya asignadas
3. Las habilidades se muestran como **chips/badges con checkbox**, agrupadas por categoría
   - Ejemplo: "Atención al Cliente", "Trabajo en Equipo", "Manejo de Caja", "Office"...
4. Botón "Guardar" → AJAX POST a `ajax/guardar_habilidades_plaza.php` con array de IDs
5. Toast de confirmación + ícono cambia a verde, modal se cierra

> [!NOTE]
> El catálogo de habilidades (`habilidades_talento`) debe popularse manualmente por el administrador de sistemas una sola vez (INSERT SQL). No requiere panel de gestión por ahora.

**Archivos a crear / modificar en el ERP:**
- **[MODIFY]** `erp.batidospitaya/modulos/reclutamiento/postulacion_panel_control.php`
  - Agregar `<th>` de las 2 columnas nuevas en los 3 `<thead>` (Tiendas, Administrativo, CDS)
  - Agregar los 2 modales al final del `<body>` (antes del cierre `</body>`)
- **[MODIFY]** `erp.batidospitaya/modulos/reclutamiento/js/postulacion_panel_control.js`
  - Al renderizar cada `<tr>`, agregar los 2 `<td>` con el ícono-botón correspondiente
  - Colorear el ícono según si el campo está o no lleno (dato viene en el payload AJAX)
  - Listeners de clic para abrir cada modal con el `plaza_id` correcto
- **[NEW]** `erp.batidospitaya/modulos/reclutamiento/ajax/get_detalle_plaza.php`
  → Devuelve `descripcion`, `responsabilidades`, `requisitos`, `habilidades` de una plaza
- **[NEW]** `erp.batidospitaya/modulos/reclutamiento/ajax/guardar_detalle_plaza.php`
  → Guarda descripción/responsabilidades/requisitos en `plazas_cargos` (UPDATE)
- **[NEW]** `erp.batidospitaya/modulos/reclutamiento/ajax/guardar_habilidades_plaza.php`
  → Guarda array de IDs de habilidades en `plazas_cargos.habilidades`
- **[NEW]** `erp.batidospitaya/modulos/reclutamiento/ajax/get_habilidades_catalogo.php`
  → Devuelve catálogo completo de `habilidades_talento` (id, nombre, categoria)

> [!NOTE]
> Todos los nuevos endpoints del ERP deben incluir `require_once '../../../core/auth/auth.php'` y `require_once '../../../core/database/conexion.php'`, siguiendo el patrón establecido del ERP.


---

### 2B — Nuestro Equipo: Colaboradores Destacados con Carrusel

La sección actual muestra tarjetas estáticas de departamentos.
Las notas solicitan mostrar **colaboradores reales** con foto, nombre, cargo y testimonio.

**Referencia visual:** https://careers.glovoapp.com/

> [!IMPORTANT]
> Requiere tabla nueva en BD: `colaboradores_talento`. El área de RRHH debe proveer las fotos y textos de los ~10 colaboradores iniciales antes de activar esta sección.

#### Contenido por colaborador
- Fotografía (JPG/PNG subida a `uploads/equipo/`)
- Nombre completo
- Cargo / Puesto
- Texto representativo o testimonial (~2-3 oraciones)

#### Estructura de la página `equipo.php`
- Bloque **"Nuestro Talento"** con carrusel de ~10 colaboradores
- Carrusel responsivo (Bootstrap Carousel nativo — sin dependencias extras)

#### Archivos a crear / modificar
- **[MODIFY]** `equipo.php` — Agregar bloque de carrusel de colaboradores
- **[MODIFY]** `css/global.css` — Estilos `.colaborador-card`: foto circular, nombre, cargo, testimonio en cursiva

#### Script SQL requerido (`014_colaboradores_talento.sql`)

```sql
CREATE TABLE IF NOT EXISTS colaboradores_talento (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  cargo VARCHAR(120) NOT NULL,
  departamento VARCHAR(80) DEFAULT NULL,
  testimonio TEXT DEFAULT NULL
    COMMENT 'Frase representativa o testimonial del colaborador',
  foto VARCHAR(255) DEFAULT NULL
    COMMENT 'Ruta relativa: uploads/equipo/nombre.jpg',
  orden INT DEFAULT 0
    COMMENT 'Orden de aparición en el carrusel',
  activo TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Colaboradores destacados para el portal de talento';
```

---

### 2C — Noticias: Botón "Leer Más" + Página de Detalle + Galería

Las tarjetas de noticias ya tienen imagen, título y extracto.
Las notas solicitan un botón **"Leer Más"** y una página de detalle con galería de fotos.

> [!IMPORTANT]
> Requiere tablas nuevas: `noticias_talento` y `noticias_fotos_talento`. El ingreso de noticias (redacción, imágenes) deberá hacerse directamente en la BD o a través de un panel admin futuro.

#### Tarjeta de noticia (grid)
- Imagen, título, extracto ← ya existe
- Botón **"Leer Más"** que lleva a `noticia_detalle.php?id=ID`

#### Página de detalle (`noticia_detalle.php`)
- Imagen principal grande
- Contenido completo del artículo (texto HTML)
- Galería de fotografías relacionadas (grid de miniaturas con lightbox)

#### Archivos a crear / modificar
- **[NEW]** `noticia_detalle.php` — Página de detalle, recibe `?id=ID` por GET
- **[MODIFY]** `noticias.php` — Agregar botón "Leer Más" a cada tarjeta

#### Scripts SQL requeridos (`015_noticias_talento.sql`)

```sql
CREATE TABLE IF NOT EXISTS noticias_talento (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  resumen TEXT
    COMMENT 'Extracto corto para las tarjetas del grid',
  contenido LONGTEXT
    COMMENT 'Contenido completo del artículo (puede contener HTML)',
  imagen_principal VARCHAR(255) DEFAULT NULL
    COMMENT 'Ruta: uploads/noticias/portada.jpg',
  categoria VARCHAR(60) DEFAULT 'General',
  estado ENUM('borrador','publicado','archivado') DEFAULT 'borrador',
  fecha_publicacion DATE DEFAULT NULL,
  autor VARCHAR(120) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Noticias corporativas del portal de talento';

CREATE TABLE IF NOT EXISTS noticias_fotos_talento (
  id INT AUTO_INCREMENT PRIMARY KEY,
  noticia_id INT NOT NULL,
  ruta_foto VARCHAR(255) NOT NULL
    COMMENT 'Ruta: uploads/noticias/galeria/foto.jpg',
  descripcion VARCHAR(255) DEFAULT NULL,
  orden INT DEFAULT 0,
  FOREIGN KEY (noticia_id) REFERENCES noticias_talento(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='Galería de fotos por noticia del portal de talento';
```

---

## FASE 3 — Nueva Sección Beneficios ⏳ FUTURO

> **Origen:** Notas de la Gerencia de Marketing y Operaciones (3er Etapa).
> **Prioridad:** Media. No bloquea las fases anteriores.

Crear una sección/página independiente que destaque los **beneficios de trabajar en Batidos Pitaya**.

#### Archivos a crear / modificar
- **[NEW]** `beneficios.php` — Página de beneficios con íconos animados, visual premium
- **[MODIFY]** `layout_talento/header.php` — Agregar pestaña "Beneficios" al menú
- **[MODIFY]** `css/global.css` — Estilos para la sección de beneficios

> [!NOTE]
> El contenido puede ser estático inicialmente (plan de carrera, ambiente de trabajo, alimentación, reconocimientos, etc.). En el futuro se puede administrar desde una tabla `beneficios_talento` in BD.

---

## Resumen de Scripts SQL a Crear

Guardar todos en `\GitHub Pitaya\SQL\` y aplicar en Hostinger **antes** de activar el código correspondiente:

| Archivo | Fase | Contenido |
|---|---|---|
| `013_plazas_detalle_talento.sql` | 2A | Columnas de detalle en `plazas_cargos` + tabla `habilidades_talento` |
| `014_colaboradores_talento.sql` | 2B | Tabla `colaboradores_talento` |
| `015_noticias_talento.sql` | 2C | Tablas `noticias_talento` + `noticias_fotos_talento` |

---

## Verificación por Fase

### Fase 2A
- Tarjetas de vacante muestran descripción y responsabilidades cortas
- "Leer Más" abre el detalle correcto con todos los campos
- El admin de RRHH puede asignar habilidades al publicar la plaza (en el ERP)

### Fase 2B
- El carrusel de colaboradores funciona en móvil, tablet y desktop
- Las fotos y datos se cargan desde BD correctamente

### Fase 2C
- "Leer Más" en noticias redirige al detalle correcto
- Galería de fotos se despliega con lightbox
- Contenido completo es legible en todos los dispositivos

### Fase 3
- Nueva pestaña "Beneficios" activa en la navbar
- Contenido visual premium y responsivo

---

## FASE 4 — ERP: Módulo de Administración de Contenido del Portal de Talento ⏳ PENDIENTE (AL FINAL DEL PLAN)

> **Origen:** Propuesta de control interno del administrador.
> **Prioridad:** Media-Baja (después de las fases de frontend 2C y 3).
> **Herramienta ERP a registrar:** `talento_contenido`

### Especificación del Módulo ERP

Se creará una nueva sección en `erp.batidospitaya/modulos/talento_contenido/` para permitir que el personal administrativo con permisos gestione el carrusel de colaboradores y el catálogo de habilidades sin tocar directamente la base de datos.

#### Páginas y Flujos
1. **`colaboradores.php`** (Gestión de Colaboradores):
   - Grid/Lista de colaboradores registrados (`colaboradores_talento`).
   - Botón para agregar y formulario modal para editar (Nombre, Cargo, Departamento, Testimonio, Orden, Activo y Foto).
   - Manejo de subida de imágenes a la ruta compartida `talento.batidospitaya/uploads/equipo/`.
2. **`habilidades.php`** (Gestión del Catálogo de Habilidades):
   - Lista de habilidades predefinidas (`habilidades_talento`) agrupadas por categoría.
   - Formulario modal para crear y editar habilidades (Nombre, Categoría, Activo).

#### Permisos del ERP (Administración vía `gestion_permisos.php`)
- **Herramienta:** `talento_contenido`
- **Acciones:**
  - `vista` (acceso a las páginas de gestión)
  - `crear` (guardar registros nuevos)
  - `editar` (actualizar existentes y subir fotos)
  - `eliminar` (ocultar/eliminar del portal)

#### Archivos a crear en el ERP
- **[NEW]** `erp.batidospitaya/modulos/talento_contenido/colaboradores.php` — Interfaz de colaboradores
- **[NEW]** `erp.batidospitaya/modulos/talento_contenido/habilidades.php` — Interfaz de catálogo de habilidades
- **[NEW]** `erp.batidospitaya/modulos/talento_contenido/js/talento_contenido.js` — Lógica frontend, peticiones AJAX y modales
- **[NEW]** `erp.batidospitaya/modulos/talento_contenido/ajax/get_colaboradores.php` — Obtener lista o detalle
- **[NEW]** `erp.batidospitaya/modulos/talento_contenido/ajax/guardar_colaborador.php` — Guardar/actualizar y subir foto
- **[NEW]** `erp.batidospitaya/modulos/talento_contenido/ajax/eliminar_colaborador.php` — Desactivar o eliminar colaborador
- **[NEW]** `erp.batidospitaya/modulos/talento_contenido/ajax/get_habilidades.php` — Obtener catálogo de habilidades
- **[NEW]** `erp.batidospitaya/modulos/talento_contenido/ajax/guardar_habilidad.php` — Guardar/actualizar habilidad
- **[NEW]** `erp.batidospitaya/modulos/talento_contenido/ajax/eliminar_habilidad.php` — Desactivar o eliminar habilidad
- **[NEW]** `SQL/016_talento_contenido_permisos.sql` — Script para inicializar la herramienta `talento_contenido` y sus acciones en el sistema de permisos del ERP

