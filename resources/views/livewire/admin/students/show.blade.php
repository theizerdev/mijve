<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Detalles del Estudiante</h2>
        <div>
            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary me-2">
                <i class="ri ri-arrow-left-line me-1"></i> Volver
            </a>
            @can('edit students')
            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary">
                <i class="ri ri-pencil-line me-1"></i> Editar
            </a>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Foto del Estudiante</h5>
                </div>
                <div class="card-body text-center">
                    @if($student->foto)
                        <img src="{{ asset('storage/' . $student->foto) }}" class="img-fluid rounded" alt="Foto del estudiante">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="ri ri-user-line ri-5x text-muted"></i>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Información Básica</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Estado:</strong>
                            @if($student->status)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Edad:</strong>
                            <span>{{ $student->edad_con_meses }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Menor de edad:</strong>
                            @if($student->esMenorDeEdad)
                                <span class="badge bg-warning">Sí</span>
                            @else
                                <span class="badge bg-success">No</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Fecha de Registro:</strong>
                            <span>{{ $student->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Última Actualización:</strong>
                            <span>{{ $student->updated_at->format('d/m/Y H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Datos del Estudiante</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Nombres:</strong></label>
                            <p class="form-control-plaintext">{{ $student->nombres }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Apellidos:</strong></label>
                            <p class="form-control-plaintext">{{ $student->apellidos }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Fecha de Nacimiento:</strong></label>
                            <p class="form-control-plaintext">{{ $student->fecha_nacimiento->format('d/m/Y') }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Documento de Identidad:</strong></label>
                            <p class="form-control-plaintext">{{ $student->documento_identidad }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Código:</strong></label>
                            <p class="form-control-plaintext">{{ $student->codigo }}</p>
                        </div>

                        <!-- Mostrar correo electrónico para estudiantes mayores de edad -->
                        @if(!$student->esMenorDeEdad && $student->correo_electronico)
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Correo Electrónico:</strong></label>
                            <p class="form-control-plaintext">{{ $student->correo_electronico }}</p>
                        </div>
                        @endif

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Grado:</strong></label>
                            <p class="form-control-plaintext">{{ $student->grado }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Sección:</strong></label>
                            <p class="form-control-plaintext">{{ $student->seccion }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Nivel Educativo:</strong></label>
                            <p class="form-control-plaintext">{{ $student->nivelEducativo->nombre ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Turno:</strong></label>
                            <p class="form-control-plaintext">{{ $student->turno->nombre ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Período Escolar:</strong></label>
                            <p class="form-control-plaintext">{{ $student->schoolPeriod->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datos del representante (si es menor de edad y tiene datos) -->
            @if($student->esMenorDeEdad && ($student->representante_nombres || $student->representante_apellidos))
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Datos del Representante</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Nombres:</strong></label>
                            <p class="form-control-plaintext">{{ $student->representante_nombres ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Apellidos:</strong></label>
                            <p class="form-control-plaintext">{{ $student->representante_apellidos ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Documento de Identidad:</strong></label>
                            <p class="form-control-plaintext">{{ $student->representante_documento_identidad ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Teléfonos:</strong></label>
                            <p class="form-control-plaintext">
                                @if(is_array($student->representante_telefonos))
                                    {{ implode(', ', $student->representante_telefonos) }}
                                @else
                                    {{ $student->representante_telefonos ?? 'N/A' }}
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Correo Electrónico:</strong></label>
                            <p class="form-control-plaintext">{{ $student->representante_correo ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>