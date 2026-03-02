<div>
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Historial de Mensajes WhatsApp</h4>
                        <p class="text-muted mb-0">Revisa todos los mensajes enviados a través de WhatsApp</p>
                    </div>
                    <div class="d-flex gap-2">
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
                                <i class="ri ri-message-3-line"></i>
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
                            <span class="avatar-initial rounded-circle bg-info">
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
                            <span class="avatar-initial rounded-circle bg-success">
                                <i class="ri ri-check-double-line"></i>
                            </span>
                        </div>
                        <h4 class="mb-1">{{ $statistics['delivered'] }}</h4>
                        <p class="text-muted mb-0">Entregados</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="avatar avatar-sm mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-warning">
                                <i class="ri ri-eye-line"></i>
                            </span>
                        </div>
                        <h4 class="mb-1">{{ $statistics['read'] }}</h4>
                        <p class="text-muted mb-0">Leídos</p>
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
                                <i class="ri ri-calendar-line"></i>
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
                            <div class="col-md-3">
                                <label for="search" class="form-label">Buscar</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       wire:model.debounce.300ms="search"
                                       placeholder="Número o contenido del mensaje...">
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Estado</label>
                                <select class="form-select" id="status" wire:model="status">
                                    <option value="">Todos</option>
                                    <option value="pending">Pendiente</option>
                                    <option value="sent">Enviado</option>
                                    <option value="delivered">Entregado</option>
                                    <option value="read">Leído</option>
                                    <option value="failed">Fallido</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="dateFrom" class="form-label">Desde</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="dateFrom" 
                                       wire:model="dateFrom">
                            </div>
                            <div class="col-md-2">
                                <label for="dateTo" class="form-label">Hasta</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="dateTo" 
                                       wire:model="dateTo">
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
                            <h5 class="card-title mb-0">Mensajes Enviados</h5>
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
                                        <th>Fecha</th>
                                        <th>Destinatario</th>
                                        <th>Mensaje</th>
                                        <th>Plantilla</th>
                                        <th>Estado</th>
                                        <th>Reintentos</th>
                                        <th>Usuario</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($messages as $message)
                                        <tr>
                                            <td>
                                                <div class="text-muted">
                                                    <div>{{ $message->created_at->format('d/m/Y') }}</div>
                                                    <small>{{ $message->created_at->format('H:i') }}</small>
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
                                                    $message->status === 'delivered' ? 'success' : 
                                                    ($message->status === 'sent' ? 'info' : 
                                                    ($message->status === 'read' ? 'primary' : 
                                                    ($message->status === 'failed' ? 'danger' : 'warning')))
                                                }}">
                                                    {{ ucfirst($message->status) }}
                                                </span>
                                                @if($message->delivered_at)
                                                    <br><small class="text-muted">{{ $message->delivered_at->diffForHumans() }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <span class="badge bg-{{ $message->retry_count > 0 ? 'warning' : 'light' }}">
                                                        {{ $message->retry_count }}
                                                    </span>
                                                </div>
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
                                                    @if($message->status === 'failed')
                                                        <button wire:click="retryMessage({{ $message->id }})" 
                                                                class="btn btn-sm btn-outline-warning"
                                                                title="Reintentar envío">
                                                            <i class="ri ri-refresh-line"></i>
                                                        </button>
                                                    @endif
                                                    <button wire:click="deleteMessage({{ $message->id }})" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Eliminar mensaje"
                                                            onclick="confirm('¿Estás seguro de eliminar este mensaje?') || event.stopImmediatePropagation()">
                                                        <i class="ri ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="ri ri-message-3-line ri-3x text-muted mb-3"></i>
                                                <h6 class="text-muted">No hay mensajes registrados</h6>
                                                <p class="text-muted small">Los mensajes enviados aparecerán aquí</p>
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