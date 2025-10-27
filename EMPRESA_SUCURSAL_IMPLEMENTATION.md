# Implementación de Empresa y Sucursal en el Sistema

## Cambios Realizados

### 1. Trait FiltersByEmpresaSucursal
**Ubicación:** `app/Traits/FiltersByEmpresaSucursal.php`

```php
public function scopeFilterByEmpresaSucursal(Builder $query)
{
    $user = auth()->user();

    if ($user->hasRole('Super Administrador')) {
        return $query; // Sin restricciones
    }

    if ($user->empresa_id) {
        $query->where('empresa_id', $user->empresa_id);
    }

    if ($user->sucursal_id) {
        $query->where('sucursal_id', $user->sucursal_id);
    }

    return $query;
}
```

### 2. Migraciones Creadas
- `2024_01_15_000001_add_empresa_sucursal_to_users.php`
- `2024_01_15_000002_add_empresa_sucursal_to_students.php`

### 3. Modelos Actualizados
- ✅ User: Ya tiene empresa_id y sucursal_id en fillable y relaciones
- ✅ Student: Agregado trait, campos en fillable y relaciones

### 4. Uso del Trait en Componentes Livewire

**Ejemplo en Index:**
```php
public function render()
{
    $students = Student::query()
        ->filterByEmpresaSucursal() // Aplicar filtro
        ->with(['nivelEducativo', 'turno', 'schoolPeriod'])
        ->when($this->search, function($query) {
            $query->where('nombres', 'like', '%' . $this->search . '%')
                  ->orWhere('apellidos', 'like', '%' . $this->search . '%');
        })
        ->paginate(10);

    return view('livewire.admin.students.index', compact('students'));
}
```

**Ejemplo en Create:**
```php
public function save()
{
    $this->validate();

    Student::create([
        'empresa_id' => auth()->user()->empresa_id,
        'sucursal_id' => auth()->user()->sucursal_id,
        'nombres' => $this->nombres,
        // ... otros campos
    ]);
}
```

## Modelos Actualizados

### Modelos con empresa_id y sucursal_id:
1. ✅ User
2. ✅ Student
3. ✅ StudentAccessLog
4. ✅ EducationalLevel (niveles_educativos)
5. ✅ Turno (turnos)
6. ✅ SchoolPeriod (school_periods)

### Modelos que NO necesitan actualización:
- Role
- Permission
- Empresa
- Sucursal

## Pasos para Aplicar en Otros Modelos

1. **Crear migración:**
```php
Schema::table('nombre_tabla', function (Blueprint $table) {
    $table->foreignId('empresa_id')->nullable()->after('id')->constrained('empresas')->nullOnDelete();
    $table->foreignId('sucursal_id')->nullable()->after('empresa_id')->constrained('sucursales')->nullOnDelete();
});
```

2. **Actualizar Modelo:**
```php
use App\Traits\FiltersByEmpresaSucursal;

class NombreModelo extends Model
{
    use FiltersByEmpresaSucursal;

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        // ... otros campos
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
```

3. **Actualizar Componentes Livewire:**
- En listados: Agregar `->filterByEmpresaSucursal()`
- En create/edit: Asignar `empresa_id` y `sucursal_id` del usuario autenticado

## Migraciones Creadas

1. `2025_01_09_000001_add_empresa_sucursal_to_users.php`
2. `2025_01_09_000002_add_empresa_sucursal_to_students.php`
3. `2025_01_09_000003_add_empresa_sucursal_to_student_access_logs.php`
4. `2025_01_09_000004_add_empresa_sucursal_to_school_periods.php`
5. `2025_01_09_000005_add_empresa_sucursal_to_niveles_educativos.php`
6. `2025_01_09_000006_add_empresa_sucursal_to_turnos.php`

## Ejecutar Migraciones

```bash
php artisan migrate
```

## Mapa Actualizado

✅ Botón de ubicación actual movido fuera del mapa en card-header
✅ Implementado en empresas create/edit
✅ Implementado en sucursales create/edit
