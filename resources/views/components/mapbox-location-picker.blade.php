@props(['latitude' => -12.0464, 'longitude' => -77.0428, 'zoom' => 13])

<div wire:ignore x-data="mapboxPicker(@js($latitude), @js($longitude), @js($zoom))" x-init="init()">
    <div class="mb-3">
        <div x-ref="geocoderContainer"></div>
    </div>

    <div class="position-relative">
        <div x-ref="mapContainer" 
             style="height: 400px; border-radius: 8px; border: 1px solid #ddd;">
        </div>
        <button type="button" 
                @click="getCurrentLocation()" 
                class="btn btn-primary btn-sm position-absolute" 
                style="top: 10px; right: 50px; z-index: 1000;"
                title="Obtener ubicación actual">
            <i class="mdi mdi-crosshairs-gps"></i>
        </button>
    </div>

    <div class="mt-3">
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label small text-muted">Latitud</label>
                <input type="text" 
                       x-model="latitude" 
                       class="form-control form-control-sm" 
                       readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted">Longitud</label>
                <input type="text" 
                       x-model="longitude" 
                       class="form-control form-control-sm" 
                       readonly>
            </div>
        </div>
        <div class="mt-2">
            <label class="form-label small text-muted">Dirección</label>
            <input type="text" 
                   x-model="address" 
                   class="form-control form-control-sm" 
                   readonly>
        </div>
    </div>
</div>

@push('styles')
<link href="https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.css" rel="stylesheet">
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.css">
@endpush

@push('scripts')
<script src="https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.js"></script>
<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.min.js"></script>

<script>
function mapboxPicker(initialLat, initialLng, initialZoom) {
    return {
        map: null,
        marker: null,
        latitude: initialLat,
        longitude: initialLng,
        address: '',
        
        init() {
            mapboxgl.accessToken = '{{ config("services.mapbox.token") }}';
            
            this.map = new mapboxgl.Map({
                container: this.$refs.mapContainer,
                style: 'mapbox://styles/mapbox/streets-v12',
                center: [initialLng, initialLat],
                zoom: initialZoom
            });

            // Agregar controles de navegación
            this.map.addControl(new mapboxgl.NavigationControl());

            // Crear marcador draggable
            this.marker = new mapboxgl.Marker({
                draggable: true,
                color: '#667eea'
            })
            .setLngLat([initialLng, initialLat])
            .addTo(this.map);

            // Geocoder (buscador)
            const geocoder = new MapboxGeocoder({
                accessToken: mapboxgl.accessToken,
                mapboxgl: mapboxgl,
                marker: false,
                placeholder: 'Buscar ubicación...'
            });

            this.$refs.geocoderContainer.appendChild(geocoder.onAdd(this.map));

            // Eventos
            geocoder.on('result', (e) => {
                const coords = e.result.geometry.coordinates;
                this.updateLocation(coords[1], coords[0], e.result.place_name);
                this.marker.setLngLat(coords);
            });

            this.marker.on('dragend', () => {
                const lngLat = this.marker.getLngLat();
                this.updateLocation(lngLat.lat, lngLat.lng);
                this.reverseGeocode(lngLat.lat, lngLat.lng);
            });

            this.map.on('click', (e) => {
                this.marker.setLngLat(e.lngLat);
                this.updateLocation(e.lngLat.lat, e.lngLat.lng);
                this.reverseGeocode(e.lngLat.lat, e.lngLat.lng);
            });

            // Obtener dirección inicial
            this.reverseGeocode(initialLat, initialLng);
        },

        getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        this.map.flyTo({ center: [lng, lat], zoom: 15 });
                        this.marker.setLngLat([lng, lat]);
                        this.updateLocation(lat, lng);
                        this.reverseGeocode(lat, lng);
                    },
                    (error) => {
                        alert('No se pudo obtener tu ubicación. Verifica los permisos del navegador.');
                    }
                );
            } else {
                alert('Tu navegador no soporta geolocalización.');
            }
        },

        updateLocation(lat, lng, addr = null) {
            this.latitude = lat.toFixed(6);
            this.longitude = lng.toFixed(6);
            if (addr) this.address = addr;
            
            // Actualizar Livewire directamente
            if (window.Livewire) {
                @this.set('latitud', this.latitude);
                @this.set('longitud', this.longitude);
                if (this.address) {
                    @this.set('direccion', this.address);
                    @this.set('address', this.address);
                }
            }
            
            // Emitir evento para Livewire
            this.$dispatch('location-updated', {
                latitude: this.latitude,
                longitude: this.longitude,
                address: this.address
            });
        },

        async reverseGeocode(lat, lng) {
            try {
                const response = await fetch(
                    `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=${mapboxgl.accessToken}`
                );
                const data = await response.json();
                if (data.features && data.features.length > 0) {
                    this.address = data.features[0].place_name;
                    
                    // Actualizar Livewire directamente
                    if (window.Livewire) {
                        @this.set('direccion', this.address);
                        @this.set('address', this.address);
                    }
                    
                    this.$dispatch('location-updated', {
                        latitude: this.latitude,
                        longitude: this.longitude,
                        address: this.address
                    });
                }
            } catch (error) {
                console.error('Error en geocodificación inversa:', error);
            }
        }
    }
}
</script>
@endpush
