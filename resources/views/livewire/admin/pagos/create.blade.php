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

    @if(!$caja_abierta)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="ri ri-alert-line me-2"></i>
                <div>
                    <strong>¡Atención!</strong> No hay una caja abierta para el día de hoy.
                    <br><small>Los pagos se registrarán sin asociar a ninguna caja. Se recomienda abrir una caja antes de registrar pagos.</small>
                </div>
            </div>
            <div class="mt-2">
                <a href="{{ route('admin.cajas.create') }}" class="btn btn-sm btn-warning">
                    <i class="ri ri-safe-line me-1"></i> Abrir Caja
                </a>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @else
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="ri ri-checkbox-circle-line me-2"></i>
                <div>
                    <strong>Caja Abierta:</strong> {{ $caja_abierta->fecha->format('d/m/Y') }}
                    <br><small>Monto inicial: ${{ number_format($caja_abierta->monto_inicial, 2) }} | Usuario: {{ $caja_abierta->usuario->name }}</small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Nuevo Pago</h4>
            <p class="text-muted mb-0">Registrar pagos de estudiantes</p>
        </div>
        <a href="{{ route('admin.pagos.index') }}" class="btn btn-secondary">
            <i class="ri ri-arrow-left-line me-1"></i> Volver
        </a>
    </div>

    <!-- Información del documento -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Documento *</label>
                            <select wire:model.change="tipo_pago" class="form-select @error('tipo_pago') is-invalid @enderror" required>
                                @foreach($tipos as $key => $tipo)
                                    <option value="{{ $key }}">{{ $tipo }}</option>
                                @endforeach
                            </select>
                            @error('tipo_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de Documento</label>
                            <input type="text" value="{{ $numero_documento ?? 'Seleccione tipo de documento' }}" class="form-control" readonly>
                            @if(!$serie_actual && $tipo_pago)
                                <div class="text-danger small mt-1">No hay series configuradas para este tipo de documento</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" wire:model="fecha_pago" class="form-control @error('fecha_pago') is-invalid @enderror">
                            @error('fecha_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Método de Pago</label>
                            <select wire:model="metodo_pago" class="form-select @error('metodo_pago') is-invalid @enderror">
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="tarjeta">Tarjeta</option>
                            </select>
                            @error('metodo_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Referencia</label>
                            <input type="text" wire:model="referencia" class="form-control @error('referencia') is-invalid @enderror" placeholder="Opcional">
                            @error('referencia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Selección de matrícula y cuotas -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Seleccionar Estudiante</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Matrícula *</label>
                        <select wire:model.change="matricula_id" class="form-select @error('matricula_id') is-invalid @enderror" required>
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
                        @php $matricula = $matriculas->firstWhere('id', $matricula_id); @endphp
                        @if($matricula)
                        <div class="border rounded p-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-sm me-3">
                                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                        {{ substr($matricula->student->nombres, 0, 1) }}{{ substr($matricula->student->apellidos, 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $matricula->student->nombres }} {{ $matricula->student->apellidos }}</h6>
                                    <small class="text-muted">{{ $matricula->student->documento_identidad }}</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Programa:</small>
                                    <p class="mb-2 fw-medium">{{ $matricula->programa->nombre }}</p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Fecha Matrícula:</small>
                                    <p class="mb-2">{{ $matricula->fecha_matricula->format('d/m/Y') }}</p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Estado:</small>
                                    <span class="badge bg-success-subtle text-success">Activa</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Costo Total:</small>
                                    <p class="mb-0 fw-bold text-primary">${{ number_format($matricula->costo ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="border rounded p-4 text-center">
                            <i class="ri ri-user-search-line ri ri-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Seleccione una matrícula</p>
                            <small class="text-muted">para ver la información del estudiante</small>
                        </div>
                    @endif
                </div>
            </div>
          </div>
          <div class="col-lg-6 mb-4">
              @if($matricula_id && count($cuotasPendientes) > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Cuotas Pendientes</h6>
                </div>
                <div class="card-body p-0">
                    <div class="cuotas-scroll" style="max-height: 350px; overflow-y: auto;">
                        <div class="p-3">
                            @foreach($cuotasPendientes as $cuota)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 class="mb-1">Cuota #{{ $cuota->numero_cuota }}</h6>
                                        <small class="text-muted">{{ $cuota->fecha_vencimiento->format('M Y') }}</small>
                                        @if($cuota->fecha_vencimiento < now())
                                            <span class="badge bg-label-danger ms-2">Vencida</span>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-primary">${{ number_format($cuota->saldo_pendiente, 2) }}</div>
                                        @if($cuota->saldo_pendiente != $cuota->monto)
                                            <small class="text-muted">Total: ${{ number_format($cuota->monto, 2) }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button wire:click="seleccionarCuota({{ $cuota->id }})" class="btn btn-sm btn-primary flex-fill">
                                        <i class="ri ri-add-line"></i> Pagar Completa
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Abono
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" wire:click="agregarAbono({{ $cuota->id }}, {{ $cuota->saldo_pendiente * 0.5 }})">50%</a></li>
                                            <li><a class="dropdown-item" href="#" wire:click="agregarAbono({{ $cuota->id }}, {{ $cuota->saldo_pendiente * 0.25 }})">25%</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="#" onclick="promptAbono({{ $cuota->id }}, {{ $cuota->saldo_pendiente }})">Personalizado</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
          </div>

        <!-- Carrito de pagos -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detalle del Pago</h5>
                    <button wire:click="agregarDetalle" class="btn btn-sm btn-outline-primary">
                        <i class="ri ri-add-line"></i> Agregar
                    </button>
                </div>
                <div class="card-body">
                    @if(count($detalles) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Concepto</th>
                                        <th>Descripción</th>
                                        <th width="120">Cant.</th>
                                        <th width="150">Precio</th>
                                        <th width="100">Total</th>
                                        <th width="50"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detalles as $index => $detalle)
                                    <tr>
                                        <td>
                                            <select wire:model="detalles.{{ $index }}.concepto_pago_id" class="form-select form-select-sm">
                                                <option value="">Seleccionar...</option>
                                                @foreach($conceptos as $concepto)
                                                    <option value="{{ $concepto->id }}">{{ $concepto->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" wire:model="detalles.{{ $index }}.descripcion" class="form-control form-control-sm" placeholder="Descripción">
                                        </td>
                                        <td>
                                            <input type="number" wire:model.blur="detalles.{{ $index }}.cantidad" class="form-control" min="1" step="1">
                                        </td>
                                        <td>
                                            <input type="number" wire:model.blur="detalles.{{ $index }}.precio_unitario" class="form-control" min="0" step="0.01">
                                        </td>
                                        <td class="text-end">
                                            ${{ number_format($this->calcularSubtotal($index), 2) }}
                                        </td>
                                        <td>
                                            <button wire:click="eliminarDetalle({{ $index }})" class="btn btn-sm btn-outline-danger">
                                                <i class="ri ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Totales -->
                        <div class="border-top pt-3 mt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Descuento</label>
                                        <input type="number" wire:model.blur="descuento" class="form-control" min="0" step="0.01">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Observaciones</label>
                                        <textarea wire:model="observaciones" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-light p-3 rounded">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal:</span>
                                            <span>${{ number_format($this->subtotal, 2) }}</span>
                                        </div>
                                        @if($descuento > 0)
                                        <div class="d-flex justify-content-between mb-2 text-danger">
                                            <span>Descuento:</span>
                                            <span>-${{ number_format($descuento, 2) }}</span>
                                        </div>
                                        @endif
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold fs-5">
                                            <span>Total:</span>
                                            <span class="text-primary">${{ number_format($this->total, 2) }}</span>
                                        </div>
                                    </div>

                                    <button wire:click="guardar" class="btn btn-success w-100 mt-3" @if($this->total <= 0 || !$matricula_id || !$this->serie_actual) disabled @endif>
                                        <i class="ri ri-save-line me-1"></i> Registrar Pago
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ri ri-shopping-cart-2-line ri ri-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No hay conceptos agregados</p>
                            <p class="text-muted small">Seleccione cuotas o agregue conceptos manualmente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.cuotas-scroll {
    scrollbar-width: thin;
    scrollbar-color: #6c757d #f8f9fa;
}

.cuotas-scroll::-webkit-scrollbar {
    width: 8px;
}

.cuotas-scroll::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 4px;
}

.cuotas-scroll::-webkit-scrollbar-thumb {
    background: #6c757d;
    border-radius: 4px;
}

.cuotas-scroll::-webkit-scrollbar-thumb:hover {
    background: #495057;
}
</style>
@endpush

@push('scripts')
<script>
function promptAbono(cuotaId, saldoMaximo) {
    const monto = prompt(`Ingrese el monto del abono (máximo: $${saldoMaximo.toFixed(2)}):`);
    if (monto && !isNaN(monto) && parseFloat(monto) > 0 && parseFloat(monto) <= saldoMaximo) {
        @this.call('agregarAbono', cuotaId, parseFloat(monto));
    } else if (monto) {
        alert('Monto inválido. Debe ser mayor a 0 y no exceder el saldo pendiente.');
    }
}
</script>
@endpush
