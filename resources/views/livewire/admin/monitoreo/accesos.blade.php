<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="ri ri-login-box-line me-2"></i>Monitoreo de Accesos</h4>
                    <button wire:click="exportExcel" class="btn btn-success btn-sm">
                        <i class="ri ri-file-excel-line me-1"></i>Exportar Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" wire:model.live="startDate" class="form-control">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" wire:model.live="endDate" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <div class="avatar-initial bg-label-primary rounded-3">
                                <i class="ri ri-door-line ri-26px"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ number_format($stats['total']) }}</h5>
                            <small class="text-muted">Total Accesos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <div class="avatar-initial bg-label-success rounded-3">
                                <i class="ri ri-login-box-line ri-26px"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ number_format($stats['entradas']) }}</h5>
                            <small class="text-muted">Entradas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <div class="avatar-initial bg-label-danger rounded-3">
                                <i class="ri ri-logout-box-line ri-26px"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ number_format($stats['salidas']) }}</h5>
                            <small class="text-muted">Salidas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Accesos por Día</h5>
                </div>
                <div class="card-body">
                    <div id="accessByDayChart"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Horarios Pico</h5>
                </div>
                <div class="card-body">
                    @foreach($byHour as $hour)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ri ri-time-line"></i>
                                </span>
                            </div>
                            <span>{{ str_pad($hour->hour, 2, '0', STR_PAD_LEFT) }}:00</span>
                        </div>
                        <strong>{{ $hour->count }}</strong>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Registros Recientes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha/Hora</th>
                                    <th>Código</th>
                                    <th>Estudiante</th>
                                    <th>Grado</th>
                                    <th>Tipo</th>
                                    <th>Registrado Por</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent as $access)
                                <tr>
                                    <td>{{ $access->access_time->format('d/m/Y H:i:s') }}</td>
                                    <td><code>{{ $access->student->codigo }}</code></td>
                                    <td>{{ $access->student->nombres }} {{ $access->student->apellidos }}</td>
                                    <td>{{ $access->student->grado }} - {{ $access->student->seccion }}</td>
                                    <td>
                                        @if($access->type === 'entrada')
                                        <span class="badge bg-label-success"><i class="ri ri-login-box-line me-1"></i>Entrada</span>
                                        @else
                                        <span class="badge bg-label-danger"><i class="ri ri-logout-box-line me-1"></i>Salida</span>
                                        @endif
                                    </td>
                                    <td>{{ $access->registeredBy->name ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let accessByDayChart;

        function renderAccessChart() {
            if (accessByDayChart) accessByDayChart.destroy();

            const byDayData = @json($byDay);
            
            const options = {
                series: [{
                    name: 'Accesos',
                    data: byDayData.map(d => d.count)
                }],
                chart: {
                    height: 300,
                    type: 'area',
                    toolbar: { show: false }
                },
                colors: ['#667eea'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                xaxis: {
                    categories: byDayData.map(d => d.date)
                },
                yaxis: { min: 0 }
            };

            accessByDayChart = new ApexCharts(document.querySelector('#accessByDayChart'), options);
            accessByDayChart.render();
        }

        document.addEventListener('DOMContentLoaded', renderAccessChart);
        document.addEventListener('livewire:update', () => setTimeout(renderAccessChart, 100));
    </script>
    @endpush
</div>
