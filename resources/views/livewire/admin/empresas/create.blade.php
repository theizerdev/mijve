<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-1">Crear Empresa</h5>
                    <p class="mb-0">Completa la información para registrar una nueva empresa</p>
                </div>
                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form wire:submit.prevent="save">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Razón Social <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('razon_social') is-invalid @enderror"
                                       wire:model="razon_social" placeholder="Ingrese la razón social">
                                @error('razon_social')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Documento <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('documento') is-invalid @enderror"
                                       wire:model="documento" placeholder="Ingrese el documento">
                                @error('documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Representante Legal</label>
                                <input type="text" class="form-control @error('representante_legal') is-invalid @enderror"
                                       wire:model="representante_legal" placeholder="Ingrese el representante legal">
                                @error('representante_legal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                    <option value="1">Activa</option>
                                    <option value="0">Inactiva</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Dirección</label>
                                <textarea class="form-control @error('direccion') is-invalid @enderror"
                                          wire:model="direccion" rows="2" placeholder="La dirección se completará automáticamente al seleccionar en el mapa"></textarea>
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Latitud <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('latitud') is-invalid @enderror"
                                       wire:model="latitud" placeholder="Seleccione en el mapa" readonly>
                                @error('latitud')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Longitud <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('longitud') is-invalid @enderror"
                                       wire:model="longitud" placeholder="Seleccione en el mapa" readonly>
                                @error('longitud')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Ubicación en el Mapa <span class="text-danger">*</span></h6>
                                        <button type="button" onclick="getCurrentLocation()" class="btn btn-primary btn-sm">
                                            <i class="mdi mdi-crosshairs-gps"></i> Ubicación Actual
                                        </button>
                                    </div>
                                    <div class="card-body p-0">
                                        <div wire:ignore id="map" style="height: 400px;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                       wire:model="telefono" placeholder="Ingrese el teléfono">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       wire:model="email" placeholder="Ingrese el correo electrónico">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>




                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.empresas.index') }}" class="btn btn-label-secondary">
                                <i class="ri ri-arrow-left-line"></i> Volver
                            </a>
                            @can('create empresas')
                            <button type="submit" class="btn btn-primary">
                                <i class="ri ri-save-line"></i> Guardar Empresa
                            </button>
                            @endcan
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
    document.addEventListener('livewire:init', function () {
        var initialLat = {{ $latitud ?: -12.0464 }};
        var initialLng = {{ $longitud ?: -77.0428 }};

        var map = L.map('map').setView([initialLat, initialLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

        function updateLocation(lat, lng) {
            @this.set('latitud', lat);
            @this.set('longitud', lng);

            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    if (data.display_name) {
                        @this.set('direccion', data.display_name);
                    }
                });
        }

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateLocation(e.latlng.lat, e.latlng.lng);
        });

        marker.on('dragend', function(e) {
            var latlng = marker.getLatLng();
            updateLocation(latlng.lat, latlng.lng);
        });

        window.getCurrentLocation = function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 15);
                    updateLocation(lat, lng);
                });
            } else {
                alert('Tu navegador no soporta geolocalización.');
            }
        };
    });
</script>
@endpush
