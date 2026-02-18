# Instrucciones de Implementación

## Portal de Empleo Público - talento.batidospitaya.com

---

## 📋 Checklist de Implementación

### 1. Base de Datos

- [ ] Ejecutar script SQL para crear tabla `postulacion_plaza`
  ```bash
  mysql -u usuario -p u839374897_erp < database/create_postulacion_plaza.sql
  ```

- [ ] Verificar que la tabla se creó correctamente
  ```sql
  SHOW TABLES LIKE 'postulacion_plaza';
  DESCRIBE postulacion_plaza;
  ```

### 2. Configuración de Carpetas

- [ ] Crear carpeta para uploads de CVs
  ```bash
  mkdir -p uploads/cv
  chmod 755 uploads/cv
  ```

- [ ] Crear carpeta para assets
  ```bash
  mkdir -p assets/img
  chmod 755 assets/img
  ```

- [ ] Verificar permisos
  ```bash
  ls -la uploads/
  ls -la assets/
  ```

### 3. Archivos de Imágenes

Necesitas agregar las siguientes imágenes en `assets/img/`:

- [ ] `favicon.png` - Ícono del sitio (32x32px)
- [ ] `logo.png` - Logo de Batidos Pitaya (transparente)
- [ ] `hero-image.png` - Imagen del hero section (800x600px aprox)
- [ ] `og-image.jpg` - Imagen para Open Graph (1200x630px)

### 4. Configuración de Plazas

Para que las plazas aparezcan en el portal, deben cumplir:

- [ ] En tabla `plazas_cargos`, campo `visible_web = 1`
- [ ] La sucursal debe estar activa (`sucursales.activa = 1`)
- [ ] Debe haber plazas disponibles (cantidad_real + cantidad_adicional > cantidad_cubierta)

**Ejemplo de actualización**:
```sql
UPDATE plazas_cargos 
SET visible_web = 1 
WHERE id IN (1, 2, 3); -- IDs de las plazas que quieres mostrar
```

### 5. Verificación de Conexión

- [ ] Verificar que `core/database/conexion.php` existe y funciona
- [ ] Probar conexión desde el portal:
  ```bash
  php -r "require 'core/database/conexion.php'; echo 'Conexión OK';"
  ```

### 6. Configuración SEO

- [ ] Actualizar URL en `sitemap.xml.php` (líneas 13, 19, 25, 48)
- [ ] Actualizar URL en `robots.txt` (línea 26)
- [ ] Actualizar URLs en meta tags de `index.php`

### 7. Testing

- [ ] Probar carga de plazas: `ajax/get_plazas.php`
- [ ] Probar detalle de plaza: `ajax/get_plaza_detalle.php?id=1`
- [ ] Probar formulario de postulación completo
- [ ] Verificar upload de CV (PDF, max 10MB)
- [ ] Probar en diferentes navegadores
- [ ] Probar en dispositivos móviles

### 8. SEO y Validación

- [ ] Verificar sitemap: https://talento.batidospitaya.com/sitemap.xml.php
- [ ] Verificar robots.txt: https://talento.batidospitaya.com/robots.txt
- [ ] Validar HTML: https://validator.w3.org/
- [ ] Validar Schema.org: https://search.google.com/test/rich-results
- [ ] Registrar en Google Search Console
- [ ] Enviar sitemap a Google

### 9. Seguridad

- [ ] Verificar que carpeta `core/` no es accesible públicamente
- [ ] Verificar que carpeta `ajax/` no lista archivos
- [ ] Verificar que carpeta `uploads/` no lista archivos
- [ ] Configurar HTTPS (certificado SSL)
- [ ] Configurar headers de seguridad

### 10. Monitoreo

- [ ] Configurar logs de errores PHP
- [ ] Monitorear espacio en disco (carpeta uploads)
- [ ] Configurar backups de base de datos
- [ ] Configurar alertas de postulaciones nuevas

---

## 🔧 Configuraciones Adicionales

### Apache (.htaccess)

Crear archivo `.htaccess` en la raíz:

```apache
# Forzar HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Proteger archivos sensibles
<FilesMatch "\.(sql|md|log)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Prevenir listado de directorios
Options -Indexes

# Configurar tipos MIME
AddType application/pdf .pdf

# Compresión GZIP
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# Cache de assets
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### PHP (php.ini o .user.ini)

```ini
; Tamaño máximo de upload
upload_max_filesize = 10M
post_max_size = 12M

; Tiempo de ejecución
max_execution_time = 30

; Zona horaria
date.timezone = America/Managua

; Mostrar errores (solo en desarrollo)
display_errors = Off
log_errors = On
error_log = /ruta/a/logs/php_errors.log
```

---

## 📊 Consultas SQL Útiles

### Ver postulaciones recientes
```sql
SELECT 
    id, nombre, correo, cargo_aplicado, 
    status, fecha_postulacion
FROM postulacion_plaza
ORDER BY fecha_postulacion DESC
LIMIT 10;
```

### Contar postulaciones por cargo
```sql
SELECT 
    nc.Nombre as cargo,
    COUNT(*) as total_postulaciones,
    SUM(CASE WHEN pp.status = 'solicitado' THEN 1 ELSE 0 END) as pendientes,
    SUM(CASE WHEN pp.status = 'aprobado' THEN 1 ELSE 0 END) as aprobados
FROM postulacion_plaza pp
JOIN NivelesCargos nc ON nc.CodNivelesCargos = pp.cargo_aplicado
GROUP BY pp.cargo_aplicado
ORDER BY total_postulaciones DESC;
```

### Ver plazas visibles en el portal
```sql
SELECT 
    pc.id,
    nc.Nombre as cargo,
    s.nombre as sucursal,
    pc.cantidad_real,
    pc.salario_propuesto,
    pc.nivel_urgencia,
    pc.visible_web
FROM plazas_cargos pc
JOIN NivelesCargos nc ON nc.CodNivelesCargos = pc.cargo
JOIN sucursales s ON s.codigo = CAST(pc.sucursal AS CHAR)
WHERE pc.visible_web = 1
ORDER BY pc.nivel_urgencia DESC;
```

---

## 🚨 Troubleshooting

### Problema: No se muestran plazas

**Solución**:
1. Verificar que `visible_web = 1` en `plazas_cargos`
2. Verificar que hay plazas disponibles (cantidad > 0)
3. Revisar logs de errores en `ajax/get_plazas.php`

### Problema: Error al subir CV

**Solución**:
1. Verificar permisos de carpeta `uploads/cv/` (755)
2. Verificar tamaño del archivo (max 10MB)
3. Verificar que es formato PDF
4. Revisar configuración PHP (`upload_max_filesize`)

### Problema: Sitemap no funciona

**Solución**:
1. Renombrar `sitemap.xml.php` a `sitemap.xml` si el servidor no ejecuta PHP
2. O configurar rewrite rule en `.htaccess`:
   ```apache
   RewriteRule ^sitemap\.xml$ sitemap.xml.php [L]
   ```

---

## 📞 Contacto

Para soporte técnico o dudas sobre la implementación, contactar al equipo de TI de Batidos Pitaya.

---

**Última actualización**: Febrero 2026
