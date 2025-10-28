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
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6 class="mb-3">Información de Costos</h6>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="costo" class="form-label">Costo General *</label>
                        <input wire:model="costo" type="number" step="0.01" class="form-control @error('costo') is-invalid @enderror" id="costo" placeholder="0.00">
                        @error('costo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="costo_matricula" class="form-label">Costo de Matrícula *</label>
                        <input wire:model="costo_matricula" type="number" step="0.01" class="form-control @error('costo_matricula') is-invalid @enderror" id="costo_matricula" placeholder="0.00">
                        @error('costo_matricula')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="costo_mensualidad" class="form-label">Costo de Mensualidad *</label>
                        <input wire:model="costo_mensualidad" type="number" step="0.01" class="form-control @error('costo_mensualidad') is-invalid @enderror" id="costo_mensualidad" placeholder="0.00">
                        @error('costo_mensualidad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="cuota_inicial" class="form-label">Cuota Inicial *</label>
                        <input wire:model="cuota_inicial" type="number" step="0.01" class="form-control @error('cuota_inicial') is-invalid @enderror" id="cuota_inicial" placeholder="0.00">
                        @error('cuota_inicial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="numero_cuotas" class="form-label">Número de Cuotas *</label>
                        <input wire:model="numero_cuotas" type="number" class="form-control @error('numero_cuotas') is-invalid @enderror" id="numero_cuotas" placeholder="0">
                        @error('numero_cuotas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6 class="mb-3">Configuración</h6>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch">
                            <input wire:model="status" type="checkbox" class="form-check-input" id="status" @if($status) checked @endif>
                            <label class="form-check-label" for="status">Activo</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.niveles-educativos.index') }}" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri ri-save-line me-1"></i> Actualizar Nivel Educativo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
