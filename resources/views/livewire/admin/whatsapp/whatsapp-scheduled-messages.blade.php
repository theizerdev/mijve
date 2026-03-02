<div>
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Mensajes Programados WhatsApp</h4>
                        <p class="text-muted mb-0">Programa y gestiona mensajes para envío automático</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button wire:click="$set('showCreateModal', true)" class="btn btn-primary">
                            <i class="ri ri-add-line me-2"></i>
                            Programar Mensaje
                        </button>
                        <a href="{{ route('admin.whatsapp.dashboard') }}" class="btn btn-secondary">
                            <i class="ri ri-arrow-left-line me-2"></i>
                            Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="avatar avatar-sm mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-primary">
                                <i class="ri ri-calendar-line"></i>
                            </span>
                        </div>
                        <h4 class="mb-1">{{ $statistics['total'] }}</h4>
                        <p class="text-muted mb-0">Total</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="avatar avatar-sm mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-warning">
                                <i class="ri ri-time-line"></i>
                            </span>
                        </div>
                        <h4 class="mb-1">{{ $statistics['pending'] }}</h4>
                        <p class="text-muted mb-0">Pendientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="avatar avatar-sm mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-success">
                                <i class="ri ri-send-plane-line"></i>
                            </span>
                        </div>
                        <h4 class="mb-1">{{ $statistics['sent'] }}</h4>
                        <p class="text-muted mb-0">Enviados</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="avatar avatar-sm mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-danger">
                                <i class="ri ri-error-warning-line"></i>
                            </span>
                        </div>
                        <h4 class="mb-1">{{ $statistics['failed'] }}</h4>
                        <p class="text-muted mb-0">Fallidos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="avatar avatar-sm mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-secondary">
                                <i class="ri ri-close-line"></i>
                            </span>
                        </div>
                        <h4 class="mb-1">{{ $statistics['cancelled'] }}</h4>
                        <p class="text-muted mb-0">Cancelados</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="avatar avatar-sm mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-info">
                                <i class="ri ri-calendar-todo-line"></i>
                            </span>
                        </div>
                        <h4 class="mb-1">{{ $statistics['today'] }}</h4>
                        <p class="text-muted mb-0">Hoy</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Buscar</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       wire:model.debounce.300ms="search"
                                       placeholder="Número o contenido del mensaje...">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Estado</label>
                                <select class="form-select" id="status" wire:model="status">
                                    <option value="">Todos</option>
                                    <option value="pending">Pendiente</option>
                                    <option value="sent">Enviado</option>
                                    <option value="failed">Fallido</option>
                                    <option value="cancelled">Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button type="button" 
                                            wire:click="resetFilters" 
                                            class="btn btn-outline-secondary">
                                        <i class="ri ri-refresh-line"></i>
                                        Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages Table -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Mensajes Programados</h5>
                            <div class="d-flex align-items-center gap-2">
                                <small class="text-muted">Mostrar:</small>
                                <select class="form-select form-select-sm" wire:model="perPage" style="width: auto;">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Programado para</th>
                                        <th>Destinatario</th>
                                        <th>Mensaje</th>
                                        <th>Plantilla</th>
                                        <th>Estado</th>
                                        <th>Usuario</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($messages as $message)
                                        <tr>
                                            <td>
                                                <div class="text-muted">
                                                    <div>{{ $message->scheduled_at->format('d/m/Y') }}</div>
                                                    <small>{{ $message->scheduled_at->format('H:i') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="ri ri-whatsapp-line me-2 text-success"></i>
                                                    <div>
                                                        <div class="fw-bold">+58{{ $message->recipient }}</div>
                                                        @if($message->student)
                                                            <small class="text-muted">{{ $message->student->nombre }} {{ $message->student->apellido }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="max-width: 300px;">
                                                    <div class="text-truncate">{{ $message->message }}</div>
                                                    @if($message->error_message)
                                                        <small class="text-danger">{{ Str::limit($message->error_message, 50) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($message->template)
                                                    <span class="badge bg-secondary">{{ $message->template->name }}</span>
                                                @else
                                                    <span class="badge bg-light text-dark">Manual</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $message->status === 'sent' ? 'success' : 
                                                    ($message->status === 'failed' ? 'danger' : 
                                                    ($message->status === 'cancelled' ? 'secondary' : 'warning'))
                                                }}">
                                                    {{ ucfirst($message->status) }}
                                                </span>
                                                @if($message->sent_at)
                                                    <br><small class="text-muted">{{ $message->sent_at->diffForHumans() }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="ri ri-user-line me-2 text-muted"></i>
                                                    <div>
                                                        <div class="fw-bold">{{ $message->user->name }}</div>
                                                        <small class="text-muted">{{ $message->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @if($message->status === 'pending')
                                                        <button wire:click="editMessage({{ $message->id }})" 
                                                                class="btn btn-sm btn-outline-primary"
                                                                title="Editar">
                                                            <i class="ri ri-edit-line"></i>
                                                        </button>
                                                        <button wire:click="cancelMessage({{ $message->id }})" 
                                                                class="btn btn-sm btn-outline-warning"
                                                                title="Cancelar"
                                                                onclick="confirm('¿Estás seguro de cancelar este mensaje?') || event.stopImmediatePropagation()">
                                                            <i class="ri ri-close-line"></i>
                                                        </button>
                                                    @endif
                                                    @if(in_array($message->status, ['pending', 'cancelled', 'failed']))
                                                        <button wire:click="deleteMessage({{ $message->id }})" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                title="Eliminar"
                                                                onclick="confirm('¿Estás seguro de eliminar este mensaje?') || event.stopImmediatePropagation()">
                                                            <i class="ri ri-delete-bin-line"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="ri ri-calendar-todo-line ri-3x text-muted mb-3"></i>
                                                <h6 class="text-muted">No hay mensajes programados</h6>
                                                <p class="text-muted small">Los mensajes programados aparecerán aquí</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Mostrando {{ $messages->firstItem() }} a {{ $messages->lastItem() }} de {{ $messages->total() }} mensajes
                            </div>
                            <div>
                                {{ $messages->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade @if($showCreateModal || $showEditModal) show d-block @endif" 
         tabindex="-1" 
         style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $showEditModal ? 'Editar Mensaje Programado' : 'Programar Mensaje' }}
                    </h5>
                    <button type="button" 
                            class="btn-close" 
                            wire:click="$set('showCreateModal', false); $set('showEditModal', false); $set('editingMessage', null);"></button>
                </div>
                <div class="modal-body">
                    @if($error)
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="ri ri-error-warning-line me-2"></i>
                            {{ $error }}
                            <button type="button" class="btn-close" wire:click="$set('error', '')"></button>
                        </div>
                    @endif

                    <!-- Tabs -->
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link @if($activeTab === 'manual') active @endif" 
                               wire:click="$set('activeTab', 'manual')" 
                               href="#">
                                Mensaje Manual
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if($activeTab === 'template') active @endif" 
                               wire:click="$set('activeTab', 'template')" 
                               href="#">
                                Plantilla
                            </a>
                        </li>
                    </ul>

                    <form wire:submit.prevent="{{ $showEditModal ? 'updateScheduledMessage' : 'createScheduledMessage' }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="recipient" class="form-label">Número de Teléfono</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+58</span>
                                        <input type="tel" 
                                               class="form-control @error('recipient') is-invalid @enderror" 
                                               id="recipient" 
                                               wire:model="recipient"
                                               placeholder="4121234567">
                                        @error('recipient')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="scheduledDate" class="form-label">Fecha y Hora</label>
                                    <div class="d-flex gap-2">
                                        <input type="date" 
                                               class="form-control @error('scheduledDate') is-invalid @enderror" 
                                               id="scheduledDate" 
                                               wire:model="scheduledDate">
                                        <input type="time" 
                                               class="form-control @error('scheduledTime') is-invalid @enderror" 
                                               id="scheduledTime" 
                                               wire:model="scheduledTime">
                                    </div>
                                    @error('scheduledDate')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @error('scheduledTime')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if($activeTab === 'manual')
                            <div class="mb-3">
                                <label for="message" class="form-label">Mensaje</label>
                                <textarea class="form-control @error('message') is-invalid @enderror" 
                                          id="message" 
                                          wire:model="message"
                                          rows="4"
                                          maxlength="1000"
                                          placeholder="Escribe tu mensaje aquí..."></textarea>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Máximo 1000 caracteres</small>
                                    <small class="text-muted">{{ strlen($message) }}/1000</small>
                                </div>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <div class="mb-3">
                                <label for="selectedTemplate" class="form-label">Plantilla</label>
                                <select class="form-select @error('selectedTemplate') is-invalid @enderror" 
                                        id="selectedTemplate" 
                                        wire:model="selectedTemplate">
                                    <option value="">Selecciona una plantilla</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedTemplate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if($selectedTemplate && !empty($templateVariables))
                                <div class="mb-3">
                                    <label class="form-label">Variables de Plantilla</label>
                                    @foreach($templateVariables as $key => $value)
                                        <div class="mb-2">
                                            <label for="var_{{ $key }}" class="form-label text-capitalize">{{ str_replace('_', ' ', $key) }}</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="var_{{ $key }}" 
                                                   wire:model="templateVariables.{{ $key }}"
                                                   placeholder="Ingresa {{ str_replace('_', ' ', $key) }}">
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Vista Previa</label>
                                    <div class="alert alert-light">
                                        <small class="text-muted">{{ $this->processTemplateMessage() }}</small>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" 
                                    class="btn btn-secondary" 
                                    wire:click="$set('showCreateModal', false); $set('showEditModal', false); $set('editingMessage', null);">
                                Cancelar
                            </button>
                            <button type="submit" 
                                    class="btn btn-primary" 
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    {{ $showEditModal ? 'Actualizar' : 'Programar' }}
                                </span>
                                <span wire:loading>
                                    <i class="ri ri-loader-4-line ri-spin me-2"></i>
                                    Procesando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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