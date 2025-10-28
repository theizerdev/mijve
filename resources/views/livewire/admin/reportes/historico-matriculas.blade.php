<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Histórico de Matrículas</h4>
            <p class="text-muted mb-0">Historial de matrículas por estudiante</p>
        </div>
        <div>
            <button wire:click="exportarExcel" class="btn btn-success me-2" @if(count($matriculas) == 0) disabled @endif>
                <i class="ri ri-file-excel-line me-1"></i> Exportar Excel
            </button>
            <button wire:click="exportarPDF" class="btn btn-danger" @if(count($matriculas) == 0) disabled @endif>
                <i class="ri ri-file-pdf-line me-1"></i> Exportar PDF
            </button>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="periodo_id" class="form-label">Período Escolar</label>
                        <select wire:model.live="periodo_id" class="form-select" id="periodo_id">
                            <option value="">Seleccione un período</option>
                            @foreach($periodos as $periodo)
                                <option value="{{ $periodo->id }}">
                                    {{ $periodo->nombre }} ({{ $periodo->fecha_inicio?->format('d/m/Y') ?? 'N/A' }} - {{ $periodo->fecha_fin?->format('d/m/Y') ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                        <input type="date" wire:model.live="fecha_inicio" class="form-control" id="fecha_inicio" value="{{ $fecha_inicio ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                        <input type="date" wire:model.live="fecha_fin" class="form-control" id="fecha_fin" value="{{ $fecha_fin ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="nivel_educativo_id" class="form-label">Nivel Educativo</label>
                        <select wire:model.live="nivel_educativo_id" class="form-select" id="nivel_educativo_id">
                            <option value="">Todos los niveles</option>
                            @foreach($nivelesEducativos as $nivel)
                                <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="programa_id" class="form-label">Programa</label>
                        <select wire:model.live="programa_id" class="form-select" id="programa_id" @if($programas->count() == 0) disabled @endif>
                            <option value="">Todos los programas</option>
                            @foreach($programas as $programa)
                                <option value="{{ $programa->id }}">{{ $programa->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <button wire:click="cargarReporte" class="btn btn-primary">
                <i class="ri ri-search-line me-1"></i> Generar Reporte
            </button>
        </div>
    </div>

    @if(count($matriculas) > 0)
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="mb-1 text-muted">Total Matrículas</p>
                        <h3 class="mb-0">{{ $estadisticas['total'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="mb-1 text-muted">Niveles</p>
                        <h3 class="mb-0">{{ $estadisticas['por_nivel']->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="mb-1 text-muted">Programas</p>
                        <h3 class="mb-0">{{ $estadisticas['por_programa']->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="mb-1 text-muted">Períodos</p>
                        <h3 class="mb-0">{{ $estadisticas['por_periodo']->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Estadísticas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Por Estado</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Estado</th>
                                        <th class="text-end">Cantidad</th>
                                        <th class="text-end">Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($estadisticas['por_estado'] as $estado => $cantidad)
                                        <tr>
                                            <td>
                                                @if($estado == 'activo')
                                                    <span class="badge bg-success">Activo</span>
                                                @elseif($estado == 'inactivo')
                                                    <span class="badge bg-danger">Inactivo</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($estado) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end">{{ $cantidad }}</td>
                                            <td class="text-end">{{ number_format(($cantidad / ($estadisticas['total'] > 0 ? $estadisticas['total'] : 1)) * 100, 2) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Por Nivel Educativo</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nivel</th>
                                        <th class="text-end">Cantidad</th>
                                        <th class="text-end">Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($estadisticas['por_nivel'] as $nivel => $cantidad)
                                        <tr>
                                            <td>{{ $nivel }}</td>
                                            <td class="text-end">{{ $cantidad }}</td>
                                            <td class="text-end">{{ number_format(($cantidad / ($estadisticas['total'] > 0 ? $estadisticas['total'] : 1)) * 100, 2) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Detalle de Matrículas</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Estudiante</th>
                                <th>Documento</th>
                                <th>Programa</th>
                                <th>Nivel</th>
                                <th>Período</th>
                                <th>Estado</th>
                                <th class="text-end">Costo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($matriculas as $matricula)
                                <tr>
                                    <td>{{ $matricula->fecha_matricula?->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td>{{ $matricula->student->nombres ?? '' }} {{ $matricula->student->apellidos ?? '' }}</td>
                                    <td>{{ $matricula->student->documento_identidad ?? '' }}</td>
                                    <td>{{ $matricula->programa->nombre ?? '' }}</td>
                                    <td>{{ $matricula->programa->nivelEducativo->nombre ?? '' }}</td>
                                    <td>{{ $matricula->periodo->nombre ?? '' }}</td>
                                    <td>
                                        @if($matricula->estado == 'activo')
                                            <span class="badge bg-success">Activo</span>
                                        @elseif($matricula->estado == 'inactivo')
                                            <span class="badge bg-danger">Inactivo</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($matricula->estado) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">${{ number_format($matricula->costo ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="ri ri-search-eye-line ri-3x text-muted mb-3"></i>
                <h5 class="mb-2">No hay datos para mostrar</h5>
                <p class="text-muted mb-0">No se encontraron matrículas con los filtros aplicados</p>
            </div>
        </div>
    @endif
</div>