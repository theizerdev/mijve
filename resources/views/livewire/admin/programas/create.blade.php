<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Crear Programa</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="store">
                    <div class="mb-3">
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

                    <div class="mb-3">
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

                    <div class="mb-3">
                        <label for="nivel_educativo_id" class="form-label">Nivel Educativo *</label>
                        <select
                            wire:model="nivel_educativo_id"
                            class="form-select @error('nivel_educativo_id') is-invalid @enderror"
                            id="nivel_educativo_id"
                            wire:change="updateCosts">
                            <option value="">Seleccione un nivel educativo</option>
                            @foreach($nivelesEducativos as $nivel)
                                <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                            @endforeach
                        </select>
                        @error('nivel_educativo_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="costo_matricula" class="form-label">Costo de Matrícula *</label>
                                <input
                                    type="number"
                                    wire:model="costo_matricula"
                                    class="form-control @error('costo_matricula') is-invalid @enderror"
                                    id="costo_matricula"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00">
                                @error('costo_matricula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="costo_mensualidad" class="form-label">Costo de Mensualidad *</label>
                                <input
                                    type="number"
                                    wire:model="costo_mensualidad"
                                    class="form-control @error('costo_mensualidad') is-invalid @enderror"
                                    id="costo_mensualidad"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00">
                                @error('costo_mensualidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
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
                        <a href="{{ route('admin.programas.index') }}" class="btn btn-secondary">
                            <i class="ri ri-arrow-left-line me-1"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri ri-save-line me-1"></i> Guardar Programa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @script
    <script>
        // No es necesario código JavaScript adicional ya que Livewire maneja todo
    </script>
    @endscript
</div>