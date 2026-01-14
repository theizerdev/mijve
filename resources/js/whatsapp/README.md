# WhatsApp API v2 - Instituto Vargas

API completa de WhatsApp usando Baileys para el U.E Vargas II.

## 🚀 Inicio Rápido

### 1. Instalación
```bash
npm install
```

### 2. Configuración
Copia `.env.example` a `.env` y configura:
```bash
cp .env.example .env
```

### 3. Base de Datos
Ejecuta las migraciones:
```bash
# Crear base de datos 'vargas_centro' en MySQL
# Ejecutar migrations/001_create_tables.sql
# Ejecutar migrations/002_add_companies.sql
```

### 4. Iniciar Servidor
```bash
npm start
# o para desarrollo:
npm run dev
```

## 📱 Uso de la API

### Autenticación
Todas las rutas requieren headers:
```
X-API-Key: test-api-key-vargas-centro
X-Company-Id: 1
```

### Endpoints Principales

#### Estado de WhatsApp
```bash
GET /api/whatsapp/status
```

#### Obtener Código QR
```bash
GET /api/whatsapp/qr
```

#### Enviar Mensaje
```bash
POST /api/whatsapp/send
{
  "to": "4121234567",
  "message": "Hola desde WhatsApp API"
}
```

#### Ver Mensajes
```bash
GET /api/whatsapp/messages?page=1&limit=50
```

### 🧪 Prueba Rápida
```bash
node test-api.js
```

## 🔧 Configuración Avanzada

### Variables de Entorno Importantes
- `PORT`: Puerto del servidor (default: 3001)
- `DB_*`: Configuración de MySQL
- `WHATSAPP_SESSION_NAME`: Nombre de la sesión
- `CORS_ORIGIN`: Origen permitido para CORS

### Empresa de Prueba
Por defecto se incluye:
- **ID**: 1
- **API Key**: `test-api-key-vargas-centro`
- **Nombre**: U.E Vargas II

## 📊 Monitoreo

### Health Check
```bash
GET /health
```

### Logs
Los logs se guardan en `logs/`:
- `combined-YYYY-MM-DD.log`: Todos los logs
- `error-YYYY-MM-DD.log`: Solo errores
- `whatsapp-YYYY-MM-DD.log`: Logs específicos de WhatsApp

## 🔒 Seguridad

- Rate limiting por empresa
- Validación de API keys
- Headers de seguridad con Helmet
- Validación de entrada con express-validator

## 📁 Estructura del Proyecto

```
src/
├── config/          # Configuración de DB y Redis
├── controllers/     # Controladores de rutas
├── middleware/      # Middlewares personalizados
├── models/          # Modelos de Sequelize
├── routes/          # Definición de rutas
├── services/        # Lógica de negocio
└── utils/           # Utilidades y helpers
```

## 🐛 Solución de Problemas

### Error: "API key inválida"
- Verifica que uses el header `X-API-Key: test-api-key-vargas-centro`
- Asegúrate de incluir `X-Company-Id: 1`

### WhatsApp no conecta
- Revisa los logs en `logs/whatsapp-*.log`
- Verifica que el directorio `storage/sessions` tenga permisos
- Escanea el código QR desde `/api/whatsapp/qr`

### Base de datos
- Verifica que MySQL esté ejecutándose
- Confirma las credenciales en `.env`
- Ejecuta las migraciones SQL manualmente si es necesario