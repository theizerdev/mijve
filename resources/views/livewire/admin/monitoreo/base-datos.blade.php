<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="ri ri-database-2-line me-2"></i>Monitoreo de Base de Datos</h4>
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
                                <i class="ri ri-database-line ri-26px"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $dbInfo['database'] }}</h5>
                            <small class="text-muted">Base de Datos</small>
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
                                <i class="ri ri-table-line ri-26px"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $dbInfo['total_tables'] }}</h5>
                            <small class="text-muted">Total Tablas</small>
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
                                <i class="ri ri-hard-drive-2-line ri-26px"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $dbInfo['total_size'] }} MB</h5>
                            <small class="text-muted">Tamaño Total</small>
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
                                <i class="ri ri-server-line ri-26px"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $dbInfo['connection'] }}</h5>
                            <small class="text-muted">Motor</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Estadísticas de Tablas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tabla</th>
                                    <th>Registros</th>
                                    <th>Tamaño (MB)</th>
                                    <th>Motor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tableStats as $table)
                                <tr>
                                    <td><code>{{ $table['name'] }}</code></td>
                                    <td>{{ number_format($table['rows']) }}</td>
                                    <td>{{ $table['size'] }}</td>
                                    <td><span class="badge bg-label-primary">{{ $table['engine'] }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
