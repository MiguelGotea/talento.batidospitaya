# 🚀 Talento Batidos Pitaya

Repositorio del portal de talento para Batidos Pitaya.

## 📦 Estructura del Proyecto

- `ajax/`, `assets/`, `css/`, `js/`: Recursos y handlers del frontend.
- `core/`: Lógica central (gestionada independientemente del deploy automático).
- `.github/workflows/`: Workflows de GitHub Actions para deploy automático.
- `.scripts/`: Scripts auxiliares de PowerShell.

## 🚀 Deploy Automático

Este repositorio utiliza **GitHub Actions** para desplegar automáticamente los cambios en el servidor de producción Hostinger.

### Exclusiones de Deploy
Por seguridad y consistencia, el deploy **NO** sincroniza:
- `.agent`, `.scripts`, `core`, `docs`, `uploads`, `.htaccess`.
- Archivos de documentación y configuración de Git.

### Documentación de Deploy

El sistema de deploy está estandarizado. La documentación completa se encuentra en el repositorio del ERP:

- [**Guía de Configuración General**](https://github.com/MiguelGotea/erp.batidospitaya/blob/main/docs/DEPLOY_SETUP.md)
- [**Implementar Nuevo Dominio**](https://github.com/MiguelGotea/erp.batidospitaya/blob/main/docs/DEPLOY_NEW_DOMAIN.md)

---

## 🛠️ Desarrollo Local

### Scripts de Ayuda
Usa el script en `.scripts/` para realizar pushes rápidos:
- `.\.scripts\gitpush.ps1`: Sube todos los cambios y activa el deploy.

---

**Última actualización:** 2026-02-17
