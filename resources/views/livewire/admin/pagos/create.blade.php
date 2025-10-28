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
            <h4 class="mb-0">Nuevo Pago</h4>
            <p class="text-muted mb-0">Registrar nuevos pagos en estilo de carrito de compras</p>
        </div>
        <div>
            <a href="{{ route('admin.pagos.index') }}" class="btn btn-secondary">
                <i class="ri ri-arrow-left-line me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Panel izquierdo - Formulario de selección -->
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información del Pago</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="agregarConcepto">
                        <div class="mb-3">
                            <label for="matricula_id" class="form-label">Matrícula *</label>
                            <select wire:model.change="matricula_id" class="form-select @error('matricula_id') is-invalid @enderror" id="matricula_id" required>
                                <option value="">Seleccione una matrícula</option>
                                @foreach($matriculas as $matricula)
                                    <option value="{{ $matricula->id }}">
                                        {{ $matricula->student->nombres ?? '' }} {{ $matricula->student->apellidos ?? '' }} - {{ $matricula->programa->nombre ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('matricula_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="concepto_id" class="form-label">Concepto de Pago</label>
                                    <select wire:model="concepto_id" class="form-select @error('concepto_id') is-invalid @enderror" id="concepto_id">
                                        <option value="">Seleccione un concepto</option>
                                        @foreach($conceptos as $concepto)
                                            <option value="{{ $concepto->id }}">{{ $concepto->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('concepto_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="monto" class="form-label">Monto</label>
                                    <input type="number" step="0.01" wire:model="monto" class="form-control @error('monto') is-invalid @enderror" id="monto">
                                    @error('monto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="ri ri-add-line me-1"></i> Agregar al Carrito
                        </button>
                    </form>

                    <button wire:click="agregarCostosAutomaticos" class="btn btn-outline-primary w-100" @if(!$matricula_id) disabled @endif>
                        <i class="ri ri-bill-line me-1"></i> Agregar Costos Automáticos
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detalles del Pago</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="fecha_pago" class="form-label">Fecha de Pago</label>
                        <input type="date" wire:model="fecha_pago" class="form-control @error('fecha_pago') is-invalid @enderror" id="fecha_pago">
                        @error('fecha_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="metodo_pago" class="form-label">Método de Pago *</label>
                        <select wire:model="metodo_pago" class="form-select @error('metodo_pago') is-invalid @enderror" id="metodo_pago" required>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
                        </select>
                        @error('metodo_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="referencia" class="form-label">Referencia</label>
                        <input type="text" wire:model="referencia" class="form-control @error('referencia') is-invalid @enderror" id="referencia" placeholder="Nº de recibo, referencia bancaria, etc.">
                        @error('referencia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel derecho - Carrito de compras -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Carrito de Pagos</h5>
                </div>
                <div class="card-body">
                    @if(count($carrito) > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Concepto</th>
                                        <th class="text-end">Monto</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($carrito as $index => $item)
                                        <tr>
                                            <td>{{ $item['concepto_nombre'] }}</td>
                                            <td class="text-end">${{ number_format($item['monto'], 2) }}</td>
                                            <td class="text-center">
                                                <button wire:click="removerItem({{ $index }})" class="btn btn-sm btn-danger" title="Remover">
                                                    <i class="ri ri-delete-bin-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-light">
                                        <td class="fw-bold">Total</td>
                                        <td class="text-end fw-bold">${{ number_format($total, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <button wire:click="store" class="btn btn-success w-100" @if($total <= 0) disabled @endif>
                                <i class="ri ri-checkbox-circle-line me-1"></i> Registrar Pagos ({{ count($carrito) }} conceptos)
                            </button>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ri ri-shopping-cart-2-line ri-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">El carrito está vacío</p>
                            <p class="text-muted small">Agregue conceptos de pago utilizando el formulario</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($matricula_id)
                @php
                    $matricula = $matriculas->firstWhere('id', $matricula_id);
                @endphp
                @if($matricula)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Información del Estudiante</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Nombre:</strong></p>
                                    <p class="text-muted">{{ $matricula->student->nombres }} {{ $matricula->student->apellidos }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Programa:</strong></p>
                                    <p class="text-muted">{{ $matricula->programa->nombre }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Documento:</strong></p>
                                    <p class="text-muted">{{ $matricula->student->documento_identidad }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Fecha de Matrícula:</strong></p>
                                    <p class="text-muted">{{ $matricula->fecha_matricula->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
