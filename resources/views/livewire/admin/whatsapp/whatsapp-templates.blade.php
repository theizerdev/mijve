<div>
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Plantillas WhatsApp</h4>
                        <p class="text-muted mb-0">Gestiona las plantillas de mensajes para WhatsApp</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.whatsapp.dashboard') }}" class="btn btn-secondary">
                            <i class="ri ri-arrow-left-line me-2"></i>
                            Volver al Dashboard
                        </a>
                        @can('create whatsapp templates')
                        <button wire:click="createTemplate" class="btn btn-primary">
                            <i class="ri ri-add-line me-2"></i>
                            Nueva Plantilla
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="avatar avatar-sm mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-primary">
                                <i class="ri ri-file-text-line"></i>
                            </span>
                        </div>
                        <h4 class="mb-1">{{ $templates->total() }}</h4>
                        <p class="text-muted mb-0">Total Plantillas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="avatar avatar-sm mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-success">
                                <i class="ri ri-checkbox-circle-line"></i>
                            </span>
                        </div>
                        <h4 class="mb-1">{{ \App\Models\WhatsAppTemplate::where('is_active', true)->count() }}</h4>
                        <p class="text-muted mb-0">Activas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="avatar avatar-sm mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-warning">
                                <i class="ri ri-notification-3-line"></i>
                            </span>
                        </div>
                        <h4 class="mb-1">{{ \App\Models\WhatsAppTemplate::where('category', 'notification')->count() }}</h4>
                        <p class="text-muted mb-0">Notificaciones</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="avatar avatar-sm mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-info">
                                <i class="ri ri-marketing-line"></i>
                            </span>
                        </div>
                        <h4 class="mb-1">{{ \App\Models\WhatsAppTemplate::where('category', 'marketing')->count() }}</h4>
                        <p class="text-muted mb-0">Marketing</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Templates Table -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="card-title mb-0">Listado de Plantillas</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Categoría</th>
                                        <th>Estado</th>
                                        <th>Uso</th>
                                        <th>Creado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($templates as $template)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="ri ri-file-text-line me-2 text-primary"></i>
                                                    <div>
                                                        <div class="fw-bold">{{ $template->name }}</div>
                                                        <small class="text-muted">
                                                            {{ Str::limit($template->content, 50) }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-muted">{{ Str::limit($template->description, 60) }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $template->category === 'notification' ? 'info' : 
                                                    ($template->category === 'reminder' ? 'warning' : 
                                                    ($template->category === 'marketing' ? 'success' : 'secondary'))
                                                }}">
                                                    {{ ucfirst($template->category) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           wire:click="toggleStatus({{ $template->id }})"
                                                           {{ $template->is_active ? 'checked' : '' }}
                                                           {{ !auth()->user()->can('update whatsapp templates') ? 'disabled' : '' }}>
                                                    <label class="form-check-label">
                                                        {{ $template->is_active ? 'Activo' : 'Inactivo' }}
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div class="fw-bold">{{ $template->usage_count }}</div>
                                                    <small class="text-muted">usos</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-muted">
                                                    <div>{{ $template->created_at->format('d/m/Y') }}</div>
                                                    <small>{{ $template->creator->name }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    @can('update whatsapp templates')
                                                    <button wire:click="editTemplate({{ $template->id }})" 
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="Editar">
                                                        <i class="ri ri-edit-line"></i>
                                                    </button>
                                                    @endcan
                                                    @can('delete whatsapp templates')
                                                    <button wire:click="confirmDelete({{ $template->id }})" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Eliminar">
                                                        <i class="ri ri-delete-bin-line"></i>
                                                    </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="ri ri-file-text-line ri-3x text-muted mb-3"></i>
                                                <h6 class="text-muted">No hay plantillas disponibles</h6>
                                                <p class="text-muted small">Crea tu primera plantilla para comenzar</p>
                                                @can('create whatsapp templates')
                                                <button wire:click="createTemplate" class="btn btn-primary btn-sm">
                                                    <i class="ri ri-add-line me-2"></i>
                                                    Crear Plantilla
                                                </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Mostrando {{ $templates->firstItem() }} a {{ $templates->lastItem() }} de {{ $templates->total() }} plantillas
                            </div>
                            <div>
                                {{ $templates->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $editingTemplate ? 'Editar Plantilla' : 'Nueva Plantilla' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="resetForm"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveTemplate">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre *</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           wire:model="name"
                                           placeholder="Ej: Bienvenida Estudiante">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Categoría *</label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" 
                                            wire:model="category">
                                        <option value="notification">Notificación</option>
                                        <option value="reminder">Recordatorio</option>
                                        <option value="marketing">Marketing</option>
                                        <option value="alert">Alerta</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      wire:model="description"
                                      rows="2"
                                      placeholder="Describe el propósito de esta plantilla"></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Contenido *</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" 
                                      wire:model="content"
                                      rows="6"
                                      placeholder="Escribe el contenido de la plantilla. Usa {{variable}} para variables"></textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Usa {{variable}} para crear variables dinámicas. Ej: Hola {{nombre}}, tu matrícula es {{matricula}}
                            </small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       wire:model="is_active">
                                <label class="form-check-label" for="is_active">
                                    Plantilla activa
                                </label>
                            </div>
                        </div>

                        @if($content)
                        <div class="mb-3">
                            <label class="form-label">Variables detectadas:</label>
                            @php
                                preg_match_all('/\{\{([^}]+)\}\}/', $content, $matches);
                                $variables = $matches[1] ?? [];
                            @endphp
                            @if(count($variables) > 0)
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($variables as $variable)
                                        <span class="badge bg-info">{{ $variable }}</span>
                                    @endforeach
                                </div>
                            @else
                                <small class="text-muted">No se detectaron variables</small>
                            @endif
                        </div>
                        @endif
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="resetForm">Cancelar</button>
                    <button type="button" class="btn btn-primary" wire:click="saveTemplate">
                        {{ $editingTemplate ? 'Actualizar' : 'Crear' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($deleteModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" wire:click="resetForm"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar esta plantilla?</p>
                    <p class="text-muted">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="resetForm">Cancelar</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteTemplate">
                        <i class="ri ri-delete-bin-line me-2"></i>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Flash Messages -->
    @if(session()->has('success'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri ri-check-line me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    @if(session()->has('error'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri ri-error-warning-line me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif
</div>