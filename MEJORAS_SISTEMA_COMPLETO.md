# Plan de Mejoras Completo del Sistema

## ✅ FASE 1 COMPLETADA

### 1. Seeders de Usuarios
- ✅ EmpresaSeeder: Crea 3 usuarios por empresa
- ✅ SucursalSeeder: Crea 3 usuarios por sucursal
- ✅ Todos con password: `password`

### 2. Multitenancy Aplicado
- ✅ Trait Multitenantable en todos los modelos
- ✅ Scopes forUser() en Empresa, Sucursal, User
- ✅ Filtros aplicados en Index de Empresas, Sucursales, Users

### 3. Validaciones Corregidas
- ✅ Status como boolean (1/0) en Users
- ✅ Tabla sucursales (no sucursals) en validaciones

## 🔄 FASE 2: PENDIENTE DE APLICAR

### Filtros Avanzados en Listados

**Students Index:**
```php
// Agregar propiedades
public $empresa_id = '';
public $sucursal_id = '';
public $nivel_educativo_id = '';
public $turno_id = '';
public $grado = '';

// En render()
$students = Student::query()
    ->when($this->empresa_id, fn($q) => $q->where('empresa_id', $this->empresa_id))
    ->when($this->sucursal_id, fn($q) => $q->where('sucursal_id', $this->sucursal_id))
    ->when($this->nivel_educativo_id, fn($q) => $q->where('nivel_educativo_id', $this->nivel_educativo_id))
    ->when($this->turno_id, fn($q) => $q->where('turno_id', $this->turno_id))
    ->when($this->grado, fn($q) => $q->where('grado', $this->grado))
    ->paginate($this->perPage);
```

**SchoolPeriods Index:**
```php
public $empresa_id = '';
public $sucursal_id = '';
public $is_active = '';
public $is_current = '';

// Aplicar filtros en render()
```

**Turnos Index:**
```php
public $empresa_id = '';
public $sucursal_id = '';
public $status = '';

// Aplicar filtros en render()
```

**NivelesEducativos Index:**
```php
public $empresa_id = '';
public $sucursal_id = '';
public $status = '';

// Aplicar filtros en render()
```

### Exportación de Datos

**Crear Trait Exportable:**
```php
// app/Traits/Exportable.php
trait Exportable {
    public function exportExcel($query, $filename, $headers)
    {
        $data = $query->get();
        
        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, $headers);
        
        foreach ($data as $row) {
            fputcsv($csv, $row->toArray());
        }
        
        rewind($csv);
        $output = stream_get_contents($csv);
        fclose($csv);
        
        return response($output)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }
}
```

**Aplicar en:**
- Students Index
- Users Index
- Empresas Index
- Sucursales Index

### Breadcrumbs

**Crear Componente:**
```php
// app/View/Components/Breadcrumb.php
class Breadcrumb extends Component
{
    public $items;
    
    public function __construct($items = [])
    {
        $this->items = $items;
    }
    
    public function render()
    {
        return view('components.breadcrumb');
    }
}
```

**Vista:**
```blade
<!-- resources/views/components/breadcrumb.blade.php -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
        @foreach($items as $item)
            @if($loop->last)
                <li class="breadcrumb-item active">{{ $item['label'] }}</li>
            @else
                <li class="breadcrumb-item"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
            @endif
        @endforeach
    </ol>
</nav>
```

**Uso:**
```blade
<x-breadcrumb :items="[
    ['label' => 'Estudiantes', 'url' => route('admin.students.index')],
    ['label' => 'Crear']
]" />
```

## 🔄 FASE 3: PENDIENTE DE APLICAR

### Sistema de Notificaciones

**Migración:**
```php
// database/migrations/xxxx_create_notifications_table.php
Schema::create('notifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('type');
    $table->string('title');
    $table->text('message');
    $table->json('data')->nullable();
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
});
```

**Modelo:**
```php
// app/Models/Notification.php
class Notification extends Model
{
    protected $fillable = ['user_id', 'type', 'title', 'message', 'data', 'read_at'];
    protected $casts = ['data' => 'array', 'read_at' => 'datetime'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }
}
```

**Eventos a Notificar:**
- Usuario creado
- Estudiante creado
- Acceso de estudiante (entrada/salida)
- Cambios importantes en configuración

### Logs de Auditoría

**Instalar Paquete:**
```bash
composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider"
php artisan migrate
```

**Aplicar en Modelos:**
```php
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Student extends Model
{
    use LogsActivity;
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombres', 'apellidos', 'status'])
            ->logOnlyDirty();
    }
}
```

**Aplicar en:**
- Student
- User
- Empresa
- Sucursal
- EducationalLevel
- SchoolPeriod
- Turno

### Búsqueda Global

**Componente Livewire:**
```php
// app/Livewire/GlobalSearch.php
class GlobalSearch extends Component
{
    public $search = '';
    public $results = [];
    
    public function updatedSearch()
    {
        if (strlen($this->search) < 3) {
            $this->results = [];
            return;
        }
        
        $this->results = [
            'students' => Student::query()
                ->where('nombres', 'like', "%{$this->search}%")
                ->orWhere('apellidos', 'like', "%{$this->search}%")
                ->limit(5)->get(),
            'users' => User::forUser()
                ->where('name', 'like', "%{$this->search}%")
                ->limit(5)->get(),
            'empresas' => Empresa::forUser()
                ->where('razon_social', 'like', "%{$this->search}%")
                ->limit(5)->get(),
        ];
    }
    
    public function render()
    {
        return view('livewire.global-search');
    }
}
```

**Agregar en Navbar:**
```blade
<livewire:global-search />
```

### Optimización con Caché

**Dashboard:**
```php
public function render()
{
    $cacheKey = 'dashboard_stats_' . auth()->id();
    
    $stats = Cache::remember($cacheKey, 300, function() {
        return [
            'totalStudents' => Student::query()->count(),
            'activeStudents' => Student::query()->where('status', 1)->count(),
            // ... otras estadísticas
        ];
    });
    
    return view('livewire.admin.dashboard', $stats);
}
```

**Invalidar Caché:**
```php
// En Student Create/Edit/Delete
Cache::forget('dashboard_stats_' . auth()->id());
```

## 📋 CHECKLIST DE IMPLEMENTACIÓN

### Fase 2
- [ ] Filtros avanzados en Students
- [ ] Filtros avanzados en SchoolPeriods
- [ ] Filtros avanzados en Turnos
- [ ] Filtros avanzados en NivelesEducativos
- [ ] Trait Exportable
- [ ] Exportación en Students
- [ ] Exportación en Users
- [ ] Exportación en Empresas
- [ ] Exportación en Sucursales
- [ ] Componente Breadcrumb
- [ ] Breadcrumbs en todas las vistas

### Fase 3
- [ ] Migración de notificaciones
- [ ] Modelo Notification
- [ ] Componente de notificaciones en navbar
- [ ] Notificaciones para usuarios creados
- [ ] Notificaciones para estudiantes creados
- [ ] Instalar spatie/laravel-activitylog
- [ ] Logs en Student
- [ ] Logs en User
- [ ] Logs en Empresa
- [ ] Logs en Sucursal
- [ ] Logs en otros modelos
- [ ] Componente GlobalSearch
- [ ] Integrar búsqueda en navbar
- [ ] Caché en Dashboard
- [ ] Caché en estadísticas
- [ ] Invalidación de caché

## 🚀 COMANDOS PARA EJECUTAR

```bash
# Fase 2
php artisan make:livewire GlobalSearch
php artisan make:component Breadcrumb

# Fase 3
composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider"
php artisan migrate
php artisan make:migration create_notifications_table
php artisan migrate
```

## 📝 NOTAS IMPORTANTES

1. **Multitenancy**: Ya aplicado en modelos principales
2. **Permisos**: Verificar que todos los componentes validen permisos
3. **Testing**: Probar cada funcionalidad después de implementar
4. **Performance**: Monitorear queries con Laravel Debugbar
5. **Caché**: Usar Redis en producción para mejor performance
