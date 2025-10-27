# Sistema de Control de Acceso con QR - Resumen Completo

## 📋 Descripción General

Sistema completo de control de acceso para estudiantes mediante códigos QR, integrado con Laravel 11, Livewire 3 y plantilla Materialize.

## 🎯 Características Principales

### 1. **Escaneo de Códigos QR**
- ✅ Escaneo por cámara en tiempo real
- ✅ Entrada manual de códigos (alternativa)
- ✅ Detección automática del tipo de acceso (entrada/salida)
- ✅ Validación de estudiantes activos

### 2. **Gestión de Accesos**
- ✅ Registro de entradas y salidas
- ✅ Prevención de duplicados (no permite 2 entradas o 2 salidas el mismo día)
- ✅ Notas opcionales para cada registro
- ✅ Auditoría completa (quién registró cada acceso)

### 3. **Estadísticas en Tiempo Real**
- ✅ Entradas del día
- ✅ Salidas del día
- ✅ Total de accesos
- ✅ Estudiantes activos en el sistema

### 4. **Información del Estudiante**
- ✅ Foto del estudiante
- ✅ Datos personales (nombres, documento, código)
- ✅ Información académica (grado, sección, nivel, turno)
- ✅ Indicador de menor de edad
- ✅ Último acceso registrado

### 5. **Historial y Reportes**
- ✅ Registros del día en tiempo real
- ✅ Filtrado y búsqueda
- ✅ Eliminación de registros (solo administradores)

### 6. **Notificaciones**
- ✅ Alertas visuales (toasts)
- ✅ Sonidos de notificación (activables/desactivables)
- ✅ Feedback inmediato de acciones

## 🗂️ Estructura del Proyecto

### Modelos de Base de Datos

#### **Student** (`app/Models/Student.php`)
```php
- nombres, apellidos
- fecha_nacimiento
- codigo (único)
- documento_identidad
- grado, seccion
- nivel_educativo_id
- turno_id
- school_periods_id
- foto
- status (activo/inactivo)
- representante_* (para menores de edad)
```

**Métodos importantes:**
- `generateQrCode()` - Genera código QR en SVG
- `generateQrCodePng()` - Genera código QR en PNG
- `getEdadAttribute()` - Calcula edad del estudiante
- `getEsMenorDeEdadAttribute()` - Verifica si es menor de edad

#### **StudentAccessLog** (`app/Models/StudentAccessLog.php`)
```php
- student_id
- type (entrada/salida)
- access_time
- registered_by (user_id)
- notes
```

**Relaciones:**
- `student()` - Pertenece a un estudiante
- `registeredBy()` - Usuario que registró el acceso

#### **AccessRecord** (`app/Models/AccessRecord.php`)
Modelo alternativo para registros más detallados:
```php
- student_id
- date
- entry_time, exit_time
- entry_user_id, exit_user_id
- access_type, access_method
- reference_code
- observations
```

### Componente Livewire

#### **QrAccess** (`app/Livewire/Admin/Students/QrAccess.php`)

**Propiedades públicas:**
```php
$search              // Búsqueda de estudiantes
$selectedStudent     // Estudiante seleccionado
$accessType          // 'entrada' o 'salida'
$notes              // Notas del registro
$soundEnabled       // Sonidos activados/desactivados
$scanMode           // 'camera' o 'manual'
$manualCode         // Código ingresado manualmente
$showStudentInfo    // Mostrar info del estudiante
$todayLogs          // Registros del día
$stats              // Estadísticas
```

**Métodos principales:**
```php
mount()                      // Inicialización
loadStats()                  // Cargar estadísticas
loadTodayLogs()             // Cargar registros del día
processQrScan($qrData)      // Procesar QR escaneado
extractStudentCode($qrData) // Extraer código del QR
searchByManualCode()        // Buscar por código manual
findStudentByCode($code)    // Buscar estudiante
determineAccessType()       // Determinar entrada/salida
registerAccess()            // Registrar acceso
resetForm()                 // Limpiar formulario
toggleSound()               // Activar/desactivar sonidos
deleteLog($logId)           // Eliminar registro
```

### Vista Blade

#### **qr-access.blade.php** (`resources/views/livewire/admin/students/qr-access.blade.php`)

**Secciones:**
1. **Estadísticas** (4 tarjetas)
   - Entradas del día
   - Salidas del día
   - Total de accesos
   - Estudiantes activos

2. **Escáner QR**
   - Modo cámara con html5-qrcode
   - Modo manual con input
   - Controles de sonido
   - Toggle entre modos

3. **Información del Estudiante**
   - Foto/Avatar
   - Datos personales
   - Información académica
   - Selector de tipo de acceso
   - Campo de notas
   - Botones de acción

4. **Historial de Accesos**
   - Tabla con registros del día
   - Información completa de cada acceso
   - Botón de eliminar (solo admin)

## 🚀 Instalación y Configuración

### 1. Requisitos Previos
```bash
- PHP 8.2+
- Laravel 11
- MySQL/MariaDB
- Composer
- Node.js & NPM
```

### 2. Dependencias PHP (ya instaladas)
```bash
composer require bacon/bacon-qr-code
composer require endroid/qr-code
composer require livewire/livewire
composer require spatie/laravel-permission
```

### 3. Migraciones
Las migraciones ya están creadas:
```
- 2025_10_22_202921_create_students_table.php
- 2025_10_26_000000_create_student_access_logs_table.php
- 2025_10_24_204433_create_access_records_table.php
```

Ejecutar:
```bash
php artisan migrate
```

### 4. Archivos de Sonido
Ubicación: `public/sounds/`
```
- beep.mp3       (éxito al escanear)
- error.mp3      (error)
- notification.mp3 (registro exitoso)
```

### 5. Permisos
Crear permiso en el seeder:
```php
Permission::create(['name' => 'access students', 'module' => 'Acceso']);
```

## 🔧 Uso del Sistema

### Acceso al Sistema
```
URL: http://127.0.0.1:8000/admin/access/students
Ruta: Route::get('/access/students', QrAccess::class)->name('access.students');
```

### Flujo de Trabajo

#### 1. **Escaneo con Cámara**
```
1. Hacer clic en "Iniciar Escáner"
2. Apuntar cámara al código QR
3. Sistema busca automáticamente al estudiante
4. Muestra información del estudiante
5. Determina si debe registrar entrada o salida
6. Usuario confirma el registro
```

#### 2. **Entrada Manual**
```
1. Cambiar a modo "Manual"
2. Ingresar código del estudiante
3. Presionar Enter o botón Buscar
4. Continúa igual que el escaneo
```

#### 3. **Registro de Acceso**
```
1. Verificar tipo de acceso (entrada/salida)
2. Agregar notas si es necesario
3. Clic en "Registrar Entrada/Salida"
4. Sistema valida y guarda
5. Actualiza estadísticas e historial
```

### Validaciones Automáticas

1. **Estudiante Activo**: Solo permite acceso a estudiantes con status = 1
2. **Sin Duplicados**: No permite 2 entradas o 2 salidas el mismo día
3. **Tipo Automático**: 
   - Si tiene entrada sin salida → sugiere salida
   - En cualquier otro caso → sugiere entrada

## 📊 Base de Datos

### Tabla: students
```sql
CREATE TABLE students (
    id BIGINT PRIMARY KEY,
    nombres VARCHAR(255),
    apellidos VARCHAR(255),
    fecha_nacimiento DATE,
    codigo VARCHAR(50) UNIQUE,
    documento_identidad VARCHAR(50),
    grado VARCHAR(50),
    seccion VARCHAR(50),
    nivel_educativo_id BIGINT,
    turno_id BIGINT,
    school_periods_id BIGINT,
    foto VARCHAR(255),
    correo_electronico VARCHAR(255),
    status BOOLEAN DEFAULT 1,
    representante_nombres VARCHAR(255),
    representante_apellidos VARCHAR(255),
    representante_documento_identidad VARCHAR(50),
    representante_telefonos JSON,
    representante_correo VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Tabla: student_access_logs
```sql
CREATE TABLE student_access_logs (
    id BIGINT PRIMARY KEY,
    student_id BIGINT,
    type ENUM('entrada', 'salida'),
    access_time DATETIME,
    registered_by BIGINT,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (registered_by) REFERENCES users(id)
);
```

## 🎨 Integración con Materialize

### Assets Utilizados
```
CSS:
- /materialize/assets/vendor/css/core.css
- /materialize/assets/css/demo.css
- /materialize/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css

JS:
- /materialize/assets/vendor/libs/jquery/jquery.js
- /materialize/assets/vendor/js/bootstrap.js
- /materialize/assets/vendor/libs/node-waves/node-waves.js
- /materialize/assets/js/main.js

Iconos:
- Remix Icons (ri-*)
```

### Componentes Materialize Usados
- Cards (tarjetas de estadísticas)
- Buttons (botones de acción)
- Forms (inputs, textareas)
- Tables (historial de accesos)
- Badges (etiquetas de estado)
- Avatars (fotos de estudiantes)
- Toasts (notificaciones)

## 🔐 Seguridad

### Permisos
```php
// Middleware en el componente
if (!Auth::user()->can('access students')) {
    abort(403);
}

// Eliminación de registros
if (!Auth::user()->hasRole('Admin')) {
    // No permitido
}
```

### Validaciones
- Solo estudiantes activos pueden registrar accesos
- Prevención de duplicados
- Auditoría completa (quién registró cada acceso)
- Validación de códigos QR

## 📱 Características Adicionales

### Responsive Design
- ✅ Optimizado para tablets
- ✅ Funciona en móviles
- ✅ Modo quiosco para tablets

### Accesibilidad
- ✅ Botones grandes para táctil
- ✅ Feedback visual y auditivo
- ✅ Mensajes claros de error/éxito

### Performance
- ✅ Carga de datos optimizada
- ✅ Actualización en tiempo real con Livewire
- ✅ Límite de 20 registros en historial

## 🐛 Solución de Problemas

### Cámara no funciona
```
1. Verificar permisos del navegador
2. Usar HTTPS (requerido para cámara)
3. Probar con otro navegador
4. Usar modo manual como alternativa
```

### Código QR no se lee
```
1. Verificar iluminación
2. Mantener código estable
3. Verificar formato del QR
4. Usar entrada manual
```

### Estudiante no encontrado
```
1. Verificar código correcto
2. Confirmar que esté registrado
3. Verificar que esté activo (status = 1)
```

## 📝 Notas Importantes

1. **Códigos QR**: Se generan automáticamente con la información del estudiante
2. **Formato QR**: Incluye código, nombre, documento y grado
3. **Sonidos**: Se pueden activar/desactivar según preferencia
4. **Historial**: Solo muestra registros del día actual
5. **Permisos**: Solo administradores pueden eliminar registros

## 🔄 Próximas Mejoras Sugeridas

1. **Reportes**
   - Exportar a Excel/PDF
   - Reportes por fecha
   - Reportes por estudiante

2. **Notificaciones**
   - Email a representantes
   - SMS de entrada/salida
   - Notificaciones push

3. **Dashboard**
   - Gráficos de asistencia
   - Estadísticas mensuales
   - Alertas de ausencias

4. **Funcionalidades**
   - Registro masivo
   - Importación de estudiantes
   - Generación masiva de QR

## 📞 Soporte

Para problemas técnicos:
1. Revisar logs: `storage/logs/laravel.log`
2. Verificar consola del navegador (F12)
3. Comprobar permisos de archivos
4. Verificar configuración de base de datos

## 📄 Licencia

Este sistema está desarrollado para uso interno educativo.

---

**Desarrollado con ❤️ usando Laravel 11 + Livewire 3 + Materialize**
