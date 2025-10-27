# Sistema de Internacionalización (i18n)

## ✅ Implementación Completa

### Idiomas Disponibles
- 🇪🇸 **Español** (por defecto)
- 🇺🇸 **English**

### Archivos de Traducción

#### Español (`lang/es/`)
- `auth.php` - Mensajes de autenticación
- `validation.php` - Mensajes de validación
- `messages.php` - Mensajes generales del sistema

#### Inglés (`lang/en/`)
- `auth.php` - Authentication messages
- `validation.php` - Validation messages
- `messages.php` - General system messages

### Uso en Vistas

```blade
<!-- Traducción simple -->
{{ __('messages.dashboard') }}

<!-- Traducción con parámetros -->
{{ __('messages.showing', ['from' => 1, 'to' => 10]) }}

<!-- En atributos -->
<button title="{{ __('messages.save') }}">
```

### Uso en Controladores/Livewire

```php
// Mensaje flash
session()->flash('message', __('messages.student_registered'));

// Validación
$this->validate([
    'email' => 'required|email'
], [
    'email.required' => __('validation.required', ['attribute' => 'email'])
]);
```

### Cambio de Idioma

**Ruta:** `/lang/{locale}`

**Ejemplo:**
```blade
<a href="{{ route('lang.switch', 'es') }}">Español</a>
<a href="{{ route('lang.switch', 'en') }}">English</a>
```

### Middleware SetLocale

Detecta automáticamente el idioma guardado en sesión y lo aplica a toda la aplicación.

### Componentes Internacionalizados

✅ Navbar
✅ NotificationBell
✅ Shortcuts
✅ Sistema de autenticación

### Agregar Nuevas Traducciones

1. **Agregar en español** (`lang/es/messages.php`):
```php
'new_key' => 'Nuevo texto en español',
```

2. **Agregar en inglés** (`lang/en/messages.php`):
```php
'new_key' => 'New text in English',
```

3. **Usar en vista**:
```blade
{{ __('messages.new_key') }}
```

### Traducciones Disponibles

#### Navbar
- shortcuts, notifications, mark_all_read, view_all_notifications
- no_notifications, my_profile, settings, logout

#### Dashboard
- dashboard, total_students, active_students
- today_entries, today_exits, students_inside

#### Módulos
- students, users, companies, branches
- new_*, edit_*, *_list

#### Acciones
- actions, view, edit, delete, save, cancel
- search, filter, export, clear, refresh, create

#### Estado
- status, active, inactive, all

#### Control de Acceso
- access_control, entry, exit
- entry_registered, exit_registered

#### Comunes
- name, email, phone, address
- created_at, updated_at
- showing, to, of, results, per_page

### Próximos Pasos

Para internacionalizar completamente el sistema:

1. Reemplazar textos hardcodeados en vistas con `__('messages.key')`
2. Agregar traducciones para formularios
3. Agregar traducciones para mensajes de error
4. Agregar traducciones para emails
5. Agregar más idiomas si es necesario

### Ejemplo Completo

**Vista:**
```blade
<h1>{{ __('messages.student_list') }}</h1>
<button>{{ __('messages.new_student') }}</button>
<input placeholder="{{ __('messages.search') }}">
```

**Resultado en Español:**
```
Lista de Estudiantes
[Nuevo Estudiante]
[Buscar...]
```

**Resultado en English:**
```
Student List
[New Student]
[Search...]
```
