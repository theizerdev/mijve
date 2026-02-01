<div>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">{{ $certificate->type_label }}</h5>
                        <small class="text-muted">Número: <code>{{ $certificate->certificate_number }}</code></small>
                    </div>
                    <div>
                        @if($certificate->status === 'active')
                            <span class="badge bg-success fs-6">Activo</span>
                        @elseif($certificate->status === 'revoked')
                            <span class="badge bg-danger fs-6">Revocado</span>
                        @else
                            <span class="badge bg-warning fs-6">Expirado</span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <!-- Contenido del certificado -->
                    <div class="certificate-preview p-4 border rounded bg-light">
                        <div class="text-center mb-4">
                            <h4 class="mb-1">{{ $certificate->empresa->razon_social ?? 'Institución Educativa' }}</h4>
                            <p class="text-muted mb-0">{{ $certificate->sucursal->direccion ?? '' }}</p>
                        </div>

                        <div class="text-center my-4">
                            <h3 class="text-primary">{{ strtoupper($certificate->type_label) }}</h3>
                        </div>

                        <div class="mb-4">
                            <p class="fs-5">
                                Se hace constar que <strong>{{ $certificate->student->nombres }} {{ $certificate->student->apellidos }}</strong>, 
                                identificado(a) con código <strong>{{ $certificate->student->codigo }}</strong>,
                                @if($certificate->certificate_type === 'enrollment')
                                    se encuentra debidamente inscrito(a) en esta institución para el período escolar 
                                    <strong>{{ $certificate->schoolPeriod->name ?? '' }}</strong>.
                                @elseif($certificate->certificate_type === 'attendance')
                                    ha asistido regularmente a clases con un porcentaje de asistencia del 
                                    <strong>{{ $certificate->attendance_percentage }}%</strong> durante el período escolar 
                                    <strong>{{ $certificate->schoolPeriod->name ?? '' }}</strong>.
                                @elseif($certificate->certificate_type === 'academic')
                                    ha obtenido un promedio general de <strong>{{ number_format($certificate->overall_average, 2) }}</strong> puntos,
                                    habiendo cursado <strong>{{ $certificate->total_subjects }}</strong> materia(s) y aprobado 
                                    <strong>{{ $certificate->approved_subjects }}</strong> durante el período escolar 
                                    <strong>{{ $certificate->schoolPeriod->name ?? '' }}</strong>.
                                @elseif($certificate->certificate_type === 'completion')
                                    ha culminado satisfactoriamente sus estudios correspondientes al período escolar 
                                    <strong>{{ $certificate->schoolPeriod->name ?? '' }}</strong>.
                                @elseif($certificate->certificate_type === 'conduct')
                                    ha mantenido una conducta <strong>{{ $certificate->conduct_grade ?? 'satisfactoria' }}</strong> 
                                    durante el período escolar <strong>{{ $certificate->schoolPeriod->name ?? '' }}</strong>.
                                @endif
                            </p>
                        </div>

                        @if($certificate->observations)
                            <div class="mb-4">
                                <p><strong>Observaciones:</strong> {{ $certificate->observations }}</p>
                            </div>
                        @endif

                        <div class="row mt-5">
                            <div class="col-6">
                                <p class="mb-1">Fecha de emisión:</p>
                                <strong>{{ $certificate->issue_date ? $certificate->issue_date->format('d/m/Y') : now()->format('d/m/Y') }}</strong>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-1">Código de verificación:</p>
                                <code>{{ $certificate->verification_code }}</code>
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <div class="border-top pt-2 d-inline-block" style="width: 250px;">
                                <p class="mb-0">{{ $certificate->issued_by ?? 'Autoridad Competente' }}</p>
                                <small class="text-muted">Firma y Sello</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <a href="{{ route('admin.certificates.index') }}" class="btn btn-secondary me-2">
                        <i class="ri ri-arrow-left-line me-1"></i> Volver
                    </a>
                    <button type="button" class="btn btn-primary" wire:click="downloadPdf">
                        <i class="ri ri-download-line me-1"></i> Descargar PDF
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Información del Documento</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Número:</td>
                            <td><code>{{ $certificate->certificate_number }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipo:</td>
                            <td>{{ $certificate->type_label }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Estado:</td>
                            <td>
                                <span class="badge bg-{{ $certificate->status === 'active' ? 'success' : 'danger' }}">
                                    {{ $certificate->status_label }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Emitido:</td>
                            <td>{{ $certificate->issue_date ? $certificate->issue_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Por:</td>
                            <td>{{ $certificate->issued_by ?? '-' }}</td>
                        </tr>
                        @if($certificate->expiration_date)
                            <tr>
                                <td class="text-muted">Expira:</td>
                                <td>{{ $certificate->expiration_date->format('d/m/Y') }}</td>
                            </tr>
                        @endif
                        @if($certificate->revocation_date)
                            <tr>
                                <td class="text-muted">Revocado:</td>
                                <td>{{ $certificate->revocation_date->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Razón:</td>
                                <td>{{ $certificate->revocation_reason }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($certificate->academic_data)
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Datos Académicos</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted">Promedio:</td>
                                <td><strong>{{ number_format($certificate->overall_average ?? 0, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Materias:</td>
                                <td>{{ $certificate->total_subjects ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Aprobadas:</td>
                                <td>{{ $certificate->approved_subjects ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Asistencia:</td>
                                <td>{{ $certificate->attendance_percentage ?? 0 }}%</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
