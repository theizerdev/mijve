<div>
    <div class="row justify-content-center">
        <div class="col-xl-9 col-md-10 col-12">
            <div class="card invoice-preview-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column m-sm-3 m-0">
                        <div class="mb-xl-0 mb-4">
                            <div class="d-flex svg-illustration mb-4 gap-2 align-items-center">
                                <span class="app-brand-logo demo">
                                    <i class="ri-bank-card-fill ri-2x text-primary"></i>
                                </span>
                                <span class="app-brand-text fw-bold fs-4">
                                    {{ $pago->empresa->razon_social ?? config('app.name') }}
                                </span>
                            </div>
                            <p class="mb-2">{{ $pago->empresa->direccion ?? '' }}</p>
                            <p class="mb-2">{{ $pago->empresa->telefono ?? '' }}</p>
                            <p class="mb-0">{{ $pago->empresa->email ?? '' }}</p>
                        </div>
                        <div>
                            <h4 class="fw-semibold mb-2">COMPROBANTE #{{ str_pad($pago->id, 6, '0', STR_PAD_LEFT) }}</h4>
                            <div class="mb-2 pt-1">
                                <span>Fecha Emisión:</span>
                                <span class="fw-semibold">{{ $pago->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="pt-1">
                                <span>Fecha Pago:</span>
                                <span class="fw-semibold">{{ $pago->fecha_pago->format('d/m/Y') }}</span>
                            </div>
                            <div class="pt-1 mt-2">
                                <span>Estado:</span>
                                @if($pago->status === 'Aprobado')
                                    <span class="badge bg-success">Aprobado</span>
                                @elseif($pago->status === 'Pendiente')
                                    <span class="badge bg-warning">Pendiente</span>
                                @else
                                    <span class="badge bg-danger">Rechazado</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-0" />
                <div class="card-body">
                    <div class="row p-sm-3 p-0">
                        <div class="col-xl-6 col-md-12 col-sm-5 col-12 mb-xl-0 mb-md-4 mb-sm-0 mb-4">
                            <h6 class="mb-3">Participante:</h6>
                            <p class="mb-1"><strong>{{ $pago->participante->nombres }} {{ $pago->participante->apellidos }}</strong></p>
                            <p class="mb-1">Cédula: {{ $pago->participante->cedula }}</p>
                            <p class="mb-1">Teléfono: {{ $pago->participante->telefono_principal }}</p>
                        </div>
                        <div class="col-xl-6 col-md-12 col-sm-7 col-12">
                            <h6 class="mb-3">Detalles del Pago:</h6>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="ps-0 py-1">Actividad:</td>
                                        <td class="fw-semibold py-1">{{ $pago->actividad->nombre }}</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-0 py-1">Método:</td>
                                        <td class="fw-semibold py-1">{{ $pago->metodoPago->tipo_pago }}</td>
                                    </tr>
                                    @if($pago->metodoPago->banco)
                                    <tr>
                                        <td class="ps-0 py-1">Banco Destino:</td>
                                        <td class="fw-semibold py-1">{{ $pago->metodoPago->banco }}</td>
                                    </tr>
                                    @endif
                                    @if($pago->referencia_bancaria)
                                    <tr>
                                        <td class="ps-0 py-1">Referencia:</td>
                                        <td class="fw-semibold py-1">{{ $pago->referencia_bancaria }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="table-responsive border-top">
                    <table class="table m-0">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Monto EUR</th>
                                <th>Tasa</th>
                                <th class="text-end">Monto Bs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Pago por actividad: {{ $pago->actividad->nombre }}</td>
                                <td>€ {{ number_format($pago->monto_euro, 2) }}</td>
                                <td>{{ number_format($pago->tasa_cambio, 4) }}</td>
                                <td class="text-end fw-bold">Bs {{ number_format($pago->monto_bolivares, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <span class="fw-semibold">Observaciones:</span>
                            <span>{{ $pago->observaciones ?: 'Sin observaciones' }}</span>
                        </div>
                        @if($pago->evidencia_pago)
                        <div class="col-12 mt-4">
                            <h6 class="fw-semibold">Evidencia de Pago:</h6>
                            <div class="border rounded p-3 text-center">
                                @php
                                    $extension = pathinfo($pago->evidencia_pago, PATHINFO_EXTENSION);
                                @endphp
                                
                                @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ asset('storage/' . $pago->evidencia_pago) }}" class="img-fluid" style="max-height: 300px;" alt="Evidencia">
                                @else
                                    <a href="{{ asset('storage/' . $pago->evidencia_pago) }}" target="_blank" class="btn btn-outline-primary">
                                        <i class="ri-file-download-line me-1"></i> Descargar Evidencia ({{ strtoupper($extension) }})
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="card-footer text-center">
                    <button class="btn btn-primary me-2" onclick="window.print()">
                        <i class="ri-printer-line me-1"></i> Imprimir
                    </button>
                    <a href="{{ route('admin.pagos.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
