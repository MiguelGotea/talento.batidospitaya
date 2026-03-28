# Script de Deploy Automatico - Talento Batidos Pitaya
# Uso: .\.scripts\gitpush.ps1

$date = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
$message = "$date"

Write-Host "Iniciando proceso de push para Talento..." -ForegroundColor Cyan

# Agregar cambios
git add .

# Commit
git commit -m "$message"

# Pull previo por si hubo cambios remotos (sync)
Write-Host "Sincronizando con cambios remotos..." -ForegroundColor Yellow
git pull origin main --rebase

# Push
Write-Host "Subiendo a GitHub..." -ForegroundColor Yellow
git push origin main

Write-Host "Proceso completado. GitHub Actions iniciara el deploy en breve." -ForegroundColor Green
