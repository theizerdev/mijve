<div>
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Generar Constancia / Certificado</h5>
            <small class="text-muted">Complete los datos para generar el documento</small>
        </div>

        <div class="card-body">
            <div class="row g-4">
                <!-- Selección de estudiante -->
                <div class="col-md-6">
                    <label class="form-label">Estudiante <span class="text-danger">*</span></label>
                    <select class="form-select @error('student_id') is-invalid @enderror" wire:model.live="student_id">
                        <option value="">Seleccione un estudiante...</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ $s->codigo }} - {{ $s->apellidos }}, {{ $s->nombres }}</option>
                        @endforeach
                    </select>
                    @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Tipo de certificado -->
                <div class="col-md-6">
                    <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                    <select class="form-select @error('certificate_type') is-invalid @enderror" wire:model.live="certificate_type">
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('certificate_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Período escolar -->
                <div class="col-md-6">
                    <label class="form-label">Período Escolar <span class="text-danger">*</span></label>
                    <select class="form-select @error('school_period_id') is-invalid @enderror" wire:model.live="school_period_id">
                        <option value="">Seleccione...</option>
                        @foreach($schoolPeriods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                        @endforeach
                    </select>
                    @error('school_period_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Matrícula (opcional) -->
                <div class="col-md-6">
                    <label class="form-label">Matrícula (Opcional)</label>
                    <select class="form-select" wire:model.live="matricula_id">
                        <option value="">Sin matrícula específica</option>
                        @foreach($matriculas as $m)
                            <option value="{{ $m->id }}">
                                {{ $m->programa->nombre ?? 'Sin programa' }} - 
                                {{ $m->schoolPeriod->name ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Observaciones -->
                <div class="col-12">
                    <label class="form-label">Observaciones</label>
                    <textarea class="form-control" wire:model="observations" rows="2" placeholder="Observaciones adicionales..."></textarea>
                </div>
            </div>

            <!-- Info del estudiante seleccionado -->
            @if($student)
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Información del Estudiante</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted">Código:</td>
                                        <td><strong>{{ $student->codigo }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Nombre:</td>
                                        <td>{{ $student->nombres }} {{ $student->apellidos }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Nivel:</td>
                                        <td>{{ $student->nivelEducativo->nombre ?? 'No asignado' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($academicData)
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Datos Académicos</h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td class="text-muted">Promedio General:</td>
                                            <td><strong>{{ number_format($academicData['overall_average'], 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Materias Cursadas:</td>
                                            <td>{{ $academicData['total_subjects'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Materias Aprobadas:</td>
                                            <td>{{ $academicData['approved_subjects'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Asistencia:</td>
                                            <td>{{ $academicData['attendance_percentage'] }}%</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="card-footer text-end">
            <a href="{{ route('admin.certificates.index') }}" class="btn btn-secondary me-2">
                <i class="ri ri-arrow-left-line me-1"></i> Cancelar
            </a>
            <button type="button" class="btn btn-primary" wire:click="generate" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="generate">
                    <i class="ri ri-file-add-line me-1"></i> Generar Certificado
                </span>
                <span wire:loading wire:target="generate">
                    <span class="spinner-border spinner-border-sm me-1"></span> Generando...
                </span>
            </button>
        </div>
    </div>
</div>
