<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Detalles de la Sección: {{ $section->name }}</h4>
        <div>
            <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary me-2">
                <i class="ri ri-arrow-left-line me-1"></i> Volver
            </a>
            @can('edit sections')
            <a href="{{ route('admin.sections.edit', $section->id) }}" class="btn btn-primary">
                <i class="ri ri-edit-line me-1"></i> Editar
            </a>
            @endcan
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información General</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Código</label>
                            <p class="form-control-plaintext">{{ $section->code }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nombre</label>
                            <p class="form-control-plaintext">{{ $section->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Período Escolar</label>
                            <p class="form-control-plaintext">{{ $section->schoolPeriod->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Materia</label>
                            <p class="form-control-plaintext">{{ $section->subject->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Profesor</label>
                            <p class="form-control-plaintext">{{ $section->teacher->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Aula</label>
                            <p class="form-control-plaintext">{{ $section->classroom->name }} - {{ $section->classroom->code }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Capacidad</label>
                            <p class="form-control-plaintext">{{ $section->capacity }} estudiantes</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Estado</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $section->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ $section->status === 'active' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </p>
                        </div>
                        @if($section->description)
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <p class="form-control-plaintext">{{ $section->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Horarios -->
            @if($section->schedules->count() > 0)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Horarios</h5>
                    <span class="badge bg-primary">{{ $section->schedules->count() }} horarios</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Día</th>
                                    <th>Hora Inicio</th>
                                    <th>Hora Fin</th>
                                    <th>Duración</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($section->schedules as $schedule)
                                <tr>
                                    <td>{{ ucfirst($schedule->day_of_week) }}</td>
                                    <td>{{ $schedule->start_time }}</td>
                                    <td>{{ $schedule->end_time }}</td>
                                    <td>{{ $schedule->duration }} minutos</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Estudiantes Inscritos -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Estudiantes Inscritos</h5>
                    <span class="badge bg-info">{{ $section->enrollments()->count() }} estudiantes</span>
                </div>
                <div class="card-body">
                    @if($section->enrollments()->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Estudiante</th>
                                    <th>Representante</th>
                                    <th>Fecha de Inscripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($section->enrollments as $enrollment)
                                <tr>
                                    <td>{{ $enrollment->student->id }}</td>
                                    <td>{{ $enrollment->student->name }} {{ $enrollment->student->last_name }}</td>
                                    <td>{{ $enrollment->student->representative->name ?? 'N/A' }}</td>
                                    <td>{{ $enrollment->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="ri ri-user-line text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No hay estudiantes inscritos en esta sección</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información de la Empresa</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Empresa</label>
                        <p class="form-control-plaintext">{{ $section->empresa->nombre }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sucursal</label>
                        <p class="form-control-plaintext">{{ $section->sucursal->nombre }}</p>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Estadísticas</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Capacidad Total:</span>
                        <strong>{{ $section->capacity }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Estudiantes Inscritos:</span>
                        <strong>{{ $section->enrollments()->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Cupos Disponibles:</span>
                        <strong>{{ max(0, $section->capacity - $section->enrollments()->count()) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ocupación:</span>
                        <strong>{{ $section->capacity > 0 ? round(($section->enrollments()->count() / $section->capacity) * 100, 1) : 0 }}%</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Horarios:</span>
                        <strong>{{ $section->schedules->count() }}</strong>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Información del Sistema</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Creado por</label>
                        <p class="form-control-plaintext">{{ $section->createdBy->name ?? 'Sistema' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha de Creación</label>
                        <p class="form-control-plaintext">{{ $section->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($section->updated_at != $section->created_at)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Última Actualización</label>
                        <p class="form-control-plaintext">{{ $section->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes Flash -->
    @if(session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
</div>