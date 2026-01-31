<div>
    <div class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <h4 class="card-title">Editar Aula</h4>
                <p class="card-title-desc">Modifique la información del aula</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.classrooms.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form wire:submit.prevent="update">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Aula *</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" wire:model="nombre" placeholder="Ej: Aula 101, Laboratorio de Computación">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="codigo" class="form-label">Código *</label>
                            <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                                   id="codigo" wire:model="codigo" placeholder="Ej: A101, LAB-01">
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tipo_aula" class="form-label">Tipo de Aula *</label>
                            <select class="form-select @error('tipo_aula') is-invalid @enderror" id="tipo_aula" wire:model="tipo_aula">
                                <option value="">Seleccione un tipo</option>
                                <option value="regular">Regular</option>
                                <option value="laboratorio">Laboratorio</option>
                                <option value="taller">Taller</option>
                                <option value="auditorio">Auditorio</option>
                                <option value="biblioteca">Biblioteca</option>
                                <option value="otro">Otro</option>
                            </select>
                            @error('tipo_aula')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="capacidad" class="form-label">Capacidad *</label>
                            <input type="number" class="form-control @error('capacidad') is-invalid @enderror" 
                                   id="capacidad" wire:model="capacidad" min="1" max="200">
                            @error('capacidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="empresa_id" class="form-label">Empresa</label>
                            <select class="form-select @error('empresa_id') is-invalid @enderror" id="empresa_id" wire:model="empresa_id">
                                <option value="">Seleccione una empresa</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                @endforeach
                            </select>
                            @error('empresa_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sucursal_id" class="form-label">Sucursal</label>
                            <select class="form-select @error('sucursal_id') is-invalid @enderror" id="sucursal_id" wire:model="sucursal_id">
                                <option value="">Seleccione una sucursal</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                @endforeach
                            </select>
                            @error('sucursal_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="ubicacion" class="form-label">Ubicación</label>
                            <input type="text" class="form-control @error('ubicacion') is-invalid @enderror" 
                                   id="ubicacion" wire:model="ubicacion" placeholder="Ej: Primer piso, ala este">
                            @error('ubicacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" wire:model="is_active">
                                <label class="form-check-label" for="is_active">
                                    {{ $is_active ? 'Activo' : 'Inactivo' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Recursos Disponibles</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="proyector" id="recurso_proyector" wire:model="recursos">
                                        <label class="form-check-label" for="recurso_proyector">Proyector</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="pizarra" id="recurso_pizarra" wire:model="recursos">
                                        <label class="form-check-label" for="recurso_pizarra">Pizarra</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="aire_acondicionado" id="recurso_aire" wire:model="recursos">
                                        <label class="form-check-label" for="recurso_aire">Aire Acondicionado</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="computadoras" id="recurso_computadoras" wire:model="recursos">
                                        <label class="form-check-label" for="recurso_computadoras">Computadoras</label>
                                    </div>
                                </div>
                            </div>
                            @error('recursos')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Aula
                        </button>
                        <a href="{{ route('admin.classrooms.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>