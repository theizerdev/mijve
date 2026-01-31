<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Editar Sección: {{ $section->name }}</h4>
        <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary">
            <i class="ri ri-arrow-left-line me-1"></i> Volver
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form wire:submit.prevent="update">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="code" class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="text" id="code" class="form-control @error('code') is-invalid @enderror" wire:model="code" placeholder="SEC-001">
                        @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8 mb-3">
                        <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Sección de Matemáticas I">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="school_period_id" class="form-label">Período Escolar <span class="text-danger">*</span></label>
                        <select id="school_period_id" class="form-select @error('school_period_id') is-invalid @enderror" wire:model="school_period_id">
                            <option value="">Seleccione un período</option>
                            @foreach($school_periods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                            @endforeach
                        </select>
                        @error('school_period_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="subject_id" class="form-label">Materia <span class="text-danger">*</span></label>
                        <select id="subject_id" class="form-select @error('subject_id') is-invalid @enderror" wire:model="subject_id">
                            <option value="">Seleccione una materia</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        @error('subject_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="teacher_id" class="form-label">Profesor <span class="text-danger">*</span></label>
                        <select id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" wire:model="teacher_id">
                            <option value="">Seleccione un profesor</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="classroom_id" class="form-label">Aula <span class="text-danger">*</span></label>
                        <select id="classroom_id" class="form-select @error('classroom_id') is-invalid @enderror" wire:model="classroom_id">
                            <option value="">Seleccione un aula</option>
                            @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}">{{ $classroom->name }} - {{ $classroom->code }}</option>
                            @endforeach
                        </select>
                        @error('classroom_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="capacity" class="form-label">Capacidad <span class="text-danger">*</span></label>
                        <input type="number" id="capacity" class="form-control @error('capacity') is-invalid @enderror" wire:model="capacity" min="1" max="100">
                        @error('capacity')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="empresa_id" class="form-label">Empresa <span class="text-danger">*</span></label>
                        <select id="empresa_id" class="form-select @error('empresa_id') is-invalid @enderror" wire:model="empresa_id">
                            <option value="">Seleccione una empresa</option>
                            @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                            @endforeach
                        </select>
                        @error('empresa_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="sucursal_id" class="form-label">Sucursal <span class="text-danger">*</span></label>
                        <select id="sucursal_id" class="form-select @error('sucursal_id') is-invalid @enderror" wire:model="sucursal_id" @empty($sucursales) disabled @endempty>
                            <option value="">Seleccione una sucursal</option>
                            @foreach($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                            @endforeach
                        </select>
                        @error('sucursal_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">Estado</label>
                        <select id="status" class="form-select @error('status') is-invalid @enderror" wire:model="status">
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea id="description" class="form-control @error('description') is-invalid @enderror" wire:model="description" rows="3" placeholder="Descripción de la sección (opcional)"></textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary">
                        <i class="ri ri-close-line me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri ri-save-line me-1"></i> Actualizar
                    </button>
                </div>
            </form>
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