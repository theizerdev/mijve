<div>
    <!-- Información del Plan de Estudio -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Información del Plan de Estudio</h5>
                    <div>
                        @can('edit study_plans')
                        <a href="{{ route('admin.study-plans.edit', $studyPlan) }}" class="btn btn-outline-primary me-2">
                            <i class="ri ri-pencil-line me-1"></i>Editar
                        </a>
                        @endcan
                        <a href="{{ route('admin.study-plans.index') }}" class="btn btn-outline-secondary">
                            <i class="ri ri-arrow-left-line me-1"></i>Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Código</label>
                            <div class="fw-semibold">{{ $studyPlan->code }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Nombre</label>
                            <div class="fw-semibold">{{ $studyPlan->name }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Programa</label>
                            <div class="fw-semibold">{{ $studyPlan->program->nombre }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Nivel Educativo</label>
                            <div class="fw-semibold">{{ $studyPlan->educationalLevel->nombre }}</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Créditos Totales</label>
                            <div class="fw-semibold">{{ $studyPlan->total_credits ?? 0 }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Horas Totales</label>
                            <div class="fw-semibold">{{ $studyPlan->total_hours ?? 0 }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Duración</label>
                            <div class="fw-semibold">
                                @if($studyPlan->duration_years)
                                    {{ $studyPlan->duration_years }} año(s)
                                @endif
                                @if($studyPlan->duration_semesters)
                                    {{ $studyPlan->duration_semesters }} semestre(s)
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Estado</label>
                            <div>
                                <span class="badge bg-{{ $studyPlan->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ $studyPlan->status === 'active' ? 'Activo' : 'Inactivo' }}
                                </span>
                                @if($studyPlan->is_default)
                                    <span class="badge bg-info ms-1">
                                        <i class="ri ri-star-line"></i> Por Defecto
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($studyPlan->description)
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">Descripción</label>
                            <div class="fw-semibold">{{ $studyPlan->description }}</div>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Fecha Efectiva</label>
                            <div class="fw-semibold">{{ $studyPlan->effective_date ? $studyPlan->effective_date->format('d/m/Y') : 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Fecha de Expiración</label>
                            <div class="fw-semibold">{{ $studyPlan->expiration_date ? $studyPlan->expiration_date->format('d/m/Y') : 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Creado por</label>
                            <div class="fw-semibold">{{ $studyPlan->createdBy->name ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $studyPlan->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Actualizado por</label>
                            <div class="fw-semibold">{{ $studyPlan->updatedBy->name ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $studyPlan->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestión de Materias -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Materias del Plan de Estudio</h5>
                    <div>
                        <div class="form-check form-switch d-inline-block me-3">
                            <input class="form-check-input" type="checkbox" id="showInactive" wire:model="showInactive">
                            <label class="form-check-label" for="showInactive">Mostrar inactivas</label>
                        </div>
                        @can('edit study_plans')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                            <i class="ri ri-add-line me-1"></i>Agregar Materia
                        </button>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    @if($subjects->count() > 0)
                        @foreach($groupedSubjects as $year => $semesters)
                            <div class="mb-4">
                                <h6 class="text-primary mb-3">Año {{ $year }}</h6>
                                @foreach($semesters as $semester => $subjects)
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-2">Semestre {{ $semester }}</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Orden</th>
                                                        <th>Código</th>
                                                        <th>Nombre</th>
                                                        <th>Tipo</th>
                                                        <th>Créditos</th>
                                                        <th>Horas</th>
                                                        <th>Estado</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($subjects as $subject)
                                                    <tr>
                                                        <td>{{ $subject->pivot->order }}</td>
                                                        <td>{{ $subject->code }}</td>
                                                        <td>
                                                            <div>
                                                                <div class="fw-semibold">{{ $subject->name }}</div>
                                                                @if($subject->description)
                                                                    <small class="text-muted">{{ Str::limit($subject->description, 50) }}</small>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $subject->pivot->subject_type === 'mandatory' ? 'primary' : 'info' }}">
                                                                {{ $subject->pivot->subject_type === 'mandatory' ? 'Obligatoria' : 'Electiva' }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $subject->credits ?? 0 }}</td>
                                                        <td>{{ $subject->hours_per_week ?? 0 }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $subject->pivot->is_active ? 'success' : 'secondary' }}">
                                                                {{ $subject->pivot->is_active ? 'Activa' : 'Inactiva' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                                    <i class="ri ri-more-2-line"></i>
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    @can('edit study_plans')
                                                                    <button class="dropdown-item" wire:click="toggleSubjectStatus({{ $subject->id }})">
                                                                        <i class="ri ri-toggle-line me-1"></i>
                                                                        {{ $subject->pivot->is_active ? 'Desactivar' : 'Activar' }}
                                                                    </button>
                                                                    <button class="dropdown-item" wire:click="removeSubject({{ $subject->id }})"
                                                                            onclick="confirm('¿Está seguro de remover esta materia del plan?') || event.stopImmediatePropagation()">
                                                                        <i class="ri ri-delete-bin-line me-1"></i>Remover
                                                                    </button>
                                                                    @endcan
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="ri ri-file-list-3-line ri-2x text-muted mb-2"></i>
                            <p class="text-muted">No hay materias asignadas a este plan de estudio</p>
                            @can('edit study_plans')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                                <i class="ri ri-add-line me-1"></i>Agregar Primera Materia
                            </button>
                            @endcan
                        </div>
                    @endif
                </div>
                @if($subjects->count() > 0)
                <div class="card-footer">
                    {{ $subjects->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal para agregar materia -->
    @can('edit study_plans')
    <div wire:ignore.self class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSubjectModalLabel">Agregar Materia al Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="addSubject">
                        <div class="mb-3">
                            <label for="selectedSubject" class="form-label">Materia <span class="text-danger">*</span></label>
                            <select class="form-select @error('selectedSubject') is-invalid @enderror" 
                                    id="selectedSubject" wire:model="selectedSubject" required>
                                <option value="">Seleccione una materia</option>
                                @foreach($availableSubjects as $subject)
                                    <option value="{{ $subject->id }}">
                                        {{ $subject->code }} - {{ $subject->name }}
                                        @if($subject->credits)
                                            ({{ $subject->credits }} créditos)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedSubject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="year" class="form-label">Año <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('year') is-invalid @enderror" 
                                       id="year" wire:model="year" min="1" required>
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="semester" class="form-label">Semestre <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('semester') is-invalid @enderror" 
                                       id="semester" wire:model="semester" min="1" required>
                                @error('semester')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="subject_type" class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select class="form-select @error('subject_type') is-invalid @enderror" 
                                        id="subject_type" wire:model="subject_type" required>
                                    <option value="mandatory">Obligatoria</option>
                                    <option value="elective">Electiva</option>
                                </select>
                                @error('subject_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="order" class="form-label">Orden <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                       id="order" wire:model="order" min="1" required>
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" wire:click="addSubject" data-bs-dismiss="modal">
                        <i class="ri ri-add-line me-1"></i>Agregar Materia
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endcan
</div>