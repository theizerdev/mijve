<div wire:poll.5s="$dispatch('refresh-servidor')">
    <!-- Header con indicador de estado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <div class="avatar-initial bg-success bg-opacity-20 rounded">
                                    <i class="ri ri-database-line ri-20px text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="mb-0 text-white">Monitoreo del Servidor</h4>
                                <small class="text-white-50">Estado del sistema en tiempo real</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm text-white me-2" role="status" style="width: 12px; height: 12px;">
                                    <span class="visually-hidden">Actualizando...</span>
                                </div>
                                <small class="text-white-75">{{ $lastUpdate }}</small>
                            </div>
                            <div class="d-flex align-items-center mt-1">
                                <span class="badge bg-success bg-opacity-20 text-white border border-success border-opacity-20">
                                    <i class="ri ri-checkbox-circle-line me-1"></i>Sistema Operativo
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas principales con progreso -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="avatar">
                            <div class="avatar-initial bg-primary bg-opacity-10 text-primary rounded">
                                <i class="ri ri-cpu-line ri-24px"></i>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ri ri-more-2-line"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i class="ri ri-refresh-line me-2"></i>Actualizar</a></li>
                                <li><a class="dropdown-item" href="#"><i class="ri ri-information-line me-2"></i>Detalles</a></li>
                            </ul>
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-1 text-primary">{{ $serverInfo['memory_usage'] }} MB</h4>
                        <p class="text-muted mb-2">Uso de Memoria RAM</p>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ min(($serverInfo['memory_usage'] / (int)$serverInfo['memory_limit']) * 100, 100) }}%"></div>
                        </div>
                        <small class="text-muted">Límite: {{ $serverInfo['memory_limit'] }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="avatar">
                            <div class="avatar-initial bg-success bg-opacity-10 text-success rounded">
                                <i class="ri ri-hard-drive-line ri-24px"></i>
                            </div>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success">Disponible</span>
                    </div>
                    <div>
                        <h4 class="mb-1 text-success">{{ $serverInfo['disk_free_space'] }} GB</h4>
                        <p class="text-muted mb-2">Espacio Libre en Disco</p>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ 100 - $serverInfo['disk_usage_percent'] }}%"></div>
                        </div>
                        <small class="text-muted">Total: {{ $serverInfo['disk_total_space'] }} GB</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="avatar">
                            <div class="avatar-initial {{ $serverInfo['disk_usage_percent'] > 80 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-warning bg-opacity-10 text-warning' }} rounded">
                                <i class="ri ri-pie-chart-line ri-24px"></i>
                            </div>
                        </div>
                        <span class="badge {{ $serverInfo['disk_usage_percent'] > 80 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-warning bg-opacity-10 text-warning' }}">
                            {{ $serverInfo['disk_usage_percent'] > 80 ? 'Crítico' : 'Normal' }}
                        </span>
                    </div>
                    <div>
                        <h4 class="mb-1 {{ $serverInfo['disk_usage_percent'] > 80 ? 'text-danger' : 'text-warning' }}">{{ $serverInfo['disk_usage_percent'] }}%</h4>
                        <p class="text-muted mb-2">Uso de Disco</p>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar {{ $serverInfo['disk_usage_percent'] > 80 ? 'bg-danger' : 'bg-warning' }}" style="width: {{ $serverInfo['disk_usage_percent'] }}%"></div>
                        </div>
                        <small class="text-muted">Usado: {{ $serverInfo['disk_total_space'] - $serverInfo['disk_free_space'] }} GB</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="avatar">
                            <div class="avatar-initial bg-info bg-opacity-10 text-info rounded">
                                <i class="ri ri-timer-line ri-24px"></i>
                            </div>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info">Configurado</span>
                    </div>
                    <div>
                        <h4 class="mb-1 text-info">{{ $serverInfo['max_execution_time'] }}s</h4>
                        <p class="text-muted mb-2">Tiempo Máx. Ejecución</p>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ min(($serverInfo['max_execution_time'] / 300) * 100, 100) }}%"></div>
                        </div>
                        <small class="text-muted">Límite recomendado: 300s</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información detallada del sistema -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <div class="avatar-initial bg-primary bg-opacity-10 text-primary rounded">
                                <i class="ri ri-computer-line ri-18px"></i>
                            </div>
                        </div>
                        <h5 class="mb-0">Información del Sistema</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded mt-4">
                                <div class="avatar avatar-sm me-3">
                                    <div class="avatar-initial bg-primary bg-opacity-10 text-primary rounded">
                                        <i class="ri ri-ubuntu-line ri-18px"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">Sistema Operativo</h6>
                                    <small class="text-muted">{{ $serverInfo['server_os'] }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded mt-4">
                                <div class="avatar avatar-sm me-3">
                                    <div class="avatar-initial bg-success bg-opacity-10 text-success rounded">
                                        <i class="ri ri-server-line ri-18px"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">Servidor Web</h6>
                                    <small class="text-muted">{{ $serverInfo['server_software'] }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded mt-4">
                                <div class="avatar avatar-sm me-3">
                                    <div class="avatar-initial bg-warning bg-opacity-10 text-warning rounded">
                                        <i class="ri ri-code-s-slash-line ri-18px"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">PHP</h6>
                                    <small class="text-muted">v{{ $serverInfo['php_version'] }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded mt-4">
                                <div class="avatar avatar-sm me-3">
                                    <div class="avatar-initial bg-danger bg-opacity-10 text-danger rounded">
                                        <i class="ri ri-flashlight-line ri-18px"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">Laravel</h6>
                                    <small class="text-muted">v{{ $serverInfo['laravel_version'] }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <div class="avatar-initial bg-info bg-opacity-10 text-info rounded">
                                <i class="ri ri-settings-3-line ri-18px"></i>
                            </div>
                        </div>
                        <h5 class="mb-0">Configuración PHP</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded mt-4">
                        <div class="d-flex align-items-center">
                            <i class="ri ri-cpu-line text-primary me-2"></i>
                            <span class="fw-medium">Memoria</span>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary">{{ $serverInfo['memory_limit'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded mt-4">
                        <div class="d-flex align-items-center">
                            <i class="ri ri-timer-line text-info me-2"></i>
                            <span class="fw-medium">Ejecución</span>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info">{{ $serverInfo['max_execution_time'] }}s</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded mt-4">
                        <div class="d-flex align-items-center">
                            <i class="ri ri-upload-line text-success me-2"></i>
                            <span class="fw-medium">Upload</span>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success">{{ $serverInfo['upload_max_filesize'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded mt-4">
                        <div class="d-flex align-items-center">
                            <i class="ri ri-mail-send-line text-warning me-2"></i>
                            <span class="fw-medium">POST</span>
                        </div>
                        <span class="badge bg-warning bg-opacity-10 text-warning">{{ $serverInfo['post_max_size'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
