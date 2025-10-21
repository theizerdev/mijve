<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Editar Período Escolar</h2>
        <a href="{{ route('admin.school-periods.index') }}" class="btn btn-secondary">
             <i class="ri ri-arrow-left-line me-1"></i> Volver
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Editar Período Escolar</h5>
        </div>

        <div class="card-body">
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form wire:submit.prevent="update">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="name" class="form-label">Nombre *</label>
                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea id="description" class="form-control @error('description') is-invalid @enderror" wire:model="description" rows="3"></textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Fecha de Inicio *</label>
                        <input type="date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" wire:model="start_date">
                        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">Fecha de Fin *</label>
                        <input type="date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" wire:model="end_date">
                        @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch mt-4">
                            <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror" id="is_active" wire:model="is_active">
                            <label class="form-check-label" for="is_active">¿Está activo?</label>
                            @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    @if($schoolPeriod->is_current)
                        <div class="col-md-12 mb-3">
                            <div class="alert alert-info">
                                 <i class="ri ri-information-line me-1"></i> Este es el período escolar actual. No se puede desactivar.
                            </div>
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                         <i class="ri ri-save-line me-1"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
