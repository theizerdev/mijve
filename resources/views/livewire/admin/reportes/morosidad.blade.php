<div>
    <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Reporte de Morosidad</h4>
            <p class="text-muted mb-0">Morosidad por nivel/programa</p>
        </div>
        <div>
            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show me-2" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show me-2" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <button wire:click="exportarExcel" class="btn btn-success me-2" @if(count($morosos) == 0) disabled @endif>
                <i class="ri ri-file-excel-line me-1"></i> Exportar Excel
            </button>
            <button wire:click="exportarPDF" class="btn btn-danger me-2" @if(count($morosos) == 0) disabled @endif>
                <i class="ri ri-file-pdf-line me-1"></i> Exportar PDF
            </button>
            <button wire:click="enviarNotificaciones" class="btn btn-warning" @if(count($morosos) == 0) disabled @endif>
                <i class="ri ri-notification-line me-1"></i> Enviar Notificaciones
            </button>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nivel_educativo_id" class="form-label">Nivel Educativo</label>
                        <select wire:model.live="nivel_educativo_id" class="form-select" id="nivel_educativo_id">
                            <option value="">Todos los niveles</option>
                            @foreach($nivelesEducativos as $nivel)
                                <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="programa_id" class="form-label">Programa</label>
                        <select wire:model.live="programa_id" class="form-select" id="programa_id" @if($programas->count() == 0) disabled @endif>
                            <option value="">Todos los programas</option>
                            @foreach($programas as $programa)
                                <option value="{{ $programa->id }}">{{ $programa->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <button wire:click="cargarReporte" class="btn btn-primary">
                <i class="ri ri-search-line me-1"></i> Generar Reporte
            </button>
        </div>
    </div>

    @if(isset($totales) && isset($totales['total_estudiantes']) && $totales['total_estudiantes'] > 0)
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="mb-1 text-muted">Total Estudiantes</p>
                        <h3 class="mb-0">{{ $totales['total_estudiantes'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="mb-1 text-muted">Estudiantes Morosos</p>
                        <h3 class="mb-0 text-danger">{{ $totales['total_morosos'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="mb-1 text-muted">Porcentaje de Morosidad</p>
                        <h3 class="mb-0
                            @if(($totales['porcentaje_morosidad'] ?? 0) > 20) text-danger
                            @elseif(($totales['porcentaje_morosidad'] ?? 0) > 10) text-warning
                            @else text-success @endif">
                            {{ number_format($totales['porcentaje_morosidad'] ?? 0, 2) }}%
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(count($morosos) > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Estudiantes Morosos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Estudiante</th>
                                <th>Programa</th>
                                <th>Nivel</th>
                                <th class="text-end">Costo Total</th>
                                <th class="text-end">Pagado</th>
                                <th class="text-end">Saldo Pendiente</th>
                                <th class="text-end">Porcentaje Pagado</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($morosos as $moroso)
                                <tr>
                                    <td>
                                        {{ $moroso['matricula']->student->nombres ?? '' }}
                                        {{ $moroso['matricula']->student->apellidos ?? '' }}
                                        <div class="small text-muted">
                                            {{ $moroso['matricula']->student->documento_identidad ?? '' }}
                                        </div>
                                    </td>
                                    <td>{{ $moroso['matricula']->programa->nombre ?? '' }}</td>
                                    <td>{{ $moroso['matricula']->programa->nivelEducativo->nombre ?? '' }}</td>
                                    <td class="text-end">${{ number_format($moroso['matricula']->costo ?? 0, 2) }}</td>
                                    <td class="text-end">${{ number_format($moroso['total_pagado'], 2) }}</td>
                                    <td class="text-end">${{ number_format($moroso['saldo_pendiente'], 2) }}</td>
                                    <td class="text-end">{{ number_format($moroso['porcentaje_pagado'], 2) }}%</td>
                                    <td>
                                        @if($moroso['porcentaje_pagado'] < 30)
                                            <span class="badge bg-danger">Alto Riesgo</span>
                                        @elseif($moroso['porcentaje_pagado'] < 60)
                                            <span class="badge bg-warning">Medio Riesgo</span>
                                        @else
                                            <span class="badge bg-info">Bajo Riesgo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button wire:click="mostrarDetalleDeuda({{ $moroso['matricula']->id }})" class="btn btn-sm btn-primary">
                                            <i class="ri ri-eye-line"></i> Detalle
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif(isset($totales) && isset($totales['total_estudiantes']) && $totales['total_estudiantes'] > 0)
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="ri ri-checkbox-circle-line ri-3x text-success mb-3"></i>
                <h5 class="mb-2">¡Excelente!</h5>
                <p class="text-muted mb-0">No se encontraron estudiantes morosos con los filtros aplicados</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="ri ri-search-eye-line ri-3x text-muted mb-3"></i>
                <h5 class="mb-2">No hay datos para mostrar</h5>
                <p class="text-muted mb-0">Configure los filtros y genere el reporte</p>
            </div>
        </div>
    @endif
</div>

<!-- Modal para mostrar el detalle de la deuda -->
<div class="modal fade" id="detalleDeudaModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Deuda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="cerrarModal"></button>
            </div>
            <div class="modal-body">
                @if($estudianteSeleccionado)
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Estudiante:</strong> {{ $estudianteSeleccionado->student->nombres ?? '' }} {{ $estudianteSeleccionado->student->apellidos ?? '' }}</p>
                            <p><strong>Documento:</strong> {{ $estudianteSeleccionado->student->documento_identidad ?? '' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Programa:</strong> {{ $estudianteSeleccionado->programa->nombre ?? '' }}</p>
                            <p><strong>Nivel:</strong> {{ $estudianteSeleccionado->programa->nivelEducativo->nombre ?? '' }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <p class="mb-1 text-muted">Costo Total</p>
                                <h4 class="mb-0">${{ number_format($estudianteSeleccionado->costo ?? 0, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <p class="mb-1 text-muted">Total Pagado</p>
                                <h4 class="mb-0 text-success">${{ number_format($estudianteSeleccionado->pagos->sum('monto_pagado'), 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center bg-warning bg-opacity-10">
                                <p class="mb-1 text-muted">Saldo Pendiente</p>
                                <h4 class="mb-0 text-warning">
                                    ${{ number_format(($estudianteSeleccionado->costo ?? 0) - $estudianteSeleccionado->pagos->sum('monto_pagado'), 2) }}
                                </h4>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Detalle de Pagos</h6>
                        <button wire:click="enviarNotificacionDeuda" class="btn btn-sm btn-warning">
                            <i class="ri ri-mail-send-line me-1"></i> Enviar Notificación
                        </button>
                    </div>

                    @if(count($detalleDeuda) > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Concepto</th>
                                        <th class="text-end">Monto</th>
                                        <th class="text-end">Pagado</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detalleDeuda as $pago)
                                        <tr>
                                            <td>{{ $pago->fecha_pago?->format('d/m/Y') ?? 'N/A' }}</td>
                                            <td>{{ $pago->conceptoPago->nombre ?? 'N/A' }}</td>
                                            <td class="text-end">${{ number_format($pago->monto, 2) }}</td>
                                            <td class="text-end">${{ number_format($pago->monto_pagado, 2) }}</td>
                                            <td>
                                                @if($pago->estado == \App\Models\Pago::ESTADO_COMPLETADO)
                                                    <span class="badge bg-success">Pagado</span>
                                                @elseif($pago->estado == \App\Models\Pago::ESTADO_PENDIENTE)
                                                    <span class="badge bg-warning">Pendiente</span>
                                                @elseif($pago->estado == \App\Models\Pago::ESTADO_CANCELADO)
                                                    <span class="badge bg-danger">Cancelado</span>
                                                @else
                                                    <span class="badge bg-info">Reembolsado</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="ri ri-file-search-line ri-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No se encontraron pagos registrados</p>
                        </div>
                    @endif
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="cerrarModal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Script para controlar la modal -->
@script
<script>
    $wire.on('mostrarModal', () => {
        const modal = new bootstrap.Modal(document.getElementById('detalleDeudaModal'));
        modal.show();
    });

    document.addEventListener('livewire:init', function () {
        const modalElement = document.getElementById('detalleDeudaModal');
        modalElement.addEventListener('hidden.bs.modal', function () {
            $wire.cerrarModal();
        });
    });
</script>
@endscript

<!-- Mostrar la modal si mostrarModal es true -->
@if($mostrarModal)
    @script
    <script>
        document.addEventListener('livewire:init', function () {
            const modal = new bootstrap.Modal(document.getElementById('detalleDeudaModal'));
            modal.show();
        });
    </script>
    @endscript
@endif
</div>
