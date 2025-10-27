<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="ri ri-user-3-line me-2"></i>Monitoreo de Estudiantes</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <div class="avatar-initial bg-label-primary rounded-3">
                                <i class="ri ri-user-line ri-26px"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ number_format($stats['total']) }}</h5>
                            <small class="text-muted">Total Estudiantes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <div class="avatar-initial bg-label-success rounded-3">
                                <i class="ri ri-user-smile-line ri-26px"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ number_format($stats['activos']) }}</h5>
                            <small class="text-muted">Activos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <div class="avatar-initial bg-label-danger rounded-3">
                                <i class="ri ri-user-unfollow-line ri-26px"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ number_format($stats['inactivos']) }}</h5>
                            <small class="text-muted">Inactivos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Por Nivel Educativo</h5>
                </div>
                <div class="card-body">
                    @foreach($byLevel as $level)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>{{ $level->nivel }}</span>
                        <strong>{{ $level->count }}</strong>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Por Grado</h5>
                </div>
                <div class="card-body">
                    @foreach($byGrade as $grade)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>{{ $grade->grado }}</span>
                        <strong>{{ $grade->count }}</strong>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Por Sección</h5>
                </div>
                <div class="card-body">
                    @foreach($bySection as $section)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Sección {{ $section->seccion }}</span>
                        <strong>{{ $section->count }}</strong>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Últimos Registros</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Estudiante</th>
                                    <th>Grado</th>
                                    <th>Sección</th>
                                    <th>Fecha Registro</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent as $student)
                                <tr>
                                    <td><code>{{ $student->codigo }}</code></td>
                                    <td>{{ $student->nombres }} {{ $student->apellidos }}</td>
                                    <td>{{ $student->grado }}</td>
                                    <td>{{ $student->seccion }}</td>
                                    <td>{{ $student->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($student->status)
                                        <span class="badge bg-label-success">Activo</span>
                                        @else
                                        <span class="badge bg-label-danger">Inactivo</span>
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
