<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Editar Estudiante</h2>
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
            <i class="ri ri-arrow-left-line me-1"></i> Volver
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Datos del Estudiante</h5>
        </div>
        <div class="card-body">
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form wire:submit.prevent="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombres" class="form-label">Nombres *</label>
                        <input type="text" id="nombres" class="form-control @error('nombres') is-invalid @enderror" wire:model="nombres">
                        @error('nombres') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="apellidos" class="form-label">Apellidos *</label>
                        <input type="text" id="apellidos" class="form-control @error('apellidos') is-invalid @enderror" wire:model="apellidos">
                        @error('apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento *</label>
                        <input type="date" id="fecha_nacimiento" class="form-control @error('fecha_nacimiento') is-invalid @enderror" wire:model="fecha_nacimiento" max="{{ date('Y-m-d') }}">
                        @error('fecha_nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        @if($fecha_nacimiento)
                            <div class="mt-2">
                                <span class="badge bg-info">
                                    Edad: {{ $this->edadConMeses }}
                                </span>
                                @if($this->esMenorDeEdad)
                                    <span class="badge bg-warning">
                                        Menor de edad
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        Mayor de edad
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="documento_identidad" class="form-label">Documento de Identidad *</label>
                        <input type="text" id="documento_identidad" class="form-control @error('documento_identidad') is-invalid @enderror" wire:model="documento_identidad">
                        @error('documento_identidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="codigo" class="form-label">Código *</label>
                        <div class="input-group">
                            <input type="text" id="codigo" class="form-control @error('codigo') is-invalid @enderror" wire:model="codigo" maxlength="8">
                            <button class="btn btn-outline-secondary" type="button" wire:click="generateCode">
                                <i class="ri ri-refresh-line"></i>
                            </button>
                        </div>
                        @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Mostrar campo de correo para estudiantes mayores de edad -->

                    <div class="col-md-6 mb-3">
                        <label for="correo_electronico" class="form-label">Correo Electrónico</label>
                        <input type="email" id="correo_electronico" class="form-control @error('correo_electronico') is-invalid @enderror" wire:model="correo_electronico" placeholder="ejemplo@correo.com">
                        <div class="form-text">Correo para enviar información de matrícula</div>
                        @error('correo_electronico') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>


                    <div class="col-md-4 mb-3">
                        <label for="grado" class="form-label">Grado *</label>
                        <input type="text" id="grado" class="form-control @error('grado') is-invalid @enderror" wire:model="grado">
                        @error('grado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="seccion" class="form-label">Sección *</label>
                        <input type="text" id="seccion" class="form-control @error('seccion') is-invalid @enderror" wire:model="seccion">
                        @error('seccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="nivel_educativo_id" class="form-label">Nivel Educativo *</label>
                        <select id="nivel_educativo_id" class="form-select @error('nivel_educativo_id') is-invalid @enderror" wire:model="nivel_educativo_id">
                            <option value="">Seleccione un nivel</option>
                            @foreach($nivelesEducativos as $nivel)
                                <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                            @endforeach
                        </select>
                        @error('nivel_educativo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="turno_id" class="form-label">Turno *</label>
                        <select id="turno_id" class="form-select @error('turno_id') is-invalid @enderror" wire:model="turno_id">
                            <option value="">Seleccione un turno</option>
                            @foreach($turnos as $turno)
                                <option value="{{ $turno->id }}">{{ $turno->nombre }}</option>
                            @endforeach
                        </select>
                        @error('turno_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="school_periods_id" class="form-label">Período Escolar *</label>
                        <select id="school_periods_id" class="form-select @error('school_periods_id') is-invalid @enderror" wire:model="school_periods_id">
                            <option value="">Seleccione un período</option>
                            @foreach($schoolPeriods as $period)
                                <option value="{{ $period->id }}">{{ $period->name }}</option>
                            @endforeach
                        </select>
                        @error('school_periods_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input @error('status') is-invalid @enderror" id="status" wire:model="status">
                            <label class="form-check-label" for="status">¿Está activo?</label>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Sección de datos del representante (solo si es menor de edad) -->
                @if($this->esMenorDeEdad)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h5 class="mb-0">Datos del Representante</h5>
                                <small class="text-muted">Estos datos son requeridos porque el estudiante es menor de edad</small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="representante_nombres" class="form-label">Nombres del Representante</label>
                                        <input type="text" id="representante_nombres" class="form-control @error('representante_nombres') is-invalid @enderror" wire:model="representante_nombres">
                                        @error('representante_nombres') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="representante_apellidos" class="form-label">Apellidos del Representante</label>
                                        <input type="text" id="representante_apellidos" class="form-control @error('representante_apellidos') is-invalid @enderror" wire:model="representante_apellidos">
                                        @error('representante_apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="representante_documento_identidad" class="form-label">Documento de Identidad del Representante</label>
                                        <input type="text" id="representante_documento_identidad" class="form-control @error('representante_documento_identidad') is-invalid @enderror" wire:model="representante_documento_identidad">
                                        @error('representante_documento_identidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="representante_telefonos" class="form-label">Teléfonos del Representante</label>
                                        <input type="text" id="representante_telefonos" class="form-control @error('representante_telefonos') is-invalid @enderror" wire:model="representante_telefonos" placeholder="Ej: 123456789, 987654321">
                                        <div class="form-text">Separe múltiples números con comas</div>
                                        @error('representante_telefonos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="representante_correo" class="form-label">Correo Electrónico del Representante</label>
                                        <input type="email" id="representante_correo" class="form-control @error('representante_correo') is-invalid @enderror" wire:model="representante_correo">
                                        @error('representante_correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary me-2">
                        <i class="ri ri-arrow-left-line me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri ri-save-line me-1"></i> Actualizar Estudiante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
