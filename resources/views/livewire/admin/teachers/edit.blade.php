<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="ri ri-pencil-line me-2"></i>Editar Profesor
                        </h5>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="save">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nombre Completo *</label>
                                        <input type="text" wire:model="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Ej: Juan Pérez García">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Correo Electrónico *</label>
                                        <input type="email" wire:model="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Ej: juan.perez@ejemplo.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="user_id" class="form-label">Usuario *</label>
                                        <select wire:model="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                            <option value="">Seleccione un usuario</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>
                                            @endforeach
                                        </select>
                                        @error('user_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="employee_code" class="form-label">Código de Empleado *</label>
                                        <input type="text" wire:model="employee_code" id="employee_code" class="form-control @error('employee_code') is-invalid @enderror" placeholder="Ej: PROF-001">
                                        @error('employee_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="specialization" class="form-label">Especialización *</label>
                                        <input type="text" wire:model="specialization" id="specialization" class="form-control @error('specialization') is-invalid @enderror" placeholder="Ej: Matemáticas, Física, Literatura">
                                        @error('specialization')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="degree" class="form-label">Título Académico *</label>
                                        <input type="text" wire:model="degree" id="degree" class="form-control @error('degree') is-invalid @enderror" placeholder="Ej: Licenciado, Ingeniero, Magister">
                                        @error('degree')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="years_experience" class="form-label">Años de Experiencia *</label>
                                        <input type="number" wire:model="years_experience" id="years_experience" class="form-control @error('years_experience') is-invalid @enderror" min="0" max="50">
                                        @error('years_experience')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="hire_date" class="form-label">Fecha de Contratación *</label>
                                        <input type="date" wire:model="hire_date" id="hire_date" class="form-control @error('hire_date') is-invalid @enderror">
                                        @error('hire_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" wire:model="is_active" id="is_active" class="form-check-input">
                                            <label for="is_active" class="form-check-label">Profesor Activo</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Notas Adicionales</label>
                                        <textarea wire:model="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Información adicional sobre el profesor"></textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <a href="{{ route('admin.teachers.show', $teacher) }}" class="btn btn-label-secondary">
                                        <i class="ri ri-arrow-left-line me-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary ms-2">
                                        <i class="ri ri-save-line me-1"></i> Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>