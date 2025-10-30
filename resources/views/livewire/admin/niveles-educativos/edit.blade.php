<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Editar Nivel Educativo</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="mb-3">Información Básica</h6>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input wire:model="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" placeholder="Ingrese el nombre del nivel educativo">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea wire:model="descripcion" class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" rows="3" placeholder="Ingrese una descripción del nivel educativo"></textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch">
                            <input wire:model="status" class="form-check-input" type="checkbox" id="status" {{ $status ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">Activo</label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <a href="{{ route('admin.niveles-educativos.index') }}" class="btn btn-label-secondary me-2">
                            <i class="ri ri-arrow-left-line me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri ri-save-line me-1"></i>Actualizar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
