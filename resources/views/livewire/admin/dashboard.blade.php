<div>
    <!-- Saludo y Bienvenida -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-white mb-1">¡Hola, {{ auth()->user()->name }}! 👋</h4>
                            <p class="mb-0 opacity-75">
                                @if($isAdmin)
                                    Panel de Administración General — {{ now()->translatedFormat('l, d \\d\\e F Y') }}
                                @else
                                    Panel de tu Extensión — {{ now()->translatedFormat('l, d \\d\\e F Y') }}
                                @endif
                            </p>
                        </div>
                        <div class="d-none d-md-flex align-items-center gap-3">
                            <div class="text-end">
                                <small class="d-block opacity-75">Rol actual</small>
                                <span class="badge bg-white text-primary">
                                    {{ auth()->user()->roles->first()?->name ?? 'Sin rol' }}
                                </span>
                            </div>
                            <div class="avatar avatar-lg">
                                <span class="avatar-initial rounded-circle bg-white text-primary fw-bold">
                                    {{ auth()->user()->initials }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($isLider && isset($stats['mi_extension']))
    <!-- Extensión del Líder -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-start border-primary border-4">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md me-3">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-building-line ri-24px"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $stats['mi_extension']->nombre ?? 'Sin Asignar' }}</h5>
                            <small class="text-muted">
                                <i class="ri ri-map-pin-line me-1"></i>{{ $stats['mi_extension']->zona ?? 'N/A' }} — Distrito {{ $stats['mi_extension']->distrito ?? 'N/A' }}
                            </small>
                        </div>
                        <span class="badge bg-label-success ms-auto">Activa</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Widgets de Estadísticas Principales -->
    <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Total Participantes</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_participantes'] ?? 0) }}</h3>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-group-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        @php $trend = $stats['tendencia_participantes'] ?? 0; @endphp
                        <span class="badge bg-label-{{ $trend >= 0 ? 'success' : 'danger' }} me-1">
                            <i class="ri ri-arrow-{{ $trend >= 0 ? 'up' : 'down' }}-s-line"></i>
                            {{ abs($trend) }}%
                        </span>
                        <small class="text-muted">vs mes anterior</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Recaudación Total</p>
                            <h3 class="mb-0 fw-bold">${{ number_format($stats['total_pagos'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-money-dollar-circle-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="ri ri-calendar-line me-1"></i>Este mes: ${{ number_format($stats['pagos_mes'] ?? 0, 2) }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Pagos Aprobados</p>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($stats['pagos_aprobados'] ?? 0) }}</h3>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ri ri-checkbox-circle-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-warning">
                            <i class="ri ri-time-line me-1"></i>{{ $stats['pagos_pendientes'] ?? 0 }} pendientes
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            @if($isAdmin)
                                <p class="text-muted mb-1 small">Extensiones Activas</p>
                                <h3 class="mb-0 fw-bold">{{ number_format($stats['total_extensiones'] ?? 0) }}</h3>
                            @else
                                <p class="text-muted mb-1 small">Actividades</p>
                                <h3 class="mb-0 fw-bold">{{ number_format($stats['total_actividades'] ?? 0) }}</h3>
                            @endif
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ri ri-{{ $isAdmin ? 'building-2-line' : 'calendar-event-line' }} ri-24px"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="ri ri-user-add-line me-1"></i>{{ $stats['participantes_mes'] ?? 0 }} nuevos este mes
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <!-- Gráfico de Pagos Mensuales -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center pb-0">
                    <h5 class="card-title mb-0">Recaudación Mensual (EUR)</h5>
                    <small class="text-muted">Últimos 6 meses</small>
                </div>
                <div class="card-body">
                    <div id="monthlyPaymentsChart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Estado de Pagos -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h5 class="card-title mb-0">Estado de Pagos</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div id="paymentStatusChart" style="min-height: 250px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda fila de gráficos + Acciones Rápidas -->
    <div class="row">
        <!-- Participantes por Extensión -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center pb-0">
                    <h5 class="card-title mb-0">Participantes por Extensión</h5>
                    @if($isAdmin)
                        <span class="badge bg-label-primary">Top 10</span>
                    @endif
                </div>
                <div class="card-body">
                    <div id="participantsByExtensionChart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h5 class="card-title mb-0">Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('create participantes')
                        <a href="{{ route('admin.participantes.create') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                            <i class="ri ri-user-add-line"></i> Nuevo Participante
                        </a>
                        @endcan

                        @can('create pagos')
                        <a href="{{ route('admin.pagos.create') }}" class="btn btn-outline-success d-flex align-items-center gap-2">
                            <i class="ri ri-money-dollar-circle-line"></i> Registrar Pago
                        </a>
                        @endcan

                        @can('access participantes')
                        <a href="{{ route('admin.participantes.index') }}" class="btn btn-outline-info d-flex align-items-center gap-2">
                            <i class="ri ri-group-line"></i> Ver Participantes
                        </a>
                        @endcan

                        @can('access pagos')
                        <a href="{{ route('admin.pagos.index') }}" class="btn btn-outline-warning d-flex align-items-center gap-2">
                            <i class="ri ri-file-list-3-line"></i> Ver Pagos
                        </a>
                        @endcan

                        @if($isAdmin)
                        @can('access extensiones')
                        <a href="{{ route('admin.extensiones.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                            <i class="ri ri-building-2-line"></i> Gestionar Extensiones
                        </a>
                        @endcan

                        @can('access actividades')
                        <a href="{{ route('admin.actividades.index') }}" class="btn btn-outline-dark d-flex align-items-center gap-2">
                            <i class="ri ri-calendar-todo-line"></i> Gestionar Actividades
                        </a>
                        @endcan
                        @endif
                    </div>

                    <!-- Resumen rápido -->
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="mb-3 text-muted">Resumen Semanal</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Pagos esta semana</span>
                            <span class="badge bg-label-primary">{{ $stats['pagos_semana'] ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Nuevos este mes</span>
                            <span class="badge bg-label-success">{{ $stats['participantes_mes'] ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Pendientes</span>
                            <span class="badge bg-label-warning">{{ $stats['pagos_pendientes'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tablas de Datos Recientes -->
    <div class="row">
        <!-- Participantes Recientes -->
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Últimos Participantes</h5>
                    @can('access participantes')
                    <a href="{{ route('admin.participantes.index') }}" class="btn btn-sm btn-outline-primary">
                        Ver Todos <i class="ri ri-arrow-right-s-line"></i>
                    </a>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Cédula</th>
                                @if($isAdmin)
                                    <th>Extensión</th>
                                @endif
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentParticipants as $participante)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ mb_substr($participante->nombres, 0, 1) }}{{ mb_substr($participante->apellidos, 0, 1) }}
                                                </span>
                                            </div>
                                            <span class="fw-medium">{{ $participante->nombres }} {{ $participante->apellidos }}</span>
                                        </div>
                                    </td>
                                    <td><small>{{ $participante->cedula ?? 'N/A' }}</small></td>
                                    @if($isAdmin)
                                        <td>
                                            <span class="badge bg-label-info">{{ $participante->extension->nombre ?? 'N/A' }}</span>
                                        </td>
                                    @endif
                                    <td><small class="text-muted">{{ $participante->created_at->format('d/m/Y') }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isAdmin ? 4 : 3 }}" class="text-center py-4">
                                        <i class="ri ri-user-line ri-2x text-muted mb-2 d-block"></i>
                                        <span class="text-muted">No hay participantes recientes</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagos Recientes -->
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Últimos Pagos</h5>
                    @can('access pagos')
                    <a href="{{ route('admin.pagos.index') }}" class="btn btn-sm btn-outline-primary">
                        Ver Todos <i class="ri ri-arrow-right-s-line"></i>
                    </a>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Participante</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $pago)
                                <tr>
                                    <td>
                                        <span class="fw-medium">{{ $pago->participante->nombres ?? 'N/A' }} {{ $pago->participante->apellidos ?? '' }}</span>
                                    </td>
                                    <td><span class="fw-medium">${{ number_format($pago->monto_euro, 2) }}</span></td>
                                    <td>
                                        @php
                                            $statusColors = ['Aprobado' => 'success', 'Pendiente' => 'warning', 'Rechazado' => 'danger'];
                                        @endphp
                                        <span class="badge bg-label-{{ $statusColors[$pago->status] ?? 'secondary' }}">
                                            {{ $pago->status }}
                                        </span>
                                    </td>
                                    <td><small class="text-muted">{{ $pago->fecha_pago?->format('d/m/Y') ?? 'N/A' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="ri ri-money-dollar-circle-line ri-2x text-muted mb-2 d-block"></i>
                                        <span class="text-muted">No hay pagos recientes</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($isAdmin && count($recentActivity) > 0)
    <!-- Actividad Reciente (Solo Admin) -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Actividad Reciente del Sistema</h5>
                    @can('access activity log')
                    <a href="{{ route('admin.activity-log') }}" class="btn btn-sm btn-outline-primary">
                        Ver Todo <i class="ri ri-arrow-right-s-line"></i>
                    </a>
                    @endcan
                </div>
                <div class="card-body p-0">
                    <div class="timeline-wrapper px-4 py-3">
                        @foreach($recentActivity as $activity)
                            <div class="d-flex align-items-start mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                                <div class="avatar avatar-sm me-3 flex-shrink-0">
                                    @php
                                        $activityIcons = [
                                            'created' => ['icon' => 'ri-add-circle-line', 'color' => 'success'],
                                            'updated' => ['icon' => 'ri-edit-line', 'color' => 'info'],
                                            'deleted' => ['icon' => 'ri-delete-bin-line', 'color' => 'danger'],
                                        ];
                                        $actInfo = $activityIcons[$activity->description] ?? ['icon' => 'ri-information-line', 'color' => 'secondary'];
                                    @endphp
                                    <span class="avatar-initial rounded-circle bg-label-{{ $actInfo['color'] }}">
                                        <i class="ri {{ $actInfo['icon'] }}"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0 fw-medium">
                                            {{ ucfirst($activity->description) }}
                                            <span class="text-muted">—</span>
                                            <small class="text-muted">{{ class_basename($activity->subject_type ?? '') }}</small>
                                        </p>
                                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                    <small class="text-muted">
                                        Por: {{ $activity->causer?->name ?? 'Sistema' }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim() || '#7367F0';
    const successColor = '#28C76F';
    const warningColor = '#FF9F43';
    const dangerColor = '#EA5455';
    const infoColor = '#00CFE8';

    // Gráfico de Pagos Mensuales
    const monthlyLabels = @json($monthlyPaymentsChart['labels']);
    const monthlyData = @json($monthlyPaymentsChart['data']);

    if (monthlyLabels.length > 0) {
        new ApexCharts(document.querySelector('#monthlyPaymentsChart'), {
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif',
            },
            series: [{
                name: 'Recaudación (EUR)',
                data: monthlyData
            }],
            xaxis: {
                categories: monthlyLabels,
                labels: { style: { fontSize: '12px' } }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return '$' + val.toLocaleString('es-ES', { minimumFractionDigits: 0 });
                    }
                }
            },
            colors: [primaryColor],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.5,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            stroke: { curve: 'smooth', width: 3 },
            dataLabels: { enabled: false },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return '$' + val.toLocaleString('es-ES', { minimumFractionDigits: 2 });
                    }
                }
            },
            grid: { borderColor: '#f1f1f1', strokeDashArray: 3 }
        }).render();
    } else {
        document.querySelector('#monthlyPaymentsChart').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted"><p>Sin datos de pagos disponibles</p></div>';
    }

    // Gráfico de Estado de Pagos
    const statusLabels = @json($paymentStatusChart['labels']);
    const statusData = @json($paymentStatusChart['data']);

    if (statusLabels.length > 0 && statusData.some(v => v > 0)) {
        new ApexCharts(document.querySelector('#paymentStatusChart'), {
            chart: {
                type: 'donut',
                height: 250,
                fontFamily: 'Inter, sans-serif',
            },
            series: statusData,
            labels: statusLabels,
            colors: [warningColor, successColor, dangerColor],
            legend: {
                position: 'bottom',
                fontSize: '13px',
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '14px',
                                fontWeight: 600,
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return Math.round(val) + '%';
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: { height: 200 },
                    legend: { position: 'bottom' }
                }
            }]
        }).render();
    } else {
        document.querySelector('#paymentStatusChart').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted"><p>Sin datos de pagos</p></div>';
    }

    // Gráfico de Participantes por Extensión
    const extLabels = @json($participantsByExtensionChart['labels']);
    const extData = @json($participantsByExtensionChart['data']);

    if (extLabels.length > 0) {
        new ApexCharts(document.querySelector('#participantsByExtensionChart'), {
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif',
            },
            series: [{
                name: 'Participantes',
                data: extData
            }],
            xaxis: {
                categories: extLabels,
                labels: {
                    style: { fontSize: '11px' },
                    rotate: -45,
                    rotateAlways: extLabels.length > 5,
                    trim: true,
                    maxHeight: 80
                }
            },
            colors: [infoColor],
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: extLabels.length > 5 ? '60%' : '40%',
                    distributed: false,
                }
            },
            dataLabels: {
                enabled: true,
                style: { fontSize: '11px', fontWeight: 600 }
            },
            grid: { borderColor: '#f1f1f1', strokeDashArray: 3 },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + ' participantes';
                    }
                }
            }
        }).render();
    } else {
        document.querySelector('#participantsByExtensionChart').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted"><p>Sin datos de extensiones</p></div>';
    }
});
</script>
@endpush
