# Sistema de Notificaciones por Email - Control de Acceso

## 📧 Descripción

Sistema automático de notificaciones por correo electrónico que informa a los representantes cuando sus representados ingresan o salen del plantel educativo.

## ✨ Características

### Notificación de Entrada
- ✅ Email automático al representante
- ✅ Información del estudiante
- ✅ Fecha y hora de entrada
- ✅ Observaciones (si las hay)

### Notificación de Salida
- ✅ Email automático al representante
- ✅ Información del estudiante
- ✅ Fecha y hora de salida
- ✅ **Tiempo total en el plantel** (calculado automáticamente)
- ✅ Observaciones (si las hay)

## 📋 Requisitos

### 1. Configuración de Email en Laravel

Editar `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-contraseña-de-aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Estudiante con Representante

El estudiante debe tener:
- `representante_nombres`
- `representante_apellidos`
- `representante_correo` (obligatorio para enviar notificaciones)

## 🔧 Componentes Creados

### 1. Mailable
**Archivo:** `app/Mail/StudentAccessNotificationMail.php`

```php
- Recibe: Student, StudentAccessLog, timeInSchool
- Genera: Email formateado con toda la información
```

### 2. Vista de Email
**Archivo:** `resources/views/emails/student-access-notification.blade.php`

**Contenido:**
- Header con color según tipo (verde=entrada, rojo=salida)
- Información del estudiante
- Datos del acceso
- Tiempo en el plantel (solo en salidas)
- Footer con información del sistema

### 3. Job Asíncrono
**Archivo:** `app/Jobs/SendAccessNotificationJob.php`

**Funcionalidad:**
- Envía emails de forma asíncrona
- No bloquea el registro de acceso
- Calcula tiempo en el plantel
- Maneja errores automáticamente

### 4. Actualización del Componente
**Archivo:** `app/Livewire/Admin/Students/QrAccess.php`

**Método agregado:**
```php
sendAccessNotification($accessLog)
- Verifica que exista correo del representante
- Despacha el Job para envío asíncrono
```

## 📊 Flujo de Trabajo

### Entrada del Estudiante
```
1. Estudiante escanea QR / ingresa código
2. Sistema registra entrada en base de datos
3. Job se despacha para enviar email
4. Representante recibe email de entrada
```

### Salida del Estudiante
```
1. Estudiante escanea QR / ingresa código
2. Sistema busca entrada del día
3. Calcula tiempo transcurrido
4. Registra salida en base de datos
5. Job se despacha para enviar email
6. Representante recibe email con tiempo en plantel
```

## ⏱️ Cálculo de Tiempo en el Plantel

El sistema calcula automáticamente:

```php
Entrada: 08:00 AM
Salida:  02:30 PM
Tiempo: 6 horas y 30 minutos
```

**Formato de visualización:**
- Si hay horas: "X hora(s) y Y minuto(s)"
- Solo minutos: "Y minuto(s)"

## 🎨 Diseño del Email

### Colores
- **Entrada:** Verde (#28a745)
- **Salida:** Rojo (#dc3545)
- **Tiempo:** Azul (#007bff)

### Estructura
```
┌─────────────────────────────┐
│  Header (Color según tipo)  │
├─────────────────────────────┤
│  Saludo al representante    │
│  Mensaje informativo        │
│                             │
│  ┌───────────────────────┐  │
│  │ Información Estudiante│  │
│  │ - Nombre              │  │
│  │ - Código              │  │
│  │ - Grado               │  │
│  │ - Tipo de acceso      │  │
│  │ - Fecha y hora        │  │
│  │ - Observaciones       │  │
│  └───────────────────────┘  │
│                             │
│  [Tiempo en plantel]        │ (solo salida)
│                             │
│  Nota informativa           │
├─────────────────────────────┤
│  Footer                     │
└─────────────────────────────┘
```

## 🚀 Configuración de Colas (Opcional)

Para envío asíncrono eficiente:

### 1. Configurar Queue Driver

`.env`:
```env
QUEUE_CONNECTION=database
```

### 2. Crear tabla de jobs
```bash
php artisan queue:table
php artisan migrate
```

### 3. Ejecutar worker
```bash
php artisan queue:work
```

### 4. Supervisor (Producción)
```ini
[program:larawire-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/worker.log
```

## 📝 Validaciones

### El sistema NO envía email si:
- El estudiante no tiene `representante_correo`
- El correo está vacío o es null
- Hay error en la configuración de email

### El sistema SÍ envía email si:
- Estudiante tiene correo de representante válido
- Configuración de email es correcta
- Registro de acceso se creó exitosamente

## 🧪 Pruebas

### Probar envío de email:

```php
php artisan tinker

use App\Models\Student;
use App\Models\StudentAccessLog;
use App\Jobs\SendAccessNotificationJob;

$student = Student::find(1);
$log = StudentAccessLog::latest()->first();

SendAccessNotificationJob::dispatch($student, $log);
```

### Verificar configuración:

```bash
php artisan config:clear
php artisan config:cache
```

## 🔐 Seguridad

### Gmail - Contraseña de Aplicación

1. Ir a cuenta de Google
2. Seguridad → Verificación en 2 pasos
3. Contraseñas de aplicaciones
4. Generar nueva contraseña
5. Usar en `.env`

### Otros Proveedores

**Mailtrap (Testing):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu-username
MAIL_PASSWORD=tu-password
```

**SendGrid:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=tu-api-key
```

## 📊 Logs y Monitoreo

### Ver logs de email:
```bash
tail -f storage/logs/laravel.log | grep "Error enviando"
```

### Ver jobs fallidos:
```bash
php artisan queue:failed
```

### Reintentar jobs fallidos:
```bash
php artisan queue:retry all
```

## 🐛 Solución de Problemas

### Email no se envía

1. **Verificar configuración:**
```bash
php artisan config:clear
php artisan tinker
>>> config('mail')
```

2. **Verificar correo del representante:**
```php
$student = Student::find(ID);
echo $student->representante_correo;
```

3. **Probar conexión SMTP:**
```bash
telnet smtp.gmail.com 587
```

### Email va a spam

- Configurar SPF, DKIM, DMARC
- Usar dominio propio
- Evitar palabras spam
- Incluir link de unsuscribe

### Job no se ejecuta

1. **Verificar queue worker:**
```bash
ps aux | grep queue:work
```

2. **Iniciar worker:**
```bash
php artisan queue:work
```

3. **Ver jobs pendientes:**
```bash
php artisan queue:monitor
```

## 📈 Mejoras Futuras

1. **Plantillas personalizables**
   - Permitir personalizar diseño
   - Agregar logo de la institución
   - Colores personalizados

2. **Notificaciones adicionales**
   - SMS al representante
   - Notificaciones push
   - WhatsApp Business API

3. **Reportes**
   - Resumen diario por email
   - Alertas de ausencias
   - Estadísticas semanales

4. **Preferencias**
   - Permitir desactivar notificaciones
   - Elegir tipo de notificaciones
   - Horarios de envío

## 📞 Soporte

Para problemas con emails:
1. Verificar logs: `storage/logs/laravel.log`
2. Revisar configuración de `.env`
3. Probar con Mailtrap primero
4. Verificar firewall/puertos

---

**Sistema implementado y listo para usar** ✅
