<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="card-title mb-0">Dashboard Super Administrador</h4>
        <div class="d-flex align-items-center">
          <select class="form-select form-select-sm me-2" wire:model.live="dateRange">
            <option value="week">Última semana</option>
            <option value="month">Último mes</option>
            <option value="quarter">Último trimestre</option>
            <option value="year">Último año</option>
          </select>
          <button class="btn btn-primary btn-sm">
            <i class="mdi mdi-refresh"></i> Actualizar
          </button>
        </div>
      </div>
      <div class="card-body">
        <p>Bienvenido al panel de Super Administrador. Desde aquí podrás gestionar todos los aspectos del sistema.</p>

        <!-- Resúmenes ejecutivos -->
        <div class="row gy-4 mb-4">
          <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-primary h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                  <div class="avatar me-3">
                    <span class="avatar-initial rounded bg-label-primary"><i class="mdi mdi-account-multiple mdi-24px"></i></span>
                  </div>
                  <div>
                    <h4 class="mb-0">{{ $totalUsers }}</h4>
                    <small class="text-muted">Usuarios Totales</small>
                  </div>
                </div>
                @if(isset($comparisonData['users']))
                <div class="d-flex align-items-center">
                  <span class="badge {{ $comparisonData['users']['change'] >= 0 ? 'bg-label-success' : 'bg-label-danger' }} me-1">
                    {{ $comparisonData['users']['change'] >= 0 ? '+' : '' }}{{ $comparisonData['users']['change'] }}%
                  </span>
                  <span class="text-muted small">vs período anterior</span>
                </div>
                @endif
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-success h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                  <div class="avatar me-3">
                    <span class="avatar-initial rounded bg-label-success"><i class="mdi mdi-office-building mdi-24px"></i></span>
                  </div>
                  <div>
                    <h4 class="mb-0">{{ $totalEmpresas }}</h4>
                    <small class="text-muted">Empresas</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-warning h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                  <div class="avatar me-3">
                    <span class="avatar-initial rounded bg-label-warning"><i class="mdi mdi-map-marker mdi-24px"></i></span>
                  </div>
                  <div>
                    <h4 class="mb-0">{{ $totalSucursales }}</h4>
                    <small class="text-muted">Sucursales</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-info h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                  <div class="avatar me-3">
                    <span class="avatar-initial rounded bg-label-info"><i class="mdi mdi-shield-account mdi-24px"></i></span>
                  </div>
                  <div>
                    <h4 class="mb-0">{{ $totalRoles }}</h4>
                    <small class="text-muted">Roles</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row gy-4">
          <!-- Gráfico de usuarios por período -->
          <div class="col-lg-8">
            <div class="card">
              <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Usuarios registrados</h5>
                <div class="dropdown">
                  <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="usersDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-dots-vertical mdi-24px"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="usersDropdown">
                    <a class="dropdown-item" href="{{ route('admin.users.index') }}">Ver todos los usuarios</a>
                    <a class="dropdown-item" href="javascript:void(0);">Exportar datos</a>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div id="usersChart"></div>
                <script class="users-data" type="application/json">
                  @json($usersByPeriod)
                </script>
              </div>
            </div>
          </div>

          <!-- Gráfico de sesiones por estado -->
          <div class="col-lg-4">
            <div class="card">
              <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Estado de sesiones</h5>
                <div class="dropdown">
                  <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="sessionsDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-dots-vertical mdi-24px"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="sessionsDropdown">
                    <a class="dropdown-item" href="{{ route('admin.active-sessions.index') }}">Ver todas</a>
                    <a class="dropdown-item" href="javascript:void(0);">Exportar datos</a>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div id="sessionsChart"></div>
                <script class="sessions-data" type="application/json">
                  @json($sessionsByStatus)
                </script>
              </div>
            </div>
          </div>

          <!-- Monthly Budget Chart (Historial de sesiones) -->
          <div class="col-lg-4 col-md-6">
            <div class="card h-100">
              <div class="card-header">
                <div class="d-flex justify-content-between">
                  <h5 class="mb-1">Historial de Sesiones</h5>
                  <div class="dropdown">
                    <button
                      class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1"
                      type="button"
                      id="monthlyBudgetDropdown"
                      data-bs-toggle="dropdown"
                      aria-haspopup="true"
                      aria-expanded="false">
                      <i class="mdi mdi-dots-vertical mdi-24px"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="monthlyBudgetDropdown">
                      <a class="dropdown-item" href="{{ route('admin.active-sessions.index') }}">Ver todas</a>
                      <a class="dropdown-item" href="javascript:void(0);">Exportar</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-body pt-xl-6">
                <div id="sessionsHistoryChart"></div>
                <div class="mt-4">
                  <p class="mb-0">
                    En el último {{ $dateRange == 'week' ? 'periodo' : ($dateRange == 'month' ? 'mes' : ($dateRange == 'quarter' ? 'trimestre' : 'mes')) }} has tenido {{ $totalActiveSessions }} sesiones activas, {{ $sessionsByStatus['inactive'] }} inactivas y {{ $totalUsers }} usuarios registrados.
                  </p>
                </div>
                <script class="sessions-history-data" type="application/json">
                  @json($sessionsByStatus)
                </script>
              </div>
              <script class="total-users-data" type="application/json">
                @json($totalUsers)
              </script>
            </div>
          </div>

          <!-- Gráfico de usuarios por empresa -->
          <div class="col-lg-8 col-md-6">
            <div class="card">
              <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Usuarios por empresa</h5>
                <div class="dropdown">
                  <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="usersByEmpresaDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-dots-vertical mdi-24px"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="usersByEmpresaDropdown">
                    <a class="dropdown-item" href="{{ route('admin.empresas.index') }}">Ver todas las empresas</a>
                    <a class="dropdown-item" href="javascript:void(0);">Exportar datos</a>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div id="usersByEmpresaChart"></div>
                <script class="users-by-empresa-data" type="application/json">
                  @json(['labels' => array_keys($usersByEmpresa), 'values' => array_values($usersByEmpresa)])
                </script>
              </div>
            </div>
          </div>

          <!-- Gráfico de accesos al sistema -->
          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Accesos al sistema</h5>
                <div class="dropdown">
                  <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="loginsDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-dots-vertical mdi-24px"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="loginsDropdown">
                    <a class="dropdown-item" href="javascript:void(0);">Exportar datos</a>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div id="loginsChart"></div>
                <script class="logins-data" type="application/json">
                  @json($loginsByPeriod)
                </script>
              </div>
            </div>
          </div>

          <!-- Estadísticas de permisos -->
          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Distribución de permisos por rol</h5>
                <div class="dropdown">
                  <button class="btn btn-text-secondary rounded-pill text-body-secondary border-0 p-1" type="button" id="permissionsDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-dots-vertical mdi-24px"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="permissionsDropdown">
                    <a class="dropdown-item" href="{{ route('admin.roles.index') }}">Ver todos los roles</a>
                    <a class="dropdown-item" href="javascript:void(0);">Exportar datos</a>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div id="permissionsChart"></div>
                <script class="permissions-data" type="application/json">
                  @json(['labels' => array_keys($permissionsStats), 'values' => array_values($permissionsStats)])
                </script>
              </div>
            </div>
          </div>
        </div>

        <!-- Información del servidor -->
        <div class="row gy-4 mt-2">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title mb-0">Información del Servidor</h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <p><strong>Versión de PHP:</strong> {{ $serverInfo['php_version'] }}</p>
                    <p><strong>Versión de Laravel:</strong> {{ $serverInfo['laravel_version'] }}</p>
                    <p><strong>Base de datos:</strong> {{ $serverInfo['database'] }}</p>
                  </div>
                  <div class="col-md-6">
                    <p><strong>Servidor:</strong> {{ $serverInfo['server_software'] }}</p>
                    <p><strong>Sistema Operativo:</strong> {{ $serverInfo['server_os'] }}</p>
                    <p><strong>Uso de Memoria:</strong> {{ $serverInfo['memory_usage'] }}</p>
                    <p><strong>Tiempo máximo de ejecución:</strong> {{ $serverInfo['max_execution_time'] }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Últimas sesiones -->
        <div class="row gy-4 mt-2">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title mb-0">Últimas Sesiones</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Usuario</th>
                        <th>IP</th>
                        <th>Última Actividad</th>
                        <th>Estado</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($recentSessions as $session)
                      <tr>
                        <td>{{ $session->user->name }}</td>
                        <td>{{ $session->ip_address }}</td>
                        <td>{{ $session->last_activity->diffForHumans() }}</td>
                        <td>
                          @if($session->is_active)
                          <span class="badge bg-success">Activa</span>
                          @else
                          <span class="badge bg-secondary">Inactiva</span>
                          @endif
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
      </div>
    </div>
  </div>
</div>
</div>

@push('styles')
<!-- Apex Charts CSS -->
<link rel="stylesheet" href="{{ asset('materialize/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endpush

@push('scripts')
<!-- Apex Charts JS -->
<script src="{{ asset('materialize/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

<script>
// Variable para almacenar los gráficos
window.dashboardCharts = {};

// Función para destruir gráficos existentes
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

// Función para renderizar los gráficos
function renderCharts() {
  // Destruir gráficos existentes
  destroyCharts();

  // Esperar un poco para asegurar que Livewire haya actualizado el DOM
  setTimeout(() => {
    // Gráfico de usuarios por período
    const usersChartEl = document.querySelector('#usersChart');
    if (usersChartEl) {
      // Limpiar el contenedor del gráfico
      usersChartEl.innerHTML = '';

      // Obtener los datos actualizados del DOM
      const usersData = JSON.parse(usersChartEl.closest('.card-body').querySelector('script.users-data').innerHTML);

      const usersChartConfig = {
        chart: {
          height: 350,
          type: 'area',
          toolbar: {
            show: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'smooth',
          width: 2
        },
        legend: {
          position: 'top',
          horizontalAlign: 'right'
        },
        grid: {
          strokeDashArray: 4,
          padding: {
            right: 0,
            left: 0
          }
        },
        colors: ['#007BFF'],
        series: [{
          name: 'Usuarios',
          data: usersData.data
        }],
        xaxis: {
          categories: usersData.labels
        },
        tooltip: {
          y: {
            formatter: function(val) {
              return val + ' usuarios';
            }
          }
        }
      };

      window.dashboardCharts.usersChart = new ApexCharts(usersChartEl, usersChartConfig);
      window.dashboardCharts.usersChart.render();
    }

    // Gráfico de sesiones por estado
    const sessionsChartEl = document.querySelector('#sessionsChart');
    if (sessionsChartEl) {
      // Limpiar el contenedor del gráfico
      sessionsChartEl.innerHTML = '';

      // Obtener los datos actualizados del DOM
      const sessionsData = JSON.parse(sessionsChartEl.closest('.card-body').querySelector('script.sessions-data').innerHTML);

      const sessionsChartConfig = {
        chart: {
          height: 350,
          type: 'donut'
        },
        labels: ['Activas', 'Inactivas'],
        series: [sessionsData.active, sessionsData.inactive],
        colors: ['#28C76F', '#EA5455'],
        stroke: {
          show: false,
          width: 0
        },
        dataLabels: {
          enabled: true,
          formatter: function(val, opt) {
            return parseInt(val) + '%';
          }
        },
        legend: {
          position: 'bottom',
          horizontalAlign: 'center'
        },
        tooltip: {
          y: {
            formatter: function(val) {
              return val + ' sesiones';
            }
          }
        }
      };

      window.dashboardCharts.sessionsChart = new ApexCharts(sessionsChartEl, sessionsChartConfig);
      window.dashboardCharts.sessionsChart.render();
    }

    // Gráfico de historial de sesiones (similar al Monthly Budget)
    const sessionsHistoryChartEl = document.querySelector('#sessionsHistoryChart');
    if (sessionsHistoryChartEl) {
      // Limpiar el contenedor del gráfico
      sessionsHistoryChartEl.innerHTML = '';

      // Obtener los datos actualizados del DOM
      const sessionsHistoryData = JSON.parse(sessionsHistoryChartEl.closest('.card-body').querySelector('script.sessions-history-data').innerHTML);
      const usersTotal = JSON.parse(sessionsHistoryChartEl.closest('.card').querySelector('script.total-users-data').innerHTML);

      const sessionsHistoryChartConfig = {
        chart: {
          height: 230,
          type: 'donut'
        },
        labels: ['Sesiones Activas', 'Sesiones Inactivas', 'Usuarios'],
        series: [sessionsHistoryData.active, sessionsHistoryData.inactive, usersTotal],
        colors: ['#28C76F', '#EA5455', '#007BFF'],
        stroke: {
          show: false,
          width: 0
        },
        dataLabels: {
          enabled: false,
          formatter: function(val, opt) {
            return parseInt(val) + '%';
          }
        },
        legend: {
          position: 'bottom',
          horizontalAlign: 'center'
        },
        tooltip: {
          y: {
            formatter: function(val) {
              return val + ' elementos';
            }
          }
        }
      };

      window.dashboardCharts.sessionsHistoryChart = new ApexCharts(sessionsHistoryChartEl, sessionsHistoryChartConfig);
      window.dashboardCharts.sessionsHistoryChart.render();
    }

    // Gráfico de usuarios por empresa
    const usersByEmpresaChartEl = document.querySelector('#usersByEmpresaChart');
    if (usersByEmpresaChartEl) {
      // Limpiar el contenedor del gráfico
      usersByEmpresaChartEl.innerHTML = '';

      // Obtener los datos actualizados del DOM
      const usersByEmpresaData = JSON.parse(usersByEmpresaChartEl.closest('.card-body').querySelector('script.users-by-empresa-data').innerHTML);

      const usersByEmpresaChartConfig = {
        chart: {
          height: 350,
          type: 'bar',
          toolbar: {
            show: false
          }
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '40%',
            endingShape: 'rounded'
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
        },
        series: [{
          name: 'Usuarios',
          data: usersByEmpresaData.values
        }],
        xaxis: {
          categories: usersByEmpresaData.labels
        },
        yaxis: {
          title: {
            text: 'Número de usuarios'
          }
        },
        fill: {
          opacity: 1
        },
        tooltip: {
          y: {
            formatter: function(val) {
              return val + ' usuarios';
            }
          }
        }
      };

      window.dashboardCharts.usersByEmpresaChart = new ApexCharts(usersByEmpresaChartEl, usersByEmpresaChartConfig);
      window.dashboardCharts.usersByEmpresaChart.render();
    }

    // Gráfico de accesos al sistema
    const loginsChartEl = document.querySelector('#loginsChart');
    if (loginsChartEl) {
      // Limpiar el contenedor del gráfico
      loginsChartEl.innerHTML = '';

      // Obtener los datos actualizados del DOM
      const loginsData = JSON.parse(loginsChartEl.closest('.card-body').querySelector('script.logins-data').innerHTML);

      const loginsChartConfig = {
        chart: {
          height: 350,
          type: 'line',
          toolbar: {
            show: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'smooth',
          width: 2
        },
        legend: {
          position: 'top',
          horizontalAlign: 'right'
        },
        grid: {
          strokeDashArray: 4,
          padding: {
            right: 0,
            left: 0
          }
        },
        colors: ['#FF9F43'],
        series: [{
          name: 'Accesos',
          data: loginsData.data
        }],
        xaxis: {
          categories: loginsData.labels
        },
        tooltip: {
          y: {
            formatter: function(val) {
              return val + ' accesos';
            }
          }
        }
      };

      window.dashboardCharts.loginsChart = new ApexCharts(loginsChartEl, loginsChartConfig);
      window.dashboardCharts.loginsChart.render();
    }

    // Gráfico de permisos por rol
    const permissionsChartEl = document.querySelector('#permissionsChart');
    if (permissionsChartEl) {
      // Limpiar el contenedor del gráfico
      permissionsChartEl.innerHTML = '';

      // Obtener los datos actualizados del DOM
      const permissionsData = JSON.parse(permissionsChartEl.closest('.card-body').querySelector('script.permissions-data').innerHTML);

      const permissionsChartConfig = {
        chart: {
          height: 350,
          type: 'bar',
          toolbar: {
            show: false
          }
        },
        plotOptions: {
          bar: {
            horizontal: true,
            columnWidth: '40%',
            endingShape: 'rounded'
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
        },
        series: [{
          name: 'Permisos',
          data: permissionsData.values
        }],
        xaxis: {
          categories: permissionsData.labels
        },
        yaxis: {
          title: {
            text: 'Roles'
          }
        },
        fill: {
          opacity: 1
        },
        tooltip: {
          y: {
            formatter: function(val) {
              return val + ' permisos';
            }
          }
        }
      };

      window.dashboardCharts.permissionsChart = new ApexCharts(permissionsChartEl, permissionsChartConfig);
      window.dashboardCharts.permissionsChart.render();
    }
  }, 150);
}

// Renderizar gráficos cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
  renderCharts();
});

// Escuchar eventos de Livewire para re-renderizar cuando cambien los datos
document.addEventListener('livewire:init', function () {
  Livewire.on('dateRangeChanged', () => {
    // Usar setTimeout para asegurar que el DOM se haya actualizado
    setTimeout(() => {
      renderCharts();
    }, 150);
  });
});

// También intentar renderizar los gráficos después de cada actualización de Livewire
document.addEventListener('livewire:update', function () {
  setTimeout(() => {
    renderCharts();
  }, 150);
});
</script>
@endpush
