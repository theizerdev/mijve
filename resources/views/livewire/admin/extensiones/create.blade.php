<div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nueva Extensión</h5>
                    <a href="{{ route('admin.extensiones.index') }}" class="btn btn-secondary">
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
                                <label class="form-label">Nombre de la Extensión</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                       wire:model="nombre" placeholder="Nombre de la extensión">
                                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Zona</label>
                                <input type="text" class="form-control @error('zona') is-invalid @enderror" 
                                       wire:model="zona" placeholder="Zona">
                                @error('zona') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Distrito</label>
                                <input type="text" class="form-control @error('distrito') is-invalid @enderror" 
                                       wire:model="distrito" placeholder="Distrito">
                                @error('distrito') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <h6 class="fw-bold mt-3 mb-3 border-bottom pb-2">Información del Líder</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nombre del Líder</label>
                                <input type="text" class="form-control @error('lider_nombre') is-invalid @enderror" 
                                       wire:model="lider_nombre" placeholder="Nombre completo del líder">
                                @error('lider_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Teléfono (WhatsApp)</label>
                                <input type="text" class="form-control @error('lider_telefono') is-invalid @enderror" 
                                       wire:model="lider_telefono" placeholder="+584121234567">
                                <div class="form-text">Formato internacional requerido para notificaciones</div>
                                @error('lider_telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                    <option value="Pendiente">Pendiente</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri ri-save-line me-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
