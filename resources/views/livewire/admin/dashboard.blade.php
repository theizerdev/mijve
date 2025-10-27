<div>
    <!-- Selector de Rango de Fechas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h4 class="mb-1">Panel de Control de Accesos</h4>
                            <p class="mb-0 text-muted">Monitoreo en tiempo real del sistema</p>
                        </div>
                        <div class="btn-group" role="group">
                            <button wire:click="$set('dateRange', 'week')" class="btn btn-sm {{ $dateRange === 'week' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="ri ri-calendar-line me-1"></i>Semana
                            </button>
                            <button wire:click="$set('dateRange', 'month')" class="btn btn-sm {{ $dateRange === 'month' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="ri ri-calendar-2-line me-1"></i>Mes
                            </button>
                            <button wire:click="$set('dateRange', 'quarter')" class="btn btn-sm {{ $dateRange === 'quarter' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="ri ri-calendar-check-line me-1"></i>Trimestre
                            </button>
                            <button wire:click="$set('dateRange', 'year')" class="btn btn-sm {{ $dateRange === 'year' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="ri ri-calendar-event-line me-1"></i>Año
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas Principales -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded-3">
                                <i class="ri ri-user-3-line ri-26px"></i>
                            </div>
                        </div>
                        @if($comparisonData['students']['change'] != 0)
                        <div class="d-flex align-items-center">
                            <p class="mb-0 {{ $comparisonData['students']['change'] > 0 ? 'text-success' : 'text-danger' }} me-1">
                                {{ $comparisonData['students']['change'] > 0 ? '+' : '' }}{{ $comparisonData['students']['change'] }}%
                            </p>
                            <i class="ri ri-arrow-{{ $comparisonData['students']['change'] > 0 ? 'up' : 'down' }}-s-line {{ $comparisonData['students']['change'] > 0 ? 'text-success' : 'text-danger' }}"></i>
                        </div>
                        @endif
                    </div>
                    <div class="card-info mt-4">
                        <h4 class="mb-1">{{ number_format($totalStudents) }}</h4>
                        <p class="mb-0">Estudiantes Activos</p>
                        <small class="text-muted">Total registrados</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded-3">
                                <i class="ri ri-login-box-line ri-26px"></i>
                            </div>
                        </div>
                        @if($comparisonData['access']['change'] != 0)
                        <div class="d-flex align-items-center">
                            <p class="mb-0 {{ $comparisonData['access']['change'] > 0 ? 'text-success' : 'text-danger' }} me-1">
                                {{ $comparisonData['access']['change'] > 0 ? '+' : '' }}{{ $comparisonData['access']['change'] }}%
                            </p>
                            <i class="ri ri-arrow-{{ $comparisonData['access']['change'] > 0 ? 'up' : 'down' }}-s-line {{ $comparisonData['access']['change'] > 0 ? 'text-success' : 'text-danger' }}"></i>
                        </div>
                        @endif
                    </div>
                    <div class="card-info mt-4">
                        <h4 class="mb-1">{{ number_format($todayEntries) }}</h4>
                        <p class="mb-0">Entradas Hoy</p>
                        <small class="text-muted">Registros del día</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-danger rounded-3">
                                <i class="ri ri-logout-box-line ri-26px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h4 class="mb-1">{{ number_format($todayExits) }}</h4>
                        <p class="mb-0">Salidas Hoy</p>
                        <small class="text-muted">Registros del día</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-warning rounded-3">
                                <i class="ri ri-building-line ri-26px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h4 class="mb-1">{{ number_format($studentsInside) }}</h4>
                        <p class="mb-0">Estudiantes Dentro</p>
                        <small class="text-muted">En el plantel ahora</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos Principales -->
    <div class="row g-4 mb-4">
        <!-- Gráfico de Accesos por Período -->
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Entradas y Salidas</h5>
                        <p class="mb-0 text-muted">Comparativa por 
                            @if($dateRange === 'week') día
                            @elseif($dateRange === 'month') semana
                            @elseif($dateRange === 'quarter') mes
                            @else mes
                            @endif
                        </p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-text-secondary rounded-pill btn-icon" type="button" data-bs-toggle="dropdown">
                            <i class="ri ri-more-2-line"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('admin.access.students') }}"><i class="ri ri-qr-scan-2-line me-2"></i>Control de Acceso</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.students.index') }}"><i class="ri ri-user-line me-2"></i>Ver Estudiantes</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div id="entriesExitsChart"></div>
                    <script class="entries-exits-data" type="application/json">
                        @json($entriesExitsByPeriod)
                    </script>
                </div>
            </div>
        </div>

        <!-- Gráfico de Tipo de Acceso -->
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-1">Distribución de Accesos</h5>
                    <p class="mb-0 text-muted">Período seleccionado</p>
                </div>
                <div class="card-body">
                    <div id="accessTypeChart"></div>
                    <script class="access-type-data" type="application/json">
                        @json($accessByType)
                    </script>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded bg-label-success"><i class="ri ri-login-box-line"></i></span>
                                </div>
                                <span>Entradas</span>
                            </div>
                            <strong>{{ number_format($accessByType['entries']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded bg-label-danger"><i class="ri ri-logout-box-line"></i></span>
                                </div>
                                <span>Salidas</span>
                            </div>
                            <strong>{{ number_format($accessByType['exits']) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estudiantes y Horarios -->
    <div class="row g-4 mb-4">
        <!-- Estudiantes por Grado -->
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-1">Estudiantes por Grado</h5>
                    <p class="mb-0 text-muted">Distribución actual</p>
                </div>
                <div class="card-body">
                    <div id="studentsGradeChart"></div>
                    <script class="students-grade-data" type="application/json">
                        @json($studentsByGrade)
                    </script>
                </div>
            </div>
        </div>

        <!-- Estudiantes por Nivel Educativo -->
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-1">Por Nivel Educativo</h5>
                    <p class="mb-0 text-muted">Distribución por nivel</p>
                </div>
                <div class="card-body">
                    <div id="studentsLevelChart"></div>
                    <script class="students-level-data" type="application/json">
                        @json($studentsByLevel)
                    </script>
                </div>
            </div>
        </div>

        <!-- Horarios Pico -->
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-1">Horarios Pico</h5>
                    <p class="mb-0 text-muted">Mayor actividad</p>
                </div>
                <div class="card-body">
                    @forelse($peakHours as $peak)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded bg-label-primary"><i class="ri ri-time-line"></i></span>
                            </div>
                            <span>{{ $peak['hour'] }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <strong class="me-2">{{ $peak['count'] }}</strong>
                            <small class="text-muted">accesos</small>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted">No hay datos disponibles</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos Recientes y Top Estudiantes -->
    <div class="row g-4">
        <!-- Accesos Recientes -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Accesos Recientes</h5>
                        <p class="mb-0 text-muted">Últimos 10 registros</p>
                    </div>
                    <a href="{{ route('admin.access.students') }}" class="btn btn-sm btn-primary">
                        <i class="ri ri-qr-scan-2-line me-1"></i>Control de Acceso
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Código</th>
                                    <th>Tipo</th>
                                    <th>Hora</th>
                                    <th>Registrado Por</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAccess as $access)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                @if($access->student->foto)
                                                <img src="{{ asset('storage/' . $access->student->foto) }}" alt="Avatar" class="rounded-circle">
                                                @else
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($access->student->nombres, 0, 1) }}{{ substr($access->student->apellidos, 0, 1) }}
                                                </span>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="fw-medium">{{ $access->student->nombres }} {{ $access->student->apellidos }}</span>
                                                <small class="text-muted d-block">{{ $access->student->grado }} - {{ $access->student->seccion }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><code>{{ $access->student->codigo }}</code></td>
                                    <td>
                                        @if($access->type === 'entrada')
                                        <span class="badge bg-label-success"><i class="ri ri-login-box-line me-1"></i>Entrada</span>
                                        @else
                                        <span class="badge bg-label-danger"><i class="ri ri-logout-box-line me-1"></i>Salida</span>
                                        @endif
                                    </td>
                                    <td>{{ $access->access_time->format('H:i:s') }}</td>
                                    <td>{{ $access->registeredBy->name }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="ri ri-inbox-line ri-24px d-block mb-2"></i>
                                        No hay accesos recientes
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Estudiantes -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-1">Top Estudiantes</h5>
                    <p class="mb-0 text-muted">Mayor actividad</p>
                </div>
                <div class="card-body">
                    @forelse($topStudents as $index => $item)
                    <div class="d-flex justify-content-between align-items-center mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded bg-label-{{ $index === 0 ? 'warning' : ($index === 1 ? 'info' : 'secondary') }}">
                                    {{ $index + 1 }}
                                </span>
                            </div>
                            <div>
                                <span class="fw-medium d-block">{{ $item->student->nombres }} {{ $item->student->apellidos }}</span>
                                <small class="text-muted">{{ $item->student->grado }} - {{ $item->student->seccion }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <strong class="d-block">{{ $item->access_count }}</strong>
                            <small class="text-muted">accesos</small>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted">No hay datos disponibles</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        window.dashboardCharts = {};

        function destroyCharts() {
            Object.keys(window.dashboardCharts).forEach(chartName => {
                try {
                    if (window.dashboardCharts[chartName]) {
                        window.dashboardCharts[chartName].destroy();
                    }
                } catch (e) {
                    console.log('Error destroying chart:', chartName, e);
                }
            });
            window.dashboardCharts = {};
        }

        function renderCharts() {
            destroyCharts();

            setTimeout(() => {
                // Gráfico de Entradas y Salidas
                const entriesExitsEl = document.querySelector('#entriesExitsChart');
                if (entriesExitsEl) {
                    entriesExitsEl.innerHTML = '';
                    const entriesExitsData = JSON.parse(entriesExitsEl.closest('.card-body').querySelector('script.entries-exits-data').innerHTML);
                    
                    const entriesExitsOptions = {
                        series: [{
                            name: 'Entradas',
                            data: entriesExitsData.entries
                        }, {
                            name: 'Salidas',
                            data: entriesExitsData.exits
                        }],
                        chart: {
                            height: 300,
                            type: 'line',
                            toolbar: { show: false }
                        },
                        colors: ['#28a745', '#dc3545'],
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 3 },
                        xaxis: {
                            categories: entriesExitsData.labels
                        },
                        yaxis: { min: 0 },
                        legend: { position: 'top' }
                    };
                    window.dashboardCharts.entriesExitsChart = new ApexCharts(entriesExitsEl, entriesExitsOptions);
                    window.dashboardCharts.entriesExitsChart.render();
                }

                // Gráfico de Tipo de Acceso
                const accessTypeEl = document.querySelector('#accessTypeChart');
                if (accessTypeEl) {
                    accessTypeEl.innerHTML = '';
                    const accessTypeData = JSON.parse(accessTypeEl.closest('.card-body').querySelector('script.access-type-data').innerHTML);
                    
                    const accessTypeOptions = {
                        series: [accessTypeData.entries, accessTypeData.exits],
                        chart: {
                            height: 280,
                            type: 'donut'
                        },
                        labels: ['Entradas', 'Salidas'],
                        colors: ['#28a745', '#dc3545'],
                        legend: { show: false },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '70%'
                                }
                            }
                        }
                    };
                    window.dashboardCharts.accessTypeChart = new ApexCharts(accessTypeEl, accessTypeOptions);
                    window.dashboardCharts.accessTypeChart.render();
                }

                // Gráfico de Estudiantes por Grado
                const studentsGradeEl = document.querySelector('#studentsGradeChart');
                if (studentsGradeEl) {
                    studentsGradeEl.innerHTML = '';
                    const studentsGradeData = JSON.parse(studentsGradeEl.closest('.card-body').querySelector('script.students-grade-data').innerHTML);
                    
                    const studentsGradeOptions = {
                        series: [{
                            name: 'Estudiantes',
                            data: Object.values(studentsGradeData)
                        }],
                        chart: {
                            height: 280,
                            type: 'bar',
                            toolbar: { show: false }
                        },
                        colors: ['#667eea'],
                        plotOptions: {
                            bar: {
                                borderRadius: 6,
                                columnWidth: '50%'
                            }
                        },
                        dataLabels: { enabled: false },
                        xaxis: {
                            categories: Object.keys(studentsGradeData)
                        },
                        yaxis: { min: 0 }
                    };
                    window.dashboardCharts.studentsGradeChart = new ApexCharts(studentsGradeEl, studentsGradeOptions);
                    window.dashboardCharts.studentsGradeChart.render();
                }

                // Gráfico de Estudiantes por Nivel
                const studentsLevelEl = document.querySelector('#studentsLevelChart');
                if (studentsLevelEl) {
                    studentsLevelEl.innerHTML = '';
                    const studentsLevelData = JSON.parse(studentsLevelEl.closest('.card-body').querySelector('script.students-level-data').innerHTML);
                    
                    const studentsLevelOptions = {
                        series: Object.values(studentsLevelData),
                        chart: {
                            height: 280,
                            type: 'pie'
                        },
                        labels: Object.keys(studentsLevelData),
                        colors: ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#00f2fe'],
                        legend: { position: 'bottom' }
                    };
                    window.dashboardCharts.studentsLevelChart = new ApexCharts(studentsLevelEl, studentsLevelOptions);
                    window.dashboardCharts.studentsLevelChart.render();
                }
            }, 150);
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderCharts();
        });

        document.addEventListener('livewire:init', function () {
            Livewire.on('dateRangeChanged', () => {
                setTimeout(() => {
                    renderCharts();
                }, 150);
            });
        });

        document.addEventListener('livewire:update', function () {
            setTimeout(() => {
                renderCharts();
            }, 150);
        });
    </script>
</div>
