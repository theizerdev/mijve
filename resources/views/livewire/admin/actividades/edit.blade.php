<div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Editar Actividad</h5>
                    <a href="{{ route('admin.actividades.index') }}" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Regresar
                    </a>
                </div>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="row g-3">
                            <!-- Empresa y Sucursal -->
                            @if(auth()->user()->hasRole('Super Administrador') || !auth()->user()->empresa_id)
                            <div class="col-md-6">
                                <label class="form-label">Empresa</label>
                                <select class="form-select @error('empresa_id') is-invalid @enderror" wire:model.live="empresa_id">
                                    <option value="">Seleccione una empresa</option>
                                    @foreach($empresas as $empresa)
                                        <option value="{{ $empresa->id }}">{{ $empresa->razon_social }}</option>
                                    @endforeach
                                </select>
                                @error('empresa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            @endif

                            <div class="col-md-6">
                                <label class="form-label">Sucursal</label>
                                <select class="form-select @error('sucursal_id') is-invalid @enderror" wire:model.live="sucursal_id" {{ empty($sucursales) ? 'disabled' : '' }}>
                                    <option value="">Seleccione una sucursal</option>
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('sucursal_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Información General -->
                            <div class="col-md-12">
                                <label class="form-label">Nombre de la Actividad</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                       wire:model="nombre" placeholder="Nombre de la actividad">
                                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                       wire:model="fecha_inicio">
                                @error('fecha_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" 
                                       wire:model="fecha_fin" min="{{ $fecha_inicio }}">
                                @error('fecha_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                          wire:model="descripcion" rows="3" maxlength="500"></textarea>
                                <div class="form-text">{{ strlen($descripcion) }}/500 caracteres</div>
                                @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Ubicación -->
                            <div class="col-md-12">
                                <label class="form-label">Dirección</label>
                                <input type="text" class="form-control @error('direccion') is-invalid @enderror" 
                                       wire:model.defer="direccion" placeholder="Dirección de la actividad" id="direccion-input">
                                @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Latitud</label>
                                <input type="text" class="form-control @error('latitud') is-invalid @enderror" 
                                       wire:model.defer="latitud" placeholder="Ej: 10.4806" id="latitud-input" readonly>
                                @error('latitud') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Longitud</label>
                                <input type="text" class="form-control @error('longitud') is-invalid @enderror" 
                                       wire:model.defer="longitud" placeholder="Ej: -66.9036" id="longitud-input" readonly>
                                @error('longitud') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Seleccionar ubicación en el mapa</label>
                                <div class="mb-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="search-input" placeholder="Buscar ubicación (ej: Caracas, Venezuela)">
                                        <button type="button" class="btn btn-primary" id="search-btn" onclick="searchLocation()">
                                            <i class="ri-search-line"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                                <div id="map" style="height: 400px; border-radius: 8px;" wire:ignore></div>
                                <div class="form-text">Haz clic en el mapa para seleccionar la ubicación de la actividad o busca por nombre</div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Costo</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('costo') is-invalid @enderror" 
                                           wire:model="costo" min="0" step="0.01" placeholder="0.00">
                                </div>
                                @error('costo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Capacidad</label>
                                <input type="number" class="form-control @error('capacidad') is-invalid @enderror" 
                                       wire:model="capacidad" min="1" step="1">
                                <div class="form-text">Cupos máximos</div>
                                @error('capacidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Edad Desde (años)</label>
                                <input type="number" class="form-control @error('edad_desde') is-invalid @enderror" 
                                       wire:model="edad_desde" min="0" max="100">
                                @error('edad_desde') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Edad Hasta (años)</label>
                                <input type="number" class="form-control @error('edad_hasta') is-invalid @enderror" 
                                       wire:model="edad_hasta" min="0" max="100">
                                @error('edad_hasta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary me-2">Actualizar</button>
                                <a href="{{ route('admin.actividades.index') }}" class="btn btn-label-secondary">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let map, marker;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener coordenadas existentes
        const existingLat = @json($latitud);
        const existingLng = @json($longitud);
        
        // Inicializar mapa
        const initialLat = existingLat || 10.4806;
        const initialLng = existingLng || -66.9036;
        const initialZoom = existingLat ? 13 : 6;
        
        map = L.map('map').setView([initialLat, initialLng], initialZoom);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        
        marker = null;
        
        // Si hay coordenadas existentes, mostrar marcador
        if (existingLat && existingLng) {
            marker = L.marker([existingLat, existingLng]).addTo(map);
        }
        
        // Evento de clic en el mapa
        map.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(8);
            const lng = e.latlng.lng.toFixed(8);
            updateLocation(lat, lng);
        });
        
        // Buscar al presionar Enter
        document.getElementById('search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchLocation();
            }
        });
    });
    
    // Función para actualizar ubicación
    async function updateLocation(lat, lng) {
        document.getElementById('latitud-input').value = lat;
        document.getElementById('longitud-input').value = lng;
        @this.set('latitud', lat);
        @this.set('longitud', lng);
        
        if (marker) {
            map.removeLayer(marker);
        }
        
        marker = L.marker([lat, lng]).addTo(map);
        
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
            const data = await response.json();
            if (data.display_name) {
                document.getElementById('direccion-input').value = data.display_name;
                @this.set('direccion', data.display_name);
            }
        } catch (error) {
            console.error('Error al obtener dirección:', error);
        }
    }
    
    // Función de búsqueda
    async function searchLocation() {
        const query = document.getElementById('search-input').value;
        if (!query) {
            alert('Por favor ingresa una ubicación');
            return;
        }
        
        console.log('Buscando:', query);
        
        try {
            // Intentar búsqueda con Nominatim
            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&addressdetails=1&countrycodes=ve`;
            console.log('URL:', url);
            
            const response = await fetch(url, {
                headers: {
                    'User-Agent': 'MijveApp/1.0'
                }
            });
            
            const data = await response.json();
            console.log('Respuesta:', data);
            
            if (data && data.length > 0) {
                // Si hay múltiples resultados, mostrar el primero
                const result = data[0];
                const lat = parseFloat(result.lat).toFixed(8);
                const lng = parseFloat(result.lon).toFixed(8);
                
                console.log('Coordenadas encontradas:', lat, lng);
                console.log('Dirección:', result.display_name);
                
                map.setView([lat, lng], 16);
                updateLocation(lat, lng);
            } else {
                // Si no encuentra, sugerir búsqueda más simple
                alert('No se encontró la ubicación. Intenta buscar solo:\n\n- "Coche Caracas"\n- "Caracas Venezuela"\n- O haz clic directamente en el mapa');
            }
        } catch (error) {
            console.error('Error en la búsqueda:', error);
            alert('Error al buscar. Intenta con una búsqueda más simple como "Coche Caracas" o haz clic en el mapa');
        }
    }
</script>
@endpush
