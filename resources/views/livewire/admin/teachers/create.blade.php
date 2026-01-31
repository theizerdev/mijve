<div>
    <div class="container-fluid">
        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Crear Profesor</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            @foreach($this->getBreadcrumb() as $route => $title)
                                <li class="breadcrumb-item">
                                    @if($loop->last)
                                        <span>{{ $title }}</span>
                                    @else
                                        <a href="{{ route($route) }}">{{ $title }}</a>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="ri ri-user-line me-2"></i>Datos del Profesor
                        </h5>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="save">
                            <div class="row">
                                {{-- Name --}}
                                <div class="col-md-4 mb-3">
                                    <label for="name" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           wire:model="name"
                                           placeholder="Ej: Juan Pérez García">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="col-md-4 mb-3">
                                    <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           wire:model="email"
                                           placeholder="Ej: juan.perez@ejemplo.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Username --}}
                                <div class="col-md-4 mb-3">
                                    <label for="username" class="form-label">Nombre de Usuario <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('username') is-invalid @enderror" 
                                           id="username" 
                                           wire:model="username"
                                           placeholder="Ej: jperez">
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                {{-- Employee Code --}}
                                <div class="col-md-6 mb-3">
                                    <label for="employee_code" class="form-label">Código de Empleado <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('employee_code') is-invalid @enderror" 
                                           id="employee_code" 
                                           wire:model="employee_code"
                                           placeholder="Ej: PROF-001">
                                    @error('employee_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Specialization --}}
                                <div class="col-md-6 mb-3">
                                    <label for="specialization" class="form-label">Especialización <span class="text-danger">*</span></label>
                                    <select class="form-select @error('specialization') is-invalid @enderror" 
                                            id="specialization" wire:model="specialization">
                                        <option value="">Seleccione una especialización</option>
                                        <option value="Matemáticas">Matemáticas</option>
                                        <option value="Ciencias">Ciencias</option>
                                        <option value="Lenguaje">Lenguaje</option>
                                        <option value="Historia">Historia</option>
                                        <option value="Educación Física">Educación Física</option>
                                        <option value="Arte">Arte</option>
                                        <option value="Música">Música</option>
                                        <option value="Tecnología">Tecnología</option>
                                        <option value="Inglés">Inglés</option>
                                        <option value="Otra">Otra</option>
                                    </select>
                                    @error('specialization')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                {{-- Degree --}}
                                <div class="col-md-12 mb-3">
                                    <label for="degree" class="form-label">Título Académico <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('degree') is-invalid @enderror" 
                                           id="degree" 
                                           wire:model="degree"
                                           placeholder="Ej: Licenciado en Matemáticas">
                                    @error('degree')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                {{-- Years Experience --}}
                                <div class="col-md-4 mb-3">
                                    <label for="years_experience" class="form-label">Años de Experiencia <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('years_experience') is-invalid @enderror" 
                                           id="years_experience" 
                                           wire:model="years_experience"
                                           min="0" max="50">
                                    @error('years_experience')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Hire Date --}}
                                <div class="col-md-4 mb-3">
                                    <label for="hire_date" class="form-label">Fecha de Contratación <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('hire_date') is-invalid @enderror" 
                                           id="hire_date" 
                                           wire:model="hire_date">
                                    @error('hire_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Status --}}
                                <div class="col-md-4 mb-3">
                                    <label for="is_active" class="form-label">Estado</label>
                                    <div class="form-check form-switch form-switch-lg mt-2">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               id="is_active" 
                                               wire:model="is_active">
                                        <label class="form-check-label" for="is_active">
                                            {{ $is_active ? 'Activo' : 'Inactivo' }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Notes --}}
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="notes" class="form-label">Notas Adicionales</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" 
                                              wire:model="notes"
                                              rows="3"
                                              placeholder="Información adicional sobre el profesor..."></textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-end">
                                        <a href="{{ route('admin.teachers.index') }}" class="btn btn-label-secondary me-2">
                                            <i class="ri ri-arrow-left-line me-1"></i> Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri ri-save-line me-1"></i> Crear Profesor y Usuario
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
