<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Editar Concepto de Pago</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="update">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input
                            type="text"
                            wire:model="nombre"
                            class="form-control @error('nombre') is-invalid @enderror"
                            id="nombre"
                            placeholder="Nombre del concepto de pago">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea
                            wire:model="descripcion"
                            class="form-control @error('descripcion') is-invalid @enderror"
                            id="descripcion"
                            rows="3"
                            placeholder="Descripción del concepto de pago"></textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input
                                type="checkbox"
                                wire:model="activo"
                                class="form-check-input"
                                id="activo">
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.conceptos-pago.index') }}" class="btn btn-secondary">
                            <i class="ri ri-arrow-left-line me-1"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri ri-save-line me-1"></i> Actualizar Concepto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
