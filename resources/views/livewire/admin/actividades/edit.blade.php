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
