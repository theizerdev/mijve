<div>
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Registrar Observación de Conducta</h5>
            <small class="text-muted">Complete los datos para agregar una entrada al libro de vida</small>
        </div>

        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Estudiante <span class="text-danger">*</span></label>
                    <select class="form-select @error('student_id') is-invalid @enderror" wire:model="student_id">
                        <option value="">Seleccione...</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ $s->codigo }} - {{ $s->apellidos }}, {{ $s->nombres }}</option>
                        @endforeach
                    </select>
                    @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Período Escolar <span class="text-danger">*</span></label>
                    <select class="form-select @error('school_period_id') is-invalid @enderror" wire:model="school_period_id">
                        <option value="">Seleccione...</option>
                        @foreach($schoolPeriods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                        @endforeach
                    </select>
                    @error('school_period_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror" wire:model="date">
                    @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tipo de Observación <span class="text-danger">*</span></label>
                    <select class="form-select @error('type') is-invalid @enderror" wire:model="type">
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Gravedad <span class="text-danger">*</span></label>
                    <select class="form-select @error('severity') is-invalid @enderror" wire:model="severity">
                        @foreach($severities as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('severity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Categoría</label>
                    <select class="form-select" wire:model="category">
                        <option value="">Seleccione...</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Descripción <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" wire:model="description" rows="3" placeholder="Describa detalladamente la observación..."></textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Acciones Tomadas</label>
                    <textarea class="form-control" wire:model="actions_taken" rows="2" placeholder="¿Qué acciones se tomaron?"></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Notificación al Representante</label>
                    <textarea class="form-control" wire:model="parent_notified" rows="2" placeholder="¿Se notificó al representante? ¿Cómo?"></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Sección (Opcional)</label>
                    <select class="form-select" wire:model="section_id">
                        <option value="">Sin sección</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fecha de Notificación</label>
                    <input type="date" class="form-control" wire:model="parent_notification_date">
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <a href="{{ route('admin.conduct-records.index') }}" class="btn btn-secondary me-2">
                <i class="ri ri-arrow-left-line me-1"></i> Cancelar
            </a>
            <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">
                    <i class="ri ri-save-line me-1"></i> Guardar
                </span>
                <span wire:loading wire:target="save">
                    <span class="spinner-border spinner-border-sm me-1"></span> Guardando...
                </span>
            </button>
        </div>
    </div>
</div>
