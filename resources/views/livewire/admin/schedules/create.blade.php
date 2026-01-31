<div>
    @section('title', 'Crear Horario')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Crear Nuevo Horario</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session()->has('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="store">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="empresa_id">Empresa *</label>
                                    <select class="form-control @error('empresa_id') is-invalid @enderror" id="empresa_id" wire:model="empresa_id">
                                        <option value="">Seleccione una empresa</option>
                                        @foreach($companies as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('empresa_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="branch_id">Sucursal *</label>
                                    <select class="form-control @error('branch_id') is-invalid @enderror" id="branch_id" wire:model="branch_id" @disabled(!$empresa_id)>
                                        <option value="">Seleccione una sucursal</option>
                                        @foreach($branches as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="school_period_id">Período Escolar *</label>
                                    <select class="form-control @error('school_period_id') is-invalid @enderror" id="school_period_id" wire:model="school_period_id">
                                        <option value="">Seleccione un período</option>
                                        @foreach($schoolPeriods as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('school_period_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="section_id">Sección *</label>
                                    <select class="form-control @error('section_id') is-invalid @enderror" id="section_id" wire:model="section_id" @disabled(!$empresa_id || !$branch_id)>
                                        <option value="">Seleccione una sección</option>
                                        @foreach($sections as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('section_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="classroom_id">Aula *</label>
                                    <select class="form-control @error('classroom_id') is-invalid @enderror" id="classroom_id" wire:model="classroom_id" @disabled(!$empresa_id || !$branch_id)>
                                        <option value="">Seleccione un aula</option>
                                        @foreach($classrooms as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('classroom_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="day">Día *</label>
                                    <select class="form-control @error('day') is-invalid @enderror" id="day" wire:model="day">
                                        <option value="">Seleccione un día</option>
                                        @foreach($days as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('day')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_time">Hora de Inicio *</label>
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" wire:model="start_time">
                                    @error('start_time')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_time">Hora de Fin *</label>
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" wire:model="end_time">
                                    @error('end_time')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notes">Notas</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" rows="3" wire:model="notes" placeholder="Ingrese notas adicionales (opcional)"></textarea>
                                    @error('notes')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Estado</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" wire:model="status">
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Guardar Horario
                                </button>
                                <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('schedule-conflicts', data => {
                let conflictsText = 'Advertencia: Existen conflictos de horario con las siguientes secciones:\n\n';
                data.conflicts.forEach(conflict => {
                    conflictsText += `• ${conflict.section_code} - ${conflict.section_name} (${conflict.start_time} - ${conflict.end_time})\n`;
                });
                conflictsText += '\n¿Deseas continuar con el registro?';
                
                if (!confirm(conflictsText)) {
                    // Limpiar campos o tomar otra acción
                }
            });
        });
    </script>
    @endpush
</div>