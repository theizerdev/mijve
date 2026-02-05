# Configuración Multi-Empresa WhatsApp

Este documento describe cómo configurar el sistema para que múltiples empresas puedan tener su propia conexión de WhatsApp independiente.

## Arquitectura

```
┌─────────────────────────────────────────────────────────────────┐
│                         LARAVEL (vargas)                        │
├─────────────────────────────────────────────────────────────────┤
│  Empresa 1                  Empresa 2                Empresa N  │
│  ├─ api_key: vg_xxx        ├─ api_key: vg_yyy      ├─ api_key  │
│  ├─ whatsapp_api_key:      ├─ whatsapp_api_key:    ├─ whatsapp │
│  │  wa_1_abc123            │  wa_2_def456          │  _api_key │
│  └─ whatsapp_status:       └─ whatsapp_status:     └─ ...      │
│     connected                 disconnected                      │
└──────────────────────────────┬──────────────────────────────────┘
                               │ HTTP (X-API-Key + X-Company-Id)
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│                      NODE.JS API (whatsapp)                     │
├─────────────────────────────────────────────────────────────────┤
│                      WhatsAppManager                            │
│  ┌─────────────┐   ┌─────────────┐   ┌─────────────┐           │
│  │ Company 1   │   │ Company 2   │   │ Company N   │           │
│  │ (Service)   │   │ (Service)   │   │ (Service)   │           │
│  │ sessions/   │   │ sessions/   │   │ sessions/   │           │
│  │ company_1/  │   │ company_2/  │   │ company_N/  │           │
│  └─────────────┘   └─────────────┘   └─────────────┘           │
└─────────────────────────────────────────────────────────────────┘
```

## Campos en la Tabla `empresas` (Laravel)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `api_key` | string | API key general de la empresa (para autenticación Laravel) |
| `whatsapp_api_key` | string | API key específica para WhatsApp API (formato: `wa_{id}_{random}`) |
| `whatsapp_rate_limit` | integer | Límite de mensajes por minuto (default: 100) |
| `whatsapp_active` | boolean | Si la integración WhatsApp está activa |
| `whatsapp_phone` | string | Número de WhatsApp conectado |
| `whatsapp_status` | enum | Estado: `disconnected`, `connecting`, `connected`, `qr_ready` |
| `whatsapp_last_connected` | timestamp | Última conexión exitosa |

## Instalación

### 1. Ejecutar migraciones en Laravel

```bash
cd c:\laragon\www\vargas

# Ejecutar todas las migraciones pendientes
php artisan migrate
```

### 2. Sincronizar empresas existentes

```bash
# Genera whatsapp_api_key para todas las empresas que no la tienen
php artisan whatsapp:sync-companies

# Para una empresa específica
php artisan whatsapp:sync-companies --empresa=1

# Para regenerar todas (incluso las que ya tienen)
php artisan whatsapp:sync-companies --all
```

### 3. Ejecutar migraciones en Node.js (WhatsApp API)

```bash
cd c:\laragon\www\whatsapp

# Conectar a MySQL y ejecutar:
mysql -u root -p vargas_centro < migrations/004_multi_company_enhancements.sql
```

### 4. Reiniciar servicios

```bash
# En una terminal - Laravel
cd c:\laragon\www\vargas
php artisan serve

# En otra terminal - WhatsApp API
cd c:\laragon\www\whatsapp
npm start
```

## Uso en Código PHP

### Opción 1: Automático (usa empresa del usuario logueado)

```php
use App\Services\WhatsAppService;

$whatsapp = new WhatsAppService();
$whatsapp->sendMessage('04121234567', 'Hola desde la empresa del usuario');
```

### Opción 2: Para una empresa específica

```php
use App\Services\WhatsAppService;
use App\Models\Empresa;

$empresa = Empresa::find(2);
$whatsapp = WhatsAppService::forCompany($empresa);
$whatsapp->sendMessage('04121234567', 'Hola desde empresa 2');
```

### Opción 3: Desde el modelo Empresa

```php
$empresa = Empresa::find(1);
$empresa->getWhatsAppService()->sendMessage('04121234567', 'Hola');
```

### Verificar estado

```php
$empresa = Empresa::find(1);

if ($empresa->hasWhatsAppConfigured()) {
    $status = $empresa->getWhatsAppService()->getStatus();
    
    if ($empresa->isWhatsAppConnected()) {
        // WhatsApp listo para enviar mensajes
    }
}
```

## Flujo al Crear una Nueva Empresa

1. Usuario crea empresa desde `Admin > Empresas > Crear`
2. `EmpresaObserver::created()` se dispara automáticamente
3. `WhatsAppApiIntegrationService::createCompany()` genera `whatsapp_api_key`
4. Se intenta registrar en la API de Node.js
5. La empresa queda lista para conectar WhatsApp

## Endpoints de la API Node.js

Todos requieren headers:
- `X-API-Key`: El `whatsapp_api_key` de la empresa
- `X-Company-Id`: El ID de la empresa

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/whatsapp/status` | Estado de conexión de la empresa |
| GET | `/api/whatsapp/qr` | Obtener código QR para conectar |
| POST | `/api/whatsapp/connect` | Iniciar conexión |
| DELETE | `/api/whatsapp/disconnect` | Desconectar |
| POST | `/api/whatsapp/send` | Enviar mensaje de texto |
| POST | `/api/whatsapp/send-document` | Enviar documento |
| GET | `/api/whatsapp/messages` | Historial de mensajes |
| POST | `/api/whatsapp/reconnect` | Reconectar |
| DELETE | `/api/whatsapp/session` | Eliminar sesión completamente |
| GET | `/api/whatsapp/manager/stats` | Estadísticas de todas las empresas |

## Solución de Problemas

### Error: "API Key no configurada"

```bash
# Verificar que la empresa tenga whatsapp_api_key
php artisan tinker
>>> App\Models\Empresa::find(1)->whatsapp_api_key

# Si está vacío, sincronizar
php artisan whatsapp:sync-companies --empresa=1
```

### Error: "WhatsApp service not initialized"

El servicio de Node.js no está corriendo o no puede conectar:

```bash
cd c:\laragon\www\whatsapp
npm start
```

### WhatsApp no conecta después de escanear QR

Verificar que la carpeta de sesión tenga permisos:

```bash
# En Node.js, las sesiones se guardan en:
storage/sessions/company_{id}/
```

### Empresa no aparece en el manager

```bash
# Verificar que exista en la tabla companies de Node.js
mysql -u root -p vargas_centro -e "SELECT * FROM companies"
```

## Archivos Clave

### Laravel (vargas)

| Archivo | Descripción |
|---------|-------------|
| `app/Models/Empresa.php` | Modelo con campos y métodos WhatsApp |
| `app/Services/WhatsAppService.php` | Servicio que envía requests a Node.js |
| `app/Services/WhatsAppApiIntegrationService.php` | Sincroniza empresas con API |
| `app/Observers/EmpresaObserver.php` | Auto-sincroniza al crear/actualizar empresa |
| `app/Livewire/Admin/Whatsapp/Conexion.php` | UI para conectar WhatsApp |
| `app/Console/Commands/SyncWhatsAppCompanies.php` | Comando para sincronizar |

### Node.js (whatsapp)

| Archivo | Descripción |
|---------|-------------|
| `src/services/WhatsAppManager.js` | Gestor de múltiples instancias |
| `src/services/WhatsAppService.js` | Servicio por empresa |
| `src/controllers/WhatsAppController.js` | Controlador de API |
| `src/models/Company.js` | Modelo Sequelize de empresas |

## Notas de Seguridad

- Cada empresa tiene su propia `whatsapp_api_key` única
- Las sesiones de WhatsApp se almacenan en carpetas separadas por empresa
- El middleware valida que la API key corresponda a una empresa activa
- Rate limiting se aplica por empresa
