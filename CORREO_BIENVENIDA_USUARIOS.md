# Sistema de Correo de Bienvenida para Usuarios

## 📧 Descripción

Sistema automático que envía un correo de bienvenida con las credenciales de acceso cuando se crea un nuevo usuario en el sistema.

## ✨ Características

### Correo de Bienvenida Incluye:
- ✅ Saludo personalizado con el nombre del usuario
- ✅ **Email/Usuario** para iniciar sesión
- ✅ **Contraseña** generada
- ✅ **URL del sistema** para acceder
- ✅ Botón directo para iniciar sesión
- ✅ Rol asignado al usuario
- ✅ Recomendaciones de seguridad
- ✅ Diseño profesional y responsive

## 🎯 Flujo de Trabajo

```
1. Administrador crea nuevo usuario
   ↓
2. Sistema guarda usuario en base de datos
   ↓
3. Sistema asigna rol al usuario
   ↓
4. Sistema envía correo de bienvenida
   ↓
5. Usuario recibe email con credenciales
   ↓
6. Usuario accede al sistema
```

## 📋 Información en el Email

### Credenciales de Acceso
```
┌─────────────────────────────────┐
│ Usuario/Email: user@example.com │
│ Contraseña: password123         │
│ URL: http://localhost           │
└─────────────────────────────────┘
```

### Recomendaciones de Seguridad
- Guardar contraseña en lugar seguro
- Cambiar contraseña después del primer login
- No compartir credenciales
- Contactar administrador si no solicitó la cuenta

### Información Adicional
- Rol asignado (Admin, Usuario, etc.)
- Enlace directo al login
- Información de contacto

## 🔧 Archivos Creados

### 1. Mailable
**Archivo:** `app/Mail/UserWelcomeMail.php`

```php
- Recibe: User, password (texto plano)
- Genera: Email formateado con credenciales
```

### 2. Vista de Email
**Archivo:** `resources/views/emails/user-welcome.blade.php`

**Diseño:**
- Header con gradiente morado
- Icono de celebración 🎉
- Caja destacada con credenciales
- Botón de acceso directo
- Advertencias de seguridad
- Footer informativo

### 3. Componente Actualizado
**Archivo:** `app/Livewire/Admin/Users/Create.php`

**Cambios:**
```php
// Guarda contraseña en texto plano antes de hashear
$plainPassword = $this->password;

// Crea usuario con contraseña hasheada
$user->password = Hash::make($plainPassword);

// Envía email con contraseña en texto plano
Mail::to($user->email)->send(new UserWelcomeMail($user, $plainPassword));
```

## 🎨 Diseño del Email

### Colores
- **Header:** Gradiente morado (#667eea → #764ba2)
- **Botón:** Azul (#667eea)
- **Advertencia:** Amarillo (#fff3cd)
- **Rol:** Azul claro (#e7f3ff)

### Estructura
```
┌─────────────────────────────────┐
│  Header (Gradiente morado)      │
│  🎉 ¡Bienvenido al Sistema!     │
├─────────────────────────────────┤
│  Saludo personalizado           │
│                                 │
│  ┌───────────────────────────┐  │
│  │ 📋 Credenciales de Acceso │  │
│  │ - Usuario/Email           │  │
│  │ - Contraseña              │  │
│  │ - URL del Sistema         │  │
│  └───────────────────────────┘  │
│                                 │
│  [🔐 Acceder al Sistema]        │
│                                 │
│  ⚠️ Recomendaciones             │
│  - Guardar contraseña           │
│  - Cambiar después del login    │
│  - No compartir credenciales    │
│                                 │
│  👤 Rol asignado: Admin         │
├─────────────────────────────────┤
│  Footer                         │
└─────────────────────────────────┘
```

## 🔐 Seguridad

### Buenas Prácticas Implementadas

1. **Contraseña en texto plano solo en memoria:**
   - Se guarda temporalmente antes de hashear
   - Se envía por email una sola vez
   - No se almacena en texto plano en BD

2. **Recomendaciones al usuario:**
   - Cambiar contraseña en primer login
   - No compartir credenciales
   - Guardar en lugar seguro

3. **Validación de email:**
   - Email debe ser único
   - Formato válido requerido

### Consideraciones Adicionales

**Para mayor seguridad (opcional):**
- Generar contraseña temporal aleatoria
- Forzar cambio de contraseña en primer login
- Enviar link de activación en lugar de contraseña
- Implementar expiración de contraseña temporal

## 📊 Ejemplo de Uso

### Crear Usuario desde Admin

1. Ir a **Usuarios → Crear**
2. Llenar formulario:
   ```
   Nombre: Juan Pérez
   Email: juan@example.com
   Contraseña: MiPassword123
   Empresa: Empresa ABC
   Sucursal: Sucursal Principal
   Rol: Admin
   ```
3. Clic en **Guardar**
4. Sistema muestra: "Usuario creado correctamente. Se ha enviado un correo con las credenciales."
5. Usuario recibe email en `juan@example.com`

### Email Recibido

```
Para: juan@example.com
Asunto: Bienvenido al Sistema - Credenciales de Acceso

Hola Juan Pérez,

Tu cuenta ha sido creada exitosamente...

Usuario/Email: juan@example.com
Contraseña: MiPassword123
URL: http://localhost

[Acceder al Sistema]

Rol asignado: Admin
```

## 🧪 Pruebas

### Probar envío de email:

```php
php artisan tinker

use App\Models\User;
use App\Mail\UserWelcomeMail;
use Illuminate\Support\Facades\Mail;

$user = User::first();
Mail::to($user->email)->send(new UserWelcomeMail($user, 'password123'));
```

### Verificar en Mailpit:

1. Abrir navegador: `http://localhost:8025`
2. Ver emails recibidos
3. Verificar contenido y formato

### Verificar en Gmail (producción):

1. Configurar `.env` con credenciales Gmail
2. Crear usuario de prueba
3. Verificar email en bandeja de entrada

## ⚙️ Configuración

### Mailpit (Desarrollo)
```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Gmail (Producción)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-contraseña-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 🐛 Solución de Problemas

### Email no se envía

1. **Verificar logs:**
```bash
tail -f storage/logs/laravel.log | grep "Error enviando correo"
```

2. **Verificar configuración:**
```bash
php artisan config:clear
php artisan tinker
>>> config('mail')
```

3. **Probar conexión:**
```bash
php artisan tinker
>>> Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

### Email va a spam

- Usar dominio verificado
- Configurar SPF/DKIM
- Evitar palabras spam
- Usar servicio profesional (SendGrid, Mailgun)

### Usuario no recibe email

1. Verificar email correcto en formulario
2. Revisar carpeta de spam
3. Verificar logs del servidor
4. Probar con otro email

## 📈 Mejoras Futuras

1. **Contraseña temporal:**
   - Generar contraseña aleatoria
   - Forzar cambio en primer login
   - Expiración después de 24 horas

2. **Link de activación:**
   - Enviar link en lugar de contraseña
   - Usuario crea su propia contraseña
   - Mayor seguridad

3. **Personalización:**
   - Logo de la empresa
   - Colores personalizados
   - Mensaje personalizado del admin

4. **Notificaciones adicionales:**
   - Email de confirmación de cambio de contraseña
   - Email de cambio de rol
   - Email de desactivación de cuenta

## 📞 Soporte

Para problemas con correos de bienvenida:
1. Verificar logs: `storage/logs/laravel.log`
2. Revisar configuración de `.env`
3. Probar con Mailpit primero
4. Verificar que el email del usuario sea válido

---

**Sistema implementado y listo para usar** ✅

El correo de bienvenida se envía automáticamente al crear cada nuevo usuario.
