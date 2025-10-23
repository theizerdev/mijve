<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Crear Nivel Educativo</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input wire:model="nombre" type="text" class="form-control" id="nombre">
                    @error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea wire:model="descripcion" class="form-control" id="descripcion"></textarea>
                    @error('descripcion') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="costo" class="form-label">Costo</label>
                    <input wire:model="costo" type="number" step="0.01" class="form-control" id="costo">
                    @error('costo') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="numero_cuotas" class="form-label">Número de Cuotas</label>
                    <input wire:model="numero_cuotas" type="number" class="form-control" id="numero_cuotas">
                    @error('numero_cuotas') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="cuota_inicial" class="form-label">Cuota Inicial</label>
                    <input wire:model="cuota_inicial" type="number" step="0.01" class="form-control" id="cuota_inicial">
                    @error('cuota_inicial') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Estado</label>
                    <select wire:model="status" class="form-select" id="status">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.niveles-educativos.index') }}" class="btn btn-secondary me-2">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
