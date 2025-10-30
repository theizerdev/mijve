<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Crear Programa</h4>
            <p class="text-muted mb-0">Registrar un nuevo programa académico</p>
        </div>
        <div>
            <a href="{{ route('admin.programas.index') }}" class="btn btn-secondary">
                <i class="ri ri-arrow-left-line me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información del Programa</h5>
                </div>
                <div class="card-body">
                    <form wire:submit="store">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input 
                                    type="text" 
                                    wire:model="nombre" 
                                    class="form-control @error('nombre') is-invalid @enderror" 
                                    id="nombre" 
                                    placeholder="Nombre del programa">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="nivel_educativo_id" class="form-label">Nivel Educativo *</label>
                                <select 
                                    wire:model="nivel_educativo_id" 
                                    class="form-select @error('nivel_educativo_id') is-invalid @enderror" 
                                    id="nivel_educativo_id">
                                    <option value="">Seleccione un nivel educativo</option>
                                    @foreach($nivelesEducativos as $nivel)
                                        <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('nivel_educativo_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea 
                                    wire:model="descripcion" 
                                    class="form-control @error('descripcion') is-invalid @enderror" 
                                    id="descripcion" 
                                    rows="3" 
                                    placeholder="Descripción del programa"></textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input 
                                        type="checkbox" 
                                        wire:model="activo" 
                                        class="form-check-input" 
                                        id="activo">
                                    <label class="form-check-label" for="activo">Activo</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri ri-save-line me-1"></i> Guardar Programa
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>