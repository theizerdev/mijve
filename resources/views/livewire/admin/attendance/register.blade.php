<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Registrar Asistencia Diaria</h5>
            <small class="text-muted">Seleccione la sección y fecha para registrar la asistencia</small>
        </div>

        <div class="card-body">
            <!-- Selectores -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Período Escolar <span class="text-danger">*</span></label>
                    <select class="form-select @error('school_period_id') is-invalid @enderror" wire:model.live="school_period_id">
                        <option value="">Seleccione...</option>
                        @foreach($schoolPeriods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                        @endforeach
                    </select>
                    @error('school_period_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sección <span class="text-danger">*</span></label>
                    <select class="form-select @error('section_id') is-invalid @enderror" wire:model.live="section_id">
                        <option value="">Seleccione...</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->nombre }} ({{ $section->nivelEducativo->nombre ?? '' }})</option>
                        @endforeach
                    </select>
                    @error('section_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror" wire:model.live="date">
                    @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            @if(count($students) > 0)
                <!-- Acción masiva -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <select class="form-select" wire:model="bulkStatus">
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary" wire:click="applyBulkStatus">
                                <i class="ri ri-checkbox-multiple-line me-1"></i> Aplicar a Todos
                            </button>
                        </div>
                    </div>
                    <div class="col-md-8 text-end">
                        <span class="badge bg-primary fs-6">{{ count($students) }} estudiantes</span>
                    </div>
                </div>

                <!-- Lista de estudiantes -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px">#</th>
                                <th style="width: 100px">Código</th>
                                <th>Estudiante</th>
                                <th style="width: 180px">Estado</th>
                                <th style="width: 120px">Hora Llegada</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><code>{{ $student->codigo }}</code></td>
                                    <td>
                                        <strong>{{ $student->apellidos }}</strong>, {{ $student->nombres }}
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm" 
                                                wire:model="attendanceData.{{ $student->id }}.status">
                                            @foreach($statuses as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="time" class="form-control form-control-sm" 
                                               wire:model="attendanceData.{{ $student->id }}.arrival_time">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" 
                                               wire:model="attendanceData.{{ $student->id }}.observations"
                                               placeholder="Observación...">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Botones -->
                <div class="mt-4 text-end">
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary me-2">
                        <i class="ri ri-arrow-left-line me-1"></i> Cancelar
                    </a>
                    <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">
                            <i class="ri ri-save-line me-1"></i> Guardar Asistencia
                        </span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-1"></span> Guardando...
                        </span>
                    </button>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="ri ri-group-line ri-64px text-muted"></i>
                    <p class="text-muted mt-3">Seleccione una sección para cargar los estudiantes</p>
                </div>
            @endif
        </div>
    </div>
</div>
