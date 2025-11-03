<div wire:poll.15s="$dispatch('refresh-estudiantes')">
    <!-- Header profesional -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-success text-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <div class="avatar-initial bg-white bg-opacity-20 rounded">
                                    <i class="ri ri-user-3-line ri-20px text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="mb-0 text-white">Monitoreo de Estudiantes</h4>
                                <small class="text-white-50">Estadísticas y distribución académica</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm text-white me-2" role="status" style="width: 12px; height: 12px;">
                                    <span class="visually-hidden">Actualizando...</span>
                                </div>
                                <small class="text-white-75">{{ $lastUpdate }}</small>
                            </div>
                            <div class="d-flex align-items-center mt-1">
                                <span class="badge bg-white bg-opacity-20 text-white border border-white border-opacity-20">
                                    <i class="ri ri-graduation-cap-line me-1"></i>{{ number_format($stats['total']) }} Estudiantes
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas principales mejoradas -->
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="avatar">
                            <div class="avatar-initial bg-primary bg-opacity-10 text-primary rounded">
                                <i class="ri ri-user-line ri-24px"></i>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ri ri-more-2-line"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i class="ri ri-user-add-line me-2"></i>Nuevo estudiante</a></li>
                                <li><a class="dropdown-item" href="#"><i class="ri ri-file-list-line me-2"></i>Ver todos</a></li>
                            </ul>
                        </div>
                    </div>
                    <div>
                        <h3 class="mb-1 text-primary">{{ number_format($stats['total']) }}</h3>
                        <p class="text-muted mb-3">Total de Estudiantes</p>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: 100%"></div>
                            </div>
                            <small class="text-success fw-medium">100%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="avatar">
                            <div class="avatar-initial bg-success bg-opacity-10 text-success rounded">
                                <i class="ri ri-user-smile-line ri-24px"></i>
                            </div>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success">
                            {{ $stats['total'] > 0 ? round(($stats['activos'] / $stats['total']) * 100, 1) : 0 }}%
                        </span>
                    </div>
                    <div>
                        <h3 class="mb-1 text-success">{{ number_format($stats['activos']) }}</h3>
                        <p class="text-muted mb-3">Estudiantes Activos</p>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ $stats['total'] > 0 ? ($stats['activos'] / $stats['total']) * 100 : 0 }}%"></div>
                            </div>
                            <small class="text-success fw-medium">Activo</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="avatar">
                            <div class="avatar-initial bg-danger bg-opacity-10 text-danger rounded">
                                <i class="ri ri-user-unfollow-line ri-24px"></i>
                            </div>
                        </div>
                        <span class="badge bg-danger bg-opacity-10 text-danger">
                            {{ $stats['total'] > 0 ? round(($stats['inactivos'] / $stats['total']) * 100, 1) : 0 }}%
                        </span>
                    </div>
                    <div>
                        <h3 class="mb-1 text-danger">{{ number_format($stats['inactivos']) }}</h3>
                        <p class="text-muted mb-3">Estudiantes Inactivos</p>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                <div class="progress-bar bg-danger" style="width: {{ $stats['total'] > 0 ? ($stats['inactivos'] / $stats['total']) * 100 : 0 }}%"></div>
                            </div>
                            <small class="text-danger fw-medium">Inactivo</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribución académica mejorada -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-0">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <div class="avatar-initial bg-primary bg-opacity-10 text-primary rounded">
                                <i class="ri ri-graduation-cap-line ri-18px"></i>
                            </div>
                        </div>
                        <h5 class="mb-0">Por Nivel Educativo</h5>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($byLevel as $index => $level)
                    <div class="d-flex align-items-center justify-content-between p-3 mb-2 {{ $index % 2 == 0 ? 'bg-light' : '' }} rounded">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xs me-2">
                                <div class="avatar-initial bg-primary bg-opacity-10 text-primary rounded">
                                    <i class="ri ri-book-line ri-12px"></i>
                                </div>
                            </div>
                            <span class="fw-medium">{{ $level->nivel }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary me-2">{{ $level->count }}</span>
                            <div class="progress" style="width: 40px; height: 4px;">
                                <div class="progress-bar bg-primary" style="width: {{ $stats['total'] > 0 ? ($level->count / $stats['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-0">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <div class="avatar-initial bg-success bg-opacity-10 text-success rounded">
                                <i class="ri ri-numbers-line ri-18px"></i>
                            </div>
                        </div>
                        <h5 class="mb-0">Por Grado</h5>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($byGrade as $index => $grade)
                    <div class="d-flex align-items-center justify-content-between p-3 mb-2 {{ $index % 2 == 0 ? 'bg-light' : '' }} rounded">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xs me-2">
                                <div class="avatar-initial bg-success bg-opacity-10 text-success rounded">
                                    <span class="fw-bold" style="font-size: 10px;">{{ $grade->grado }}</span>
                                </div>
                            </div>
                            <span class="fw-medium">{{ $grade->grado }}° Grado</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success bg-opacity-10 text-success me-2">{{ $grade->count }}</span>
                            <div class="progress" style="width: 40px; height: 4px;">
                                <div class="progress-bar bg-success" style="width: {{ $stats['activos'] > 0 ? ($grade->count / $stats['activos']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-0">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <div class="avatar-initial bg-info bg-opacity-10 text-info rounded">
                                <i class="ri ri-group-line ri-18px"></i>
                            </div>
                        </div>
                        <h5 class="mb-0">Por Sección</h5>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($bySection as $index => $section)
                    <div class="d-flex align-items-center justify-content-between p-3 mb-2 {{ $index % 2 == 0 ? 'bg-light' : '' }} rounded">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xs me-2">
                                <div class="avatar-initial bg-info bg-opacity-10 text-info rounded">
                                    <span class="fw-bold" style="font-size: 10px;">{{ $section->seccion }}</span>
                                </div>
                            </div>
                            <span class="fw-medium">Sección {{ $section->seccion }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-info bg-opacity-10 text-info me-2">{{ $section->count }}</span>
                            <div class="progress" style="width: 40px; height: 4px;">
                                <div class="progress-bar bg-info" style="width: {{ $stats['activos'] > 0 ? ($section->count / $stats['activos']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de últimos registros mejorada -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <div class="avatar-initial bg-secondary bg-opacity-10 text-secondary rounded">
                                    <i class="ri ri-user-add-line ri-18px"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-0">Últimos Registros</h5>
                                <small class="text-muted">Estudiantes registrados recientemente</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="ri ri-refresh-line me-1"></i>Actualizar
                            </button>
                            <button class="btn btn-sm btn-outline-success">
                                <i class="ri ri-user-add-line me-1"></i>Nuevo
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 ps-4">
                                        <div class="d-flex align-items-center">
                                            <i class="ri ri-hashtag text-muted me-2"></i>
                                            Código
                                        </div>
                                    </th>
                                    <th class="border-0">
                                        <div class="d-flex align-items-center">
                                            <i class="ri ri-user-line text-muted me-2"></i>
                                            Estudiante
                                        </div>
                                    </th>
                                    <th class="border-0">
                                        <div class="d-flex align-items-center">
                                            <i class="ri ri-graduation-cap-line text-muted me-2"></i>
                                            Grado
                                        </div>
                                    </th>
                                    <th class="border-0">
                                        <div class="d-flex align-items-center">
                                            <i class="ri ri-group-line text-muted me-2"></i>
                                            Sección
                                        </div>
                                    </th>
                                    <th class="border-0">
                                        <div class="d-flex align-items-center">
                                            <i class="ri ri-calendar-line text-muted me-2"></i>
                                            Fecha Registro
                                        </div>
                                    </th>
                                    <th class="border-0 pe-4">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent as $index => $student)
                                <tr class="{{ $index % 2 == 0 ? 'bg-light bg-opacity-50' : '' }}">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-2">
                                                <div class="avatar-initial bg-primary bg-opacity-10 text-primary rounded">
                                                    <i class="ri ri-user-line ri-12px"></i>
                                                </div>
                                            </div>
                                            <code class="text-primary fw-medium">{{ $student->codigo }}</code>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <div class="avatar-initial bg-success bg-opacity-10 text-success rounded">
                                                    {{ substr($student->nombres, 0, 1) }}{{ substr($student->apellidos, 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $student->nombres }} {{ $student->apellidos }}</h6>
                                                <small class="text-muted">Estudiante</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            <i class="ri ri-numbers-line me-1"></i>{{ $student->grado }}°
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning bg-opacity-10 text-warning">
                                            <i class="ri ri-group-line me-1"></i>{{ $student->seccion }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="ri ri-time-line text-muted me-2"></i>
                                            <div>
                                                <div class="fw-medium">{{ $student->created_at->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $student->created_at->format('H:i') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="pe-4">
                                        @if($student->status)
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="ri ri-checkbox-circle-line me-1"></i>Activo
                                        </span>
                                        @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger">
                                            <i class="ri ri-close-circle-line me-1"></i>Inactivo
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
