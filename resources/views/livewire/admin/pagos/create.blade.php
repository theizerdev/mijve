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

                    @if($matricula_id)
                        <!-- Sección de Cuotas de Matrícula -->
                        @if(count($paymentSchedule) > 0)
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">Cuotas de Matrícula</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                                                </th>
                                                <th>Cuota</th>
                                                <th>Monto</th>
                                                <th>Pagado</th>
                                                <th>Fecha Venc.</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($paymentSchedule as $cuota)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" wire:model.live="selectedCuotas" value="{{ $cuota->id }}" class="form-check-input">
                                                </td>
                                                <td>
                                                    @if($cuota->numero_cuota == 0)
                                                        Cuota inicial
                                                    @else
                                                        Cuota {{ $cuota->numero_cuota }}
                                                    @endif
                                                </td>
                                                <td>${{ number_format($cuota->monto, 2) }}</td>
                                                <td>${{ number_format($cuota->monto_pagado ?? 0, 2) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('d/m/Y') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer">
                                    <button wire:click="agregarCuotasSeleccionadas" class="btn btn-sm btn-outline-primary w-100" @if(count($selectedCuotas) == 0) disabled @endif>
                                        <i class="ri ri-add-line me-1"></i> Agregar Cuotas Seleccionadas ({{ count($selectedCuotas) }})
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Sección de Conceptos de Pago -->
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">Otros Conceptos de Pago</h5>
                            </div>
                            <div class="card-body">
                                <form wire:submit.prevent="agregarConcepto">
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
                    @endif
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
                        <div class="input-group">
                            <select wire:model="metodo_pago" class="form-select @error('metodo_pago') is-invalid @enderror" id="metodo_pago" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia Bancaria</option>
                                <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                            </select>
                            @if($metodo_pago == 'transferencia' || $metodo_pago == 'tarjeta')
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalReferencia">
                                    <i class="ri ri-information-line"></i>
                                </button>
                            @endif
                        </div>
                        @error('metodo_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Modal para instrucciones de pago -->
                    <div class="modal fade" id="modalReferencia" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Instrucciones para {{ $metodo_pago == 'transferencia' ? 'Transferencia' : 'Tarjeta' }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    @if($metodo_pago == 'transferencia')
                                        <p>Por favor realice la transferencia a:</p>
                                        <ul>
                                            <li>Banco: Banco Nacional</li>
                                            <li>Cuenta: 123-456-789</li>
                                            <li>Titular: Institución Educativa</li>
                                            <li>RUC: 12345678901</li>
                                        </ul>
                                    @else
                                        <p>Los pagos con tarjeta se procesan a través de nuestra pasarela segura.</p>
                                        <p>Se aceptan todas las tarjetas Visa, Mastercard y American Express.</p>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
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
                                        <td>
                                            {{ $item['concepto_nombre'] }}
                                            @if($item['es_parcial'] || ($item['monto_pagado'] ?? 0) < $item['monto'])
                                                <span class="badge bg-warning">Parcial</span>
                                            @else
                                                <span class="badge bg-success">Completo</span>
                                            @endif
                                            @if(isset($item['numero_cuota']))
                                                <span class="badge bg-info">Cuota {{ $item['numero_cuota'] }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($item['es_parcial'] || ($item['monto_pagado'] ?? 0) < $item['monto'])
                                                <div class="mb-1">
                                                    <small class="text-muted">Pagado anterior: ${{ number_format($item['monto_pagado'] ?? 0, 2) }}</small>
                                                </div>
                                            @endif
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">$</span>
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    wire:model.live="carrito.{{ $index }}.monto_pagado"
                                                    wire:change="calcularTotal"
                                                    class="form-control text-end"
                                                    min="0"
                                                    max="{{ $item['monto'] }}"
                                                    style="width: 100px;"
                                                    @if(!($item['es_parcial'] || ($item['monto_pagado'] ?? 0) < $item['monto'])) readonly @endif
                                                    title="Máximo: ${{ number_format($item['monto'], 2) }}"
                                                >
                                                <button
                                                    class="btn btn-outline-secondary"
                                                    type="button"
                                                    wire:click="togglePagoParcial({{ $index }})"
                                                    title="{{ ($item['es_parcial'] || ($item['monto_pagado'] ?? 0) < $item['monto']) ? 'Marcar como pago completo' : 'Marcar como pago parcial' }}"
                                                >
                                                    <i class="ri ri-{{ ($item['es_parcial'] || ($item['monto_pagado'] ?? 0) < $item['monto']) ? 'check-double-line' : 'percent-line' }}"></i>
                                                </button>
                                            </div>
                                            <div class="mt-1">
                                                @if($item['es_parcial'] || ($item['monto_pagado'] ?? 0) < $item['monto'])
                                                    <small class="text-success">Saldo pendiente: ${{ number_format($item['monto'] - ($item['monto_pagado'] ?? 0), 2) }}</small>
                                                @else
                                                    <small class="text-muted">Total: ${{ number_format($item['monto'], 2) }}</small>
                                                @endif
                                            </div>
                                        </td>
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
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Total:</span>
                                                <span class="fw-bold" id="payment-total">${{ number_format($total, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Pagado:</span>
                                                <span class="fw-bold" id="payment-paid">${{ number_format(array_sum(array_column($carrito, 'monto_pagado')), 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Saldo:</span>
                                                <span class="fw-bold" id="payment-balance">${{ number_format($total - array_sum(array_column($carrito, 'monto_pagado')), 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button wire:click="store" class="btn btn-success w-100" @if($total <= 0) disabled @endif>
                                <i class="ri ri-checkbox-circle-line me-1"></i> Registrar Pagos y Generar Comprobante ({{ count($carrito) }} conceptos)
                            </button>
                            <div class="text-center mt-2">
                                <small class="text-muted">Se generará un comprobante por cada pago registrado</small>
                            </div>
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
  @push('scripts')
    <script>
        document.addEventListener('livewire:load', function() {
            console.log('Livewire initialized, setting up payment totals listener');

            Livewire.on('update-payment-totals', ({total, totalPagado, saldoPendiente}) => {
                console.log('Received update-payment-totals event', {total, totalPagado, saldoPendiente});

                const totalEl = document.getElementById('payment-total');
                const paidEl = document.getElementById('payment-paid');
                const balanceEl = document.getElementById('payment-balance');

                if (totalEl && paidEl && balanceEl) {
                    totalEl.textContent = '$' + total.toFixed(2);
                    paidEl.textContent = '$' + totalPagado.toFixed(2);
                    balanceEl.textContent = '$' + saldoPendiente.toFixed(2);
                    console.log('Updated payment totals in UI');
                } else {
                    console.error('Could not find all payment total elements');
                }
            });
        });
    </script>
@endpush