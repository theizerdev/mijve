<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Crear Nuevo Turno</h2>
        <a href="{{ route('admin.turnos.index') }}" class="btn btn-outline-secondary">
            <i class="ri ri-arrow-left-line me-1"></i> Volver
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Información del Turno</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" wire:model="nombre" class="form-control @error('nombre') is-invalid @enderror">
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea wire:model="descripcion" class="form-control @error('descripcion') is-invalid @enderror"></textarea>
                        @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hora de Inicio</label>
                        <input type="time" wire:model="hora_inicio" class="form-control @error('hora_inicio') is-invalid @enderror">
                        @error('hora_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hora de Fin</label>
                        <input type="time" wire:model="hora_fin" class="form-control @error('hora_fin') is-invalid @enderror">
                        @error('hora_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Estado</label>
                        <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri ri-save-line me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>