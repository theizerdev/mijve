<div>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Nueva Serie de Documentos</h5>
        </div>

        <div class="card-body">
            <form wire:submit="guardar">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de Documento</label>
                        <select wire:model.live="tipo_documento" class="form-select @error('tipo_documento') is-invalid @enderror">
                            @foreach($tipos as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('tipo_documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Serie</label>
                        <input type="text" wire:model="serie" class="form-control @error('serie') is-invalid @enderror" placeholder="F001">
                        @error('serie') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Correlativo Inicial</label>
                        <input type="number" wire:model="correlativo_actual" class="form-control @error('correlativo_actual') is-invalid @enderror" min="0">
                        @error('correlativo_actual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Longitud del Correlativo</label>
                        <input type="number" wire:model="longitud_correlativo" class="form-control @error('longitud_correlativo') is-invalid @enderror" min="4" max="12">
                        @error('longitud_correlativo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @if(auth()->user()->hasRole('Super Administrador'))
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Empresa</label>
                        <select wire:model.live="empresa_id" class="form-select @error('empresa_id') is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            @foreach($empresas as $empresa)
                                <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                            @endforeach
                        </select>
                        @error('empresa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sucursal</label>
                        <select wire:model="sucursal_id" class="form-select @error('sucursal_id') is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                            @endforeach
                        </select>
                        @error('sucursal_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    @endif

                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <input type="checkbox" wire:model="activo" class="form-check-input" id="activo">
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.series.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
