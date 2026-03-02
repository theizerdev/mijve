<div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Registrar Nuevo Pago</h5>
                    <a href="{{ route('admin.pagos.index') }}" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Regresar
                    </a>
                </div>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="row g-4">
                            <!-- Sección 1: Participante -->
                            <div class="col-12">
                                <h6 class="fw-bold border-bottom pb-2 mb-3">1. Datos del Participante</h6>
                                
                                @if(!$participante_selected)
                                    <div class="position-relative">
                                        <label class="form-label">Buscar Participante</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ri ri-search-line"></i></span>
                                            <input type="text" class="form-control" placeholder="Nombre, apellido o cédula..."
                                                   wire:model.live.debounce.300ms="search_participante">
                                        </div>
                                        @if(!empty($participantes))
                                            <div class="list-group position-absolute w-100 mt-1 shadow" style="z-index: 1000;">
                                                @foreach($participantes as $participante)
                                                    <button type="button" class="list-group-item list-group-item-action"
                                                            wire:click="selectParticipante({{ $participante->id }})">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <strong>{{ $participante->nombres }} {{ $participante->apellidos }}</strong>
                                                                <br>
                                                                <small class="text-muted">CI: {{ $participante->cedula }}</small>
                                                            </div>
                                                            <i class="ri ri-user-add-line"></i>
                                                        </div>
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif
                                        @error('participante_id') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                @else
                                    <div class="alert alert-primary d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="ri ri-user-follow-line me-2"></i>
                                            <strong>{{ $participante_selected->nombres }} {{ $participante_selected->apellidos }}</strong>
                                            <span class="ms-2 badge bg-white text-primary">{{ $participante_selected->cedula }}</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="$set('participante_selected', null)">
                                            Cambiar
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- Información de la Actividad -->
                            @if($actividad_selected)
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading mb-2"><i class="ri ri-information-line me-1"></i> Información de la Actividad</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Fechas</small>
                                            <strong>{{ $actividad_selected->fecha_inicio->format('d/m/Y') }} - {{ $actividad_selected->fecha_fin->format('d/m/Y') }}</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Cupos Disponibles</small>
                                            <strong>{{ $actividad_selected->capacidad - $actividad_selected->cupos_ocupados }} de {{ $actividad_selected->capacidad }}</strong>
                                        </div>
                                        @if($actividad_selected->direccion)
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Ubicación</small>
                                            <strong>{{ Str::limit($actividad_selected->direccion, 40) }}</strong>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Sección 2: Detalles del Pago -->
                            <div class="col-12">
                                <h6 class="fw-bold border-bottom pb-2 mb-3">2. Detalles del Pago (Solo Divisas)</h6>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Actividad</label>
                                        <select class="form-select @error('actividad_id') is-invalid @enderror" wire:model.live="actividad_id">
                                            <option value="">Seleccione Actividad</option>
                                            @foreach($actividades as $actividad)
                                                <option value="{{ $actividad->id }}" {{ !$actividad->tieneCuposDisponibles() ? 'disabled' : '' }}>
                                                    {{ $actividad->nombre }} (Costo: ${{ number_format($actividad->costo, 2) }})
                                                    @if(!$actividad->tieneCuposDisponibles()) - AGOTADO @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('actividad_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Método de Pago</label>
                                        <select class="form-select @error('metodo_pago_id') is-invalid @enderror" wire:model.live="metodo_pago_id">
                                            <option value="">Seleccione Método</option>
                                            @foreach($metodos_pago as $metodo)
                                                <option value="{{ $metodo->id }}">
                                                    {{ $metodo->tipo_pago }} - {{ $metodo->banco }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('metodo_pago_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Monto (Divisa)</label>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text">€</span>
                                            <input type="number" class="form-control @error('monto_euro') is-invalid @enderror" 
                                                   wire:model="monto_euro" step="0.01" min="0">
                                        </div>
                                        @error('monto_euro') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Fecha de Pago</label>
                                        <input type="date" class="form-control @error('fecha_pago') is-invalid @enderror" 
                                               wire:model="fecha_pago" max="{{ date('Y-m-d') }}">
                                        @error('fecha_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Observaciones</label>
                                        <textarea class="form-control" wire:model="observaciones" rows="2" placeholder="Notas adicionales sobre el pago..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección 3: Observaciones -->
                            <div class="col-12">
                                <h6 class="fw-bold border-bottom pb-2 mb-3">3. Observaciones</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Observaciones</label>
                                        <textarea class="form-control" wire:model="observaciones" rows="3" placeholder="Notas adicionales sobre el pago..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <a href="{{ route('admin.pagos.index') }}" class="btn btn-label-secondary me-2">Cancelar</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri ri-save-line me-1"></i> Registrar Pago
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
