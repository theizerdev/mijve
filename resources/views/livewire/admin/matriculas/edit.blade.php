<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Editar Matrícula</h4>
            <p class="text-muted mb-0">Actualizar datos de matrícula de estudiante</p>
        </div>
        <div>
            <a href="{{ route('admin.matriculas.index') }}" class="btn btn-secondary">
                <i class="ri ri-arrow-left-line me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Formulario de Matrícula</h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="update">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="student_id" class="form-label">Estudiante *</label>
                        <select wire:model="student_id" class="form-select" id="student_id" required>
                            <option value="">Seleccione un estudiante</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->nombres }} {{ $student->apellidos }} (DNI: {{ $student->documento_identidad }})</option>
                            @endforeach
                        </select>
                        @error('student_id') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="programa_id" class="form-label">Programa *</label>
                        <select wire:model="programa_id" class="form-select" id="programa_id" required>
                            <option value="">Seleccione un programa</option>
                            @foreach($programas as $programa)
                                <option value="{{ $programa->id }}">{{ $programa->nombre }}</option>
                            @endforeach
                        </select>
                        @error('programa_id') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="periodo_id" class="form-label">Período Escolar *</label>
                        <select wire:model="periodo_id" class="form-select" id="periodo_id" required>
                            <option value="">Seleccione un período</option>
                            @foreach($periodos as $periodo)
                                <option value="{{ $periodo->id }}">{{ $periodo->name }}</option>
                            @endforeach
                        </select>
                        @error('periodo_id') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="fecha_matricula" class="form-label">Fecha de Matrícula *</label>
                        <input type="date" wire:model="fecha_matricula" class="form-control" id="fecha_matricula" required>
                        @error('fecha_matricula') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="estado" class="form-label">Estado *</label>
                        <select wire:model="estado" class="form-select" id="estado" required>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                            <option value="graduado">Graduado</option>
                        </select>
                        @error('estado') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <h5 class="mt-4 mb-3">Información de Costos</h5>
                    </div>

                    <div class="col-md-4">
                        <label for="costo" class="form-label">Costo Total *</label>
                        <input type="number" step="0.01" wire:model="costo" class="form-control" id="costo" required>
                        @error('costo') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="cuota_inicial" class="form-label">Cuota Inicial *</label>
                        <input type="number" step="0.01" wire:model="cuota_inicial" class="form-control" id="cuota_inicial" required>
                        @error('cuota_inicial') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="numero_cuotas" class="form-label">Número de Cuotas *</label>
                        <input type="number" wire:model="numero_cuotas" class="form-control" id="numero_cuotas" required>
                        @error('numero_cuotas') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri ri-save-line me-1"></i> Actualizar Matrícula
                    </button>
                    <a href="{{ route('admin.matriculas.index') }}" class="btn btn-secondary ms-2">
                        <i class="ri ri-arrow-left-line me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
