<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Crear Nuevo Plan de Estudio</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Código <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" wire:model="code" placeholder="Ej: ING-SIS-2024">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" wire:model="name" placeholder="Ej: Ingeniería de Sistemas">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="program_id" class="form-label">Programa <span class="text-danger">*</span></label>
                                <select class="form-select @error('program_id') is-invalid @enderror" 
                                        id="program_id" wire:model="program_id">
                                    <option value="">Seleccione un programa</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}">{{ $program->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('program_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="educational_level_id" class="form-label">Nivel Educativo <span class="text-danger">*</span></label>
                                <select class="form-select @error('educational_level_id') is-invalid @enderror" 
                                        id="educational_level_id" wire:model="educational_level_id">
                                    <option value="">Seleccione un nivel</option>
                                    @foreach($educationalLevels as $level)
                                        <option value="{{ $level->id }}">{{ $level->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('educational_level_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" wire:model="description" rows="3" 
                                      placeholder="Descripción del plan de estudio..."></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="total_credits" class="form-label">Créditos Totales</label>
                                <input type="number" class="form-control @error('total_credits') is-invalid @enderror" 
                                       id="total_credits" wire:model="total_credits" min="0">
                                @error('total_credits')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="total_hours" class="form-label">Horas Totales</label>
                                <input type="number" class="form-control @error('total_hours') is-invalid @enderror" 
                                       id="total_hours" wire:model="total_hours" min="0">
                                @error('total_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="duration_years" class="form-label">Duración (Años)</label>
                                <input type="number" class="form-control @error('duration_years') is-invalid @enderror" 
                                       id="duration_years" wire:model="duration_years" min="0">
                                @error('duration_years')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="duration_semesters" class="form-label">Duración (Semestres)</label>
                                <input type="number" class="form-control @error('duration_semesters') is-invalid @enderror" 
                                       id="duration_semesters" wire:model="duration_semesters" min="0">
                                @error('duration_semesters')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="effective_date" class="form-label">Fecha Efectiva</label>
                                <input type="date" class="form-control @error('effective_date') is-invalid @enderror" 
                                       id="effective_date" wire:model="effective_date">
                                @error('effective_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="expiration_date" class="form-label">Fecha de Expiración</label>
                                <input type="date" class="form-control @error('expiration_date') is-invalid @enderror" 
                                       id="expiration_date" wire:model="expiration_date">
                                @error('expiration_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" wire:model="status">
                                    <option value="active">Activo</option>
                                    <option value="inactive">Inactivo</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_default" wire:model="is_default">
                                <label class="form-check-label" for="is_default">
                                    Establecer como plan por defecto
                                </label>
                            </div>
                            <small class="text-muted">Si hay otro plan por defecto para el mismo programa y nivel, será desactivado.</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.study-plans.index') }}" class="btn btn-outline-secondary">
                                <i class="ri ri-arrow-left-line me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri ri-save-line me-1"></i>Crear Plan de Estudio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>