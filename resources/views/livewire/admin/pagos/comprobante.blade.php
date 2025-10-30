<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Comprobante de Pago</h4>
            <p class="text-muted mb-0">N° {{ $comprobante->numero }}</p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary me-2">
                <i class="ri ri-printer-line me-1"></i> Imprimir
            </button>
            <a href="{{ route('admin.pagos.index') }}" class="btn btn-secondary">
                <i class="ri ri-arrow-left-line me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Detalles del Comprobante</h5>
                <span class="badge bg-{{ $comprobante->comprobanteable->estado == 'completado' ? 'success' : 'warning' }}">
                    {{ \App\Models\Pago::getEstados()[$comprobante->comprobanteable->estado] }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Información del Pago</h6>
                    <p class="mb-1"><strong>Fecha:</strong> {{ $comprobante->fecha_emision->format('d/m/Y H:i') }}</p>
                    <p class="mb-1"><strong>Método:</strong> {{ ucfirst($comprobante->comprobanteable->metodo_pago) }}</p>
                    <p class="mb-1"><strong>Referencia:</strong> {{ $comprobante->comprobanteable->referencia ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Registrado por:</strong> {{ $comprobante->comprobanteable->user->name }}</p>
                </div>
                <div class="col-md-6">
                    <h6>Información del Estudiante</h6>
                    <p class="mb-1"><strong>Nombre:</strong> {{ $comprobante->comprobanteable->matricula->student->nombres }} {{ $comprobante->comprobanteable->matricula->student->apellidos }}</p>
                    <p class="mb-1"><strong>Documento:</strong> {{ $comprobante->comprobanteable->matricula->student->documento_identidad }}</p>
                    <p class="mb-1"><strong>Programa:</strong> {{ $comprobante->comprobanteable->matricula->programa->nombre }}</p>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th class="text-end">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                {{ $comprobante->comprobanteable->conceptoPago->nombre }}
                                @if($comprobante->comprobanteable->estado == 'pendiente')
                                    <span class="badge bg-warning">Parcial</span>
                                @endif
                            </td>
                            <td class="text-end">
                                ${{ number_format($comprobante->comprobanteable->monto, 2) }}
                                @if($comprobante->comprobanteable->estado == 'pendiente')
                                    <div class="text-success">
                                        <small>Pagado: ${{ number_format($comprobante->comprobanteable->monto_pagado, 2) }}</small>
                                    </div>
                                    <div class="text-danger">
                                        <small>Pendiente: ${{ number_format($comprobante->comprobanteable->monto - $comprobante->comprobanteable->monto_pagado, 2) }}</small>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        <tr class="table-light">
                            <td class="fw-bold">Total</td>
                            <td class="text-end fw-bold">${{ number_format($comprobante->comprobanteable->monto, 2) }}</td>
                        </tr>
                        @if($comprobante->comprobanteable->estado == 'pendiente')
                        <tr class="table-light">
                            <td class="fw-bold">Total Pagado</td>
                            <td class="text-end fw-bold text-success">${{ number_format($comprobante->comprobanteable->monto_pagado, 2) }}</td>
                        </tr>
                        <tr class="table-light">
                            <td class="fw-bold">Saldo Pendiente</td>
                            <td class="text-end fw-bold text-danger">${{ number_format($comprobante->comprobanteable->monto - $comprobante->comprobanteable->monto_pagado, 2) }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="border-top pt-3">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Firma Autorizada</h6>
                        <div class="mt-4 pt-2">
                            <p class="border-top pt-2 mb-0">__________________________</p>
                            <small class="text-muted">Nombre y Firma</small>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6>Sello Institucional</h6>
                        <div class="mt-4">
                            <img src="{{ asset('materialize/assets/img/branding/logo.png') }}" alt="Logo" style="height: 80px; opacity: 0.7;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .card, .card * {
                visibility: visible;
            }
            .card {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: none;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</div>
