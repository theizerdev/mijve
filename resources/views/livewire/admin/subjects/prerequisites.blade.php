<div>
    <!-- Breadcrumb -->
    <nav class="mb-4" aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach($this->getBreadcrumb() as $item)
                <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                    @if($loop->last)
                        {{ $item['name'] }}
                    @else
                        <a href="{{ $item['route'] }}">{{ $item['name'] }}</a>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4">Prerrequisitos - {{ $subject->name }}</h2>
            <p class="text-muted mb-0">Gestione los prerrequisitos de esta materia</p>
        </div>
        <button wire:click="create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Agregar Prerrequisito
        </button>
    </div>

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Prerequisites List -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Prerrequisitos</h5>
                <div class="form-check">
                    <input wire:model="showInactive" class="form-check-input" type="checkbox" id="showInactive">
                    <label class="form-check-label" for="showInactive">
                        Mostrar inactivos
                    </label>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($prerequisites->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Materia Prerrequisito</th>
                                <th>Tipo</th>
                                <th>Notas</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prerequisites as $prerequisite)
                                <tr>
                                    <td>
                                        <strong>{{ $prerequisite->prerequisiteSubject->name }}</strong>
                                        <br>
                                        <small class="text-muted">Código: {{ $prerequisite->prerequisiteSubject->code }}</small>
                                    </td>
                                    <td>
                                        @if($prerequisite->type == 'mandatory')
                                            <span class="badge bg-danger">Obligatorio</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Recomendado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($prerequisite->notes)
                                            <span class="text-muted">{{ Str::limit($prerequisite->notes, 50) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($prerequisite->is_active)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button wire:click="edit({{ $prerequisite->id }})" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button wire:click="toggleStatus({{ $prerequisite->id }})" class="btn btn-sm btn-outline-{{ $prerequisite->is_active ? 'warning' : 'success' }}">
                                                <i class="fas fa-{{ $prerequisite->is_active ? 'times' : 'check' }}"></i>
                                            </button>
                                            <button wire:click="delete({{ $prerequisite->id }})" class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirm('¿Está seguro de eliminar este prerrequisito?') || event.stopImmediatePropagation()">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $prerequisites->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-book text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No hay prerrequisitos registrados para esta materia.</p>
                    <button wire:click="create" class="btn btn-primary mt-2">
                        <i class="fas fa-plus"></i> Agregar Prerrequisito
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade @if($showModal) show d-block @endif" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $prerequisiteId ? 'Editar' : 'Agregar' }} Prerrequisito</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label for="prerequisite_subject_id" class="form-label">Materia Prerrequisito</label>
                            <select wire:model="prerequisite_subject_id" class="form-select @error('prerequisite_subject_id') is-invalid @enderror" id="prerequisite_subject_id">
                                <option value="">Seleccione una materia</option>
                                @foreach($availableSubjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                                @endforeach
                            </select>
                            @error('prerequisite_subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Tipo de Prerrequisito</label>
                            <select wire:model="type" class="form-select @error('type') is-invalid @enderror" id="type">
                                <option value="mandatory">Obligatorio</option>
                                <option value="recommended">Recomendado</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notas (Opcional)</label>
                            <textarea wire:model="notes" class="form-control @error('notes') is-invalid @enderror" id="notes" rows="3" placeholder="Ingrese notas adicionales sobre el prerrequisito"></textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input wire:model="is_active" class="form-check-input" type="checkbox" id="is_active">
                                <label class="form-check-label" for="is_active">
                                    Activo
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">Cancelar</button>
                    <button type="button" class="btn btn-primary" wire:click="save">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>