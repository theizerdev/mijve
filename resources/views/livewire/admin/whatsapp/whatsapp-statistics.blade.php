<div>
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Estadísticas WhatsApp</h4>
                        <p class="text-muted mb-0">Análisis detallado del rendimiento de mensajes WhatsApp</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ri ri-download-line me-2"></i>
                                Exportar
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" wire:click="exportData('csv')">Exportar CSV</a></li>
                                <li><a class="dropdown-item" href="#" wire:click="exportData('json')">Exportar JSON</a></li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.whatsapp.dashboard') }}" class="btn btn-secondary">
                            <i class="ri ri-arrow-left-line me-2"></i>
                            Volver al Dashboard
                        </a>
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
                                <label for="dateRange" class="form-label">Rango de Fechas</label>
                                <select class="form-select" id="dateRange" wire:model="dateRange">
                                    <option value="7days">Últimos 7 días</option>
                                    <option value="30days">Últimos 30 días</option>
                                    <option value="90days">Últimos 90 días</option>
                                    <option value="1year">Último año</option>
                                    <option value="custom">Personalizado</option>
                                </select>
                            </div>
                            @if($dateRange === 'custom')
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
                            @endif
                            <div class="col-md-2">
                                <label for="userId" class="form-label">Usuario</label>
                                <select class="form-select" id="userId" wire:model="userId">
                                    <option value="">Todos</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Estado</label>
                                <select class="form-select" id="status" wire:model="status">
                                    <option value="">Todos</option>
                                    <option value="sent">Enviado</option>
                                    <option value="delivered">Entregado</option>
                                    <option value="read">Leído</option>
                                    <option value="failed">Fallido</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="button" 
                                        wire:click="resetFilters" 
                                        class="btn btn-outline-secondary w-100">
                                    <i class="ri ri-refresh-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link @if($activeTab === 'overview') active @endif" 
                   wire:click="$set('activeTab', 'overview')" 
                   href="#">
                   <i class="ri ri-dashboard-line me-2"></i>
                    Vista General
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if($activeTab === 'detailed') active @endif" 
                   wire:click="$set('activeTab', 'detailed')" 
                   href="#">
                   <i class="ri ri-file-list-line me-2"></i>
                    Detalle de Mensajes
                </a>
            </li>
        </ul>

        @if($activeTab === 'overview')
            <!-- Overview Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="avatar avatar-sm mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-primary">
                                    <i class="ri ri-message-3-line"></i>
                                </span>
                            </div>
                            <h3 class="mb-1">{{ number_format($statistics['total']) }}</h3>
                            <p class="text-muted mb-0">Total de Mensajes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="avatar avatar-sm mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-success">
                                    <i class="ri ri-send-plane-line"></i>
                                </span>
                            </div>
                            <h3 class="mb-1">{{ number_format($statistics['delivered']) }}</h3>
                            <p class="text-muted mb-0">Mensajes Entregados</p>
                            <small class="text-success">{{ $statistics['delivery_rate'] }}%</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="avatar avatar-sm mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-info">
                                    <i class="ri ri-eye-line"></i>
                                </span>
                            </div>
                            <h3 class="mb-1">{{ number_format($statistics['read']) }}</h3>
                            <p class="text-muted mb-0">Mensajes Leídos</p>
                            <small class="text-info">{{ $statistics['read_rate'] }}%</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="avatar avatar-sm mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-danger">
                                    <i class="ri ri-error-warning-line"></i>
                                </span>
                            </div>
                            <h3 class="mb-1">{{ number_format($statistics['failed']) }}</h3>
                            <p class="text-muted mb-0">Mensajes Fallidos</p>
                            <small class="text-danger">{{ $statistics['failure_rate'] }}%</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Metrics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Uso de Plantillas</h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1">{{ number_format($statistics['template_usage']) }}</h4>
                                    <small class="text-muted">Mensajes con plantilla</small>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted">{{ $statistics['total'] > 0 ? round(($statistics['template_usage'] / $statistics['total']) * 100, 1) : 0 }}%</div>
                                    <small class="text-muted">del total</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Mensajes Manuales</h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1">{{ number_format($statistics['manual_messages']) }}</h4>
                                    <small class="text-muted">Mensajes manuales</small>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted">{{ $statistics['total'] > 0 ? round(($statistics['manual_messages'] / $statistics['total']) * 100, 1) : 0 }}%</div>
                                    <small class="text-muted">del total</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Mensajes Programados</h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1">{{ number_format($statistics['scheduled_sent']) }}</h4>
                                    <small class="text-muted">Enviados exitosamente</small>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted">{{ $statistics['scheduled'] > 0 ? round(($statistics['scheduled_sent'] / $statistics['scheduled']) * 100, 1) : 0 }}%</div>
                                    <small class="text-muted">de programados</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h6 class="card-title mb-0">Tendencia de Mensajes</h6>
                        </div>
                        <div class="card-body">
                            <div style="height: 300px;">
                                <canvas id="messagesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h6 class="card-title mb-0">Distribución por Estado</h6>
                        </div>
                        <div class="card-body">
                            <div style="height: 300px;">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Users and Templates -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h6 class="card-title mb-0">Usuarios Más Activos</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Usuario</th>
                                            <th class="text-end">Mensajes</th>
                                            <th class="text-end">Entregados</th>
                                            <th class="text-end">Tasa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($statistics['top_users'] as $user)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ri ri-user-line me-2 text-muted"></i>
                                                        {{ $user->user->name ?? 'Usuario eliminado' }}
                                                    </div>
                                                </td>
                                                <td class="text-end">{{ number_format($user->total_messages) }}</td>
                                                <td class="text-end">{{ number_format($user->delivered_messages) }}</td>
                                                <td class="text-end">
                                                    <span class="badge bg-{{ $user->total_messages > 0 ? ($user->delivered_messages / $user->total_messages > 0.8 ? 'success' : 'warning') : 'secondary' }}">
                                                        {{ $user->total_messages > 0 ? round(($user->delivered_messages / $user->total_messages) * 100, 1) : 0 }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h6 class="card-title mb-0">Plantillas Más Usadas</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Plantilla</th>
                                            <th class="text-end">Uso</th>
                                            <th class="text-end">Tasa Entrega</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($statistics['top_templates'] as $template)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ri ri-file-text-line me-2 text-muted"></i>
                                                        {{ $template->template->name ?? 'Plantilla eliminada' }}
                                                    </div>
                                                </td>
                                                <td class="text-end">{{ number_format($template->usage_count) }}</td>
                                                <td class="text-end">
                                                    <span class="badge bg-{{ $template->delivery_rate > 80 ? 'success' : 'warning' }}">
                                                        {{ round($template->delivery_rate, 1) }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Detailed Messages Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">Detalle de Mensajes</h6>
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
                                            <th>Usuario</th>
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
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <i class="ri ri-file-list-line ri-3x text-muted mb-3"></i>
                                                    <h6 class="text-muted">No hay mensajes registrados</h6>
                                                    <p class="text-muted small">Los mensajes aparecerán aquí según los filtros aplicados</p>
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
        @endif
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

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            // Initialize charts when component loads
            initializeCharts();
            
            // Reinitialize charts when Livewire updates
            Livewire.hook('message.processed', (message, component) => {
                initializeCharts();
            });
        });

        function initializeCharts() {
            // Messages trend chart
            const messagesCtx = document.getElementById('messagesChart');
            if (messagesCtx) {
                const dailyStats = @json($statistics['daily_stats']);
                
                new Chart(messagesCtx, {
                    type: 'line',
                    data: {
                        labels: dailyStats.map(stat => stat.date),
                        datasets: [{
                            label: 'Total',
                            data: dailyStats.map(stat => stat.total),
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }, {
                            label: 'Enviados',
                            data: dailyStats.map(stat => stat.sent),
                            borderColor: 'rgb(54, 162, 235)',
                            tension: 0.1
                        }, {
                            label: 'Entregados',
                            data: dailyStats.map(stat => stat.delivered),
                            borderColor: 'rgb(255, 99, 132)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Status distribution chart
            const statusCtx = document.getElementById('statusChart');
            if (statusCtx) {
                const stats = @json($statistics);
                
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Entregados', 'Leídos', 'Fallidos', 'Pendientes'],
                        datasets: [{
                            data: [
                                stats.delivered,
                                stats.read,
                                stats.failed,
                                stats.sent - stats.delivered
                            ],
                            backgroundColor: [
                                'rgb(75, 192, 192)',
                                'rgb(54, 162, 235)',
                                'rgb(255, 99, 132)',
                                'rgb(255, 205, 86)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });
            }
        }
    </script>
    @endpush
</div>