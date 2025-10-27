<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="ri ri-server-line me-2"></i>Monitoreo del Servidor</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <div class="avatar-initial bg-label-primary rounded-3">
                                <i class="ri ri-cpu-line ri-26px"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $serverInfo['memory_usage'] }} MB</h5>
                            <small class="text-muted">Uso de Memoria</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <div class="avatar-initial bg-label-success rounded-3">
                                <i class="ri ri-hard-drive-line ri-26px"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $serverInfo['disk_free_space'] }} GB</h5>
                            <small class="text-muted">Espacio Libre</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <div class="avatar-initial bg-label-warning rounded-3">
                                <i class="ri ri-database-2-line ri-26px"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $serverInfo['disk_usage_percent'] }}%</h5>
                            <small class="text-muted">Uso de Disco</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <div class="avatar-initial bg-label-info rounded-3">
                                <i class="ri ri-time-line ri-26px"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $serverInfo['max_execution_time'] }}s</h5>
                            <small class="text-muted">Tiempo Máx. Ejecución</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Información del Sistema</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-medium">Sistema Operativo:</td>
                            <td>{{ $serverInfo['server_os'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Servidor:</td>
                            <td>{{ $serverInfo['server_software'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">PHP:</td>
                            <td>{{ $serverInfo['php_version'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Laravel:</td>
                            <td>{{ $serverInfo['laravel_version'] }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Configuración PHP</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-medium">Límite de Memoria:</td>
                            <td>{{ $serverInfo['memory_limit'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Tiempo Máx. Ejecución:</td>
                            <td>{{ $serverInfo['max_execution_time'] }} segundos</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Tamaño Máx. Upload:</td>
                            <td>{{ $serverInfo['upload_max_filesize'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Tamaño Máx. POST:</td>
                            <td>{{ $serverInfo['post_max_size'] }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
