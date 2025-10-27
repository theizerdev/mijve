# ✅ Mapbox Aplicado en Empresas y Sucursales

## Archivos Actualizados

### ✅ Empresas - COMPLETADO

#### 1. `app/Livewire/Admin/Empresas/Create.php`
- ✅ Agregado `use Livewire\Attributes\On;`
- ✅ Agregado propiedad `public $address = '';`
- ✅ Inicializado `latitud` y `longitud` con valores por defecto de Lima
- ✅ Agregado método `updateLocation()` con atributo `#[On('location-updated')]`
- ✅ Actualizado `save()` para usar `$this->address`

#### 2. `app/Livewire/Admin/Empresas/Edit.php`
- ✅ Agregado `use Livewire\Attributes\On;`
- ✅ Agregado propiedad `public $address = '';`
- ✅ Agregado método `updateLocation()`
- ✅ Actualizado `mount()` para cargar coordenadas existentes
- ✅ Actualizado `save()` para usar `$this->address`

#### 3. `resources/views/livewire/admin/empresas/create.blade.php`
- ✅ Reemplazado Leaflet por componente Mapbox
- ✅ Eliminado código de Leaflet.js
- ✅ Agregado `<x-mapbox-location-picker />`

### 📝 Pendiente: Empresas Edit View

Aplicar los mismos cambios en `resources/views/livewire/admin/empresas/edit.blade.php`:

```blade
<!-- Reemplazar el bloque de dirección y mapa por: -->
<div class="col-12 mb-3">
    <label class="form-label">Ubicación <span class="text-danger">*</span></label>
    <x-mapbox-location-picker 
        :latitude="$latitud" 
        :longitude="$longitud" 
        :zoom="15" 
    />
    @error('latitud') <div class="text-danger small">{{ $message }}</div> @enderror
    @error('longitud') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<!-- Eliminar: -->
- Campos de latitud/longitud manuales
- Todo el código de Leaflet (@push('styles') y @push('scripts'))
```

---

## 📋 TODO: Aplicar en Sucursales

### 1. `app/Livewire/Admin/Sucursales/Create.php`

```php
<?php

namespace App\Livewire\Admin\Sucursales;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Sucursal;
use App\Models\Empresa;

class Create extends Component
{
    // ... propiedades existentes ...
    public $latitud = -12.0464;
    public $longitud = -77.0428;
    public $address = '';
    
    protected $rules = [
        // ... reglas existentes ...
        'latitud' => 'required|numeric|between:-90,90',
        'longitud' => 'required|numeric|between:-180,180',
        'address' => 'nullable|string',
    ];
    
    #[On('location-updated')]
    public function updateLocation($latitude, $longitude, $address)
    {
        $this->latitud = $latitude;
        $this->longitud = $longitude;
        $this->address = $address;
        $this->direccion = $address;
    }
    
    public function save()
    {
        $this->validate();
        
        Sucursal::create([
            // ... campos existentes ...
            'direccion' => $this->address ?: $this->direccion,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
        ]);
        
        // ... resto del método ...
    }
}
```

### 2. `app/Livewire/Admin/Sucursales/Edit.php`

```php
<?php

namespace App\Livewire\Admin\Sucursales;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Sucursal;
use App\Models\Empresa;

class Edit extends Component
{
    public $sucursal;
    // ... propiedades existentes ...
    public $latitud;
    public $longitud;
    public $address = '';
    
    protected $rules = [
        // ... reglas existentes ...
        'latitud' => 'required|numeric|between:-90,90',
        'longitud' => 'required|numeric|between:-180,180',
        'address' => 'nullable|string',
    ];
    
    #[On('location-updated')]
    public function updateLocation($latitude, $longitude, $address)
    {
        $this->latitud = $latitude;
        $this->longitud = $longitude;
        $this->address = $address;
        $this->direccion = $address;
    }
    
    public function mount(Sucursal $sucursal)
    {
        $this->sucursal = $sucursal;
        // ... asignaciones existentes ...
        $this->latitud = $sucursal->latitud ?: -12.0464;
        $this->longitud = $sucursal->longitud ?: -77.0428;
        $this->address = $sucursal->direccion;
    }
    
    public function save()
    {
        $this->validate();
        
        $this->sucursal->update([
            // ... campos existentes ...
            'direccion' => $this->address ?: $this->direccion,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
        ]);
        
        // ... resto del método ...
    }
}
```

### 3. Vistas de Sucursales

En `resources/views/livewire/admin/sucursales/create.blade.php` y `edit.blade.php`:

```blade
<!-- Reemplazar el bloque de dirección y mapa por: -->
<div class="col-12 mb-3">
    <label class="form-label">Ubicación <span class="text-danger">*</span></label>
    <x-mapbox-location-picker 
        :latitude="$latitud" 
        :longitude="$longitud" 
        :zoom="15" 
    />
    @error('latitud') <div class="text-danger small">{{ $message }}</div> @enderror
    @error('longitud') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<!-- Eliminar todo el código de Leaflet -->
```

---

## 🗄️ Migración de Base de Datos

Si las tablas no tienen las columnas necesarias, crear migración:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Para empresas
        if (!Schema::hasColumn('empresas', 'latitud')) {
            Schema::table('empresas', function (Blueprint $table) {
                $table->decimal('latitud', 10, 7)->nullable()->after('direccion');
                $table->decimal('longitud', 10, 7)->nullable()->after('latitud');
            });
        }
        
        // Para sucursales
        if (!Schema::hasColumn('sucursales', 'latitud')) {
            Schema::table('sucursales', function (Blueprint $table) {
                $table->decimal('latitud', 10, 7)->nullable()->after('direccion');
                $table->decimal('longitud', 10, 7)->nullable()->after('latitud');
            });
        }
    }
    
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['latitud', 'longitud']);
        });
        
        Schema::table('sucursales', function (Blueprint $table) {
            $table->dropColumn(['latitud', 'longitud']);
        });
    }
};
```

Ejecutar: `php artisan migrate`

---

## ✅ Checklist de Implementación

### Empresas
- [x] Create.php - Componente Livewire
- [x] Edit.php - Componente Livewire  
- [x] create.blade.php - Vista
- [ ] edit.blade.php - Vista (aplicar mismo patrón)

### Sucursales
- [ ] Create.php - Componente Livewire
- [ ] Edit.php - Componente Livewire
- [ ] create.blade.php - Vista
- [ ] edit.blade.php - Vista

### Configuración
- [x] .env.example - Variable MAPBOX_ACCESS_TOKEN
- [x] config/services.php - Configuración Mapbox
- [x] Componente Mapbox creado

---

## 🎯 Resultado Final

Después de aplicar todos los cambios:

1. ✅ Selector de ubicación moderno con Mapbox
2. ✅ Búsqueda de direcciones con autocompletado
3. ✅ Marcador arrastrable
4. ✅ Geocodificación inversa automática
5. ✅ Coordenadas actualizadas en tiempo real
6. ✅ Integración perfecta con Livewire
7. ✅ Sin dependencia de Leaflet

**Nota**: Solo falta aplicar el mismo patrón en los archivos pendientes siguiendo los ejemplos proporcionados.
