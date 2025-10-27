# 🗺️ Implementación de Mapbox GL JS

## Configuración Inicial

### 1. Agregar API Key al .env
```env
MAPBOX_ACCESS_TOKEN=tu_token_aqui
```

### 2. Uso en Formularios de Empresas/Sucursales

#### En el Componente Livewire (Create.php o Edit.php):

```php
<?php

namespace App\Livewire\Admin\Empresas;

use Livewire\Component;
use Livewire\Attributes\On;

class Create extends Component
{
    public $latitude = -12.0464;  // Lima, Perú por defecto
    public $longitude = -77.0428;
    public $address = '';
    
    #[On('location-updated')]
    public function updateLocation($latitude, $longitude, $address)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->address = $address;
    }
    
    public function save()
    {
        $validated = $this->validate([
            'razon_social' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'nullable|string',
            // ... otros campos
        ]);
        
        Empresa::create($validated);
        
        return redirect()->route('admin.empresas.index');
    }
}
```

#### En la Vista Blade (create.blade.php):

```blade
<div>
    <form wire:submit="save">
        <!-- Otros campos del formulario -->
        
        <div class="mb-3">
            <label class="form-label">Razón Social</label>
            <input type="text" wire:model="razon_social" class="form-control">
            @error('razon_social') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <!-- Selector de Ubicación -->
        <div class="mb-3">
            <label class="form-label">Ubicación</label>
            <x-mapbox-location-picker 
                :latitude="$latitude" 
                :longitude="$longitude" 
                :zoom="13" 
            />
            @error('latitude') <span class="text-danger">{{ $message }}</span> @enderror
            @error('longitude') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
```

## Características del Componente

### ✅ Funcionalidades Incluidas:

1. **Búsqueda de Direcciones**: Autocompletado con sugerencias
2. **Marcador Arrastrable**: Mueve el pin para ajustar ubicación
3. **Click en Mapa**: Haz click para colocar el marcador
4. **Geocodificación Inversa**: Obtiene dirección automáticamente
5. **Controles de Navegación**: Zoom y rotación
6. **Coordenadas en Tiempo Real**: Muestra lat/lng actualizadas
7. **Integración con Livewire**: Actualiza propiedades automáticamente

### 🎨 Personalización:

```blade
<!-- Cambiar ubicación inicial -->
<x-mapbox-location-picker 
    :latitude="-12.0464" 
    :longitude="-77.0428" 
    :zoom="15" 
/>

<!-- Para editar (con datos existentes) -->
<x-mapbox-location-picker 
    :latitude="$empresa->latitude ?? -12.0464" 
    :longitude="$empresa->longitude ?? -77.0428" 
    :zoom="15" 
/>
```

## Migración de Base de Datos

Agregar columnas a la tabla empresas:

```php
Schema::table('empresas', function (Blueprint $table) {
    $table->decimal('latitude', 10, 7)->nullable();
    $table->decimal('longitude', 10, 7)->nullable();
    $table->text('address')->nullable();
});
```

## Mostrar Mapa en Vista Show

```blade
<div class="card">
    <div class="card-header">
        <h5>Ubicación</h5>
    </div>
    <div class="card-body">
        <div id="map" style="height: 300px; border-radius: 8px;"></div>
        <p class="mt-2 mb-0"><strong>Dirección:</strong> {{ $empresa->address }}</p>
    </div>
</div>

@push('scripts')
<script src="https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.js"></script>
<link href="https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.css" rel="stylesheet">
<script>
    mapboxgl.accessToken = '{{ config("services.mapbox.token") }}';
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v12',
        center: [{{ $empresa->longitude }}, {{ $empresa->latitude }}],
        zoom: 15
    });
    
    new mapboxgl.Marker({ color: '#667eea' })
        .setLngLat([{{ $empresa->longitude }}, {{ $empresa->latitude }}])
        .addTo(map);
</script>
@endpush
```

## Ventajas sobre Leaflet

✅ Mapas vectoriales (más rápidos y nítidos)
✅ Mejor rendimiento en móviles
✅ Estilos modernos y personalizables
✅ Geocodificación integrada
✅ Búsqueda de lugares potente
✅ Actualizaciones constantes
✅ Mejor documentación

## Límites Gratuitos

- **50,000 cargas de mapa/mes** - Gratis
- **100,000 geocodificaciones/mes** - Gratis
- Suficiente para aplicaciones pequeñas/medianas
