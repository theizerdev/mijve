<div>
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Generar Acta de Notas</h5>
                    <small class="text-muted">Configure los parámetros del acta</small>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Período Escolar <span class="text-danger">*</span></label>
                        <select class="form-select @error('school_period_id') is-invalid @enderror" wire:model.live="school_period_id">
                            <option value="">Seleccione...</option>
                            @foreach($schoolPeriods as $period)
                                <option value="{{ $period->id }}">{{ $period->name }}</option>
                            @endforeach
                        </select>
                        @error('school_period_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sección <span class="text-danger">*</span></label>
                        <select class="form-select @error('section_id') is-invalid @enderror" wire:model.live="section_id">
                            <option value="">Seleccione...</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->nombre }} ({{ $section->nivelEducativo->nombre ?? '' }})</option>
                            @endforeach
                        </select>
                        @error('section_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Materia (Opcional)</label>
                        <select class="form-select" wire:model.live="subject_id">
                            <option value="">Todas las materias</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lapso (Opcional)</label>
                        <select class="form-select" wire:model.live="evaluation_period_id">
                            <option value="">Todos los lapsos</option>
                            @foreach($evaluationPeriods as $ep)
                                <option value="{{ $ep->id }}">{{ $ep->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo de Acta <span class="text-danger">*</span></label>
                        <select class="form-select @error('report_type') is-invalid @enderror" wire:model="report_type">
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('report_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title" placeholder="Se genera automáticamente...">
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" wire:model="observations" rows="2"></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" wire:click="generatePreview" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="generatePreview">
                                <i class="ri ri-eye-line me-1"></i> Vista Previa
                            </span>
                            <span wire:loading wire:target="generatePreview">
                                <span class="spinner-border spinner-border-sm me-1"></span> Cargando...
                            </span>
                        </button>
                        @if($previewData)
                            <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="ri ri-save-line me-1"></i> Generar Acta
                                </span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Guardando...
                                </span>
                            </button>
                        @endif
                        <a href="{{ route('admin.grade-reports.index') }}" class="btn btn-secondary">
                            <i class="ri ri-arrow-left-line me-1"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            @if($previewData)
                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ $previewData['statistics']['total_students'] }}</h3>
                                <small>Estudiantes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ $previewData['statistics']['approved_count'] }}</h3>
                                <small>Aprobados</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ $previewData['statistics']['failed_count'] }}</h3>
                                <small>Reprobados</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ $previewData['statistics']['average_grade'] }}</h3>
                                <small>Promedio</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de calificaciones -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Vista Previa de Calificaciones</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Estudiante</th>
                                    <th class="text-center">Promedio</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($previewData['students'] as $index => $studentData)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><code>{{ $studentData['codigo'] }}</code></td>
                                        <td>{{ $studentData['apellidos'] }}, {{ $studentData['nombres'] }}</td>
                                        <td class="text-center">
                                            <strong class="fs-5">{{ number_format($studentData['average'], 2) }}</strong>
                                        </td>
                                        <td class="text-center">
                                            @if($studentData['status'] === 'approved')
                                                <span class="badge bg-success">Aprobado</span>
                                            @else
                                                <span class="badge bg-danger">Reprobado</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <div class="row text-center">
                            <div class="col">
                                <small class="text-muted">Nota Más Alta:</small>
                                <strong class="text-success ms-1">{{ $previewData['statistics']['highest_grade'] }}</strong>
                            </div>
                            <div class="col">
                                <small class="text-muted">Nota Más Baja:</small>
                                <strong class="text-danger ms-1">{{ $previewData['statistics']['lowest_grade'] }}</strong>
                            </div>
                            <div class="col">
                                <small class="text-muted">Tasa de Aprobación:</small>
                                <strong class="text-primary ms-1">{{ $previewData['statistics']['approval_rate'] }}%</strong>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="ri ri-file-chart-line ri-64px text-muted"></i>
                        <h5 class="mt-3 text-muted">Configure los parámetros y haga clic en "Vista Previa"</h5>
                        <p class="text-muted">Se mostrarán las calificaciones de los estudiantes según los filtros seleccionados</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
