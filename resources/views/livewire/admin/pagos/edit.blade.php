<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Editar Pago</h4>
            <p class="text-muted mb-0">Modificar información del pago</p>
        </div>
        <div>
            <a href="{{ route('admin.pagos.index') }}" class="btn btn-secondary">
                <i class="ri ri-arrow-left-line me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Formulario de Edición</h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="update">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="matricula_id" class="form-label">Matrícula</label>
                        <select wire:model="matricula_id" class="form-select @error('matricula_id') is-invalid @enderror" id="matricula_id" disabled>
                            <option value="">Seleccione una matrícula</option>
                            @foreach($matriculas as $matricula)
                                <option value="{{ $matricula->id }}">
                                    {{ $matricula->student->nombres ?? '' }} {{ $matricula->student->apellidos ?? '' }} - {{ $matricula->programa->nombre ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('matricula_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="concepto_pago_id" class="form-label">Concepto de Pago</label>
                        <select wire:model="concepto_pago_id" class="form-select @error('concepto_pago_id') is-invalid @enderror" id="concepto_pago_id" @if($pago->conceptoPago) disabled @endif>
                            <option value="">Seleccione un concepto</option>
                            @foreach($conceptos as $concepto)
                                <option value="{{ $concepto->id }}" @if($pago->concepto_pago_id == $concepto->id) selected @endif>
                                    {{ $concepto->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('concepto_pago_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="monto" class="form-label">Monto</label>
                        <input type="number" step="0.01" wire:model="monto" class="form-control @error('monto') is-invalid @enderror" id="monto">
                        @error('monto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="monto_pagado" class="form-label">Monto Pagado</label>
                        <input type="number" step="0.01" wire:model="monto_pagado" class="form-control @error('monto_pagado') is-invalid @enderror" id="monto_pagado">
                        @error('monto_pagado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="fecha_pago" class="form-label">Fecha de Pago</label>
                        <input type="date" wire:model="fecha_pago" class="form-control @error('fecha_pago') is-invalid @enderror" id="fecha_pago">
                        @error('fecha_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="metodo_pago" class="form-label">Método de Pago</label>
                        <select wire:model="metodo_pago" class="form-select @error('metodo_pago') is-invalid @enderror" id="metodo_pago">
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
                        </select>
                        @error('metodo_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="referencia" class="form-label">Referencia</label>
                        <input type="text" wire:model="referencia" class="form-control @error('referencia') is-invalid @enderror" id="referencia" placeholder="Nº de recibo, referencia bancaria, etc.">
                        @error('referencia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="estado" class="form-label">Estado</label>
                        <select wire:model="estado" class="form-select @error('estado') is-invalid @enderror" id="estado">
                            <option value="pendiente">Pendiente</option>
                            <option value="parcial">Parcial</option>
                            <option value="pagado">Pagado</option>
                        </select>
                        @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri ri-save-line me-1"></i> Actualizar Pago
                    </button>
                    <a href="{{ route('admin.pagos.index') }}" class="btn btn-secondary ms-2">
                        <i class="ri ri-arrow-left-line me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>