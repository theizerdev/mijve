<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Seguimiento de Actividades</h5>
                            <p class="mb-0">Registro de todas las actividades realizadas en el sistema</p>
                        </div>
                        <div>
                            @can('export activity log')
                            <button class="btn btn-primary" wire:click="export">
                                <i class="ri ri-download-line"></i> Exportar
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-header border-bottom">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" placeholder="Buscar por descripción o usuario..."
                                   wire:model.live.debounce.300ms="search">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Usuario</label>
                            <select class="form-select" wire:model.live="userFilter">
                                <option value="">Todos los usuarios</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha</label>
                            <select class="form-select" wire:model.live="dateRange">
                                <option value="">Todas las fechas</option>
                                <option value="today">Hoy</option>
                                <option value="yesterday">Ayer</option>
                                <option value="week">Esta semana</option>
                                <option value="month">Este mes</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="button" class="btn btn-label-secondary" wire:click="clearFilters">
                                <i class="ri ri-eraser-line"></i> Limpiar
                            </button>
                            <button type="button" class="btn btn-label-success" wire:click="export">
                                <i class="mdi mdi-file-excel"></i> Exportar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha y Hora</th>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Elemento</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities as $activity)
                                    <tr>
                                        <td>
                                            <div>{{ $activity->created_at->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $activity->created_at->format('H:i:s') }}</small>
                                        </td>
                                        <td>
                                            @if($activity->causer)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                                            {{ $activity->causer->initials }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $activity->causer->name }}</div>
                                                        <small class="text-muted">{{ $activity->causer->email }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="badge bg-label-secondary">Sistema</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-label-{{ $this->getActionColor($activity->description) }}">
                                                {{ ucfirst($activity->description) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($activity->subject)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-xs me-2">
                                                        <span class="avatar-initial rounded bg-label-secondary">
                                                            {{ substr($activity->subject_type, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ class_basename($activity->subject_type) }}</div>
                                                        <small class="text-muted">
                                                            @if(isset($activity->subject->name))
                                                                {{ $activity->subject->name }}
                                                            @elseif(isset($activity->subject->nombres))
                                                                {{ $activity->subject->nombres }}
                                                            @elseif(isset($activity->subject->razon_social))
                                                                {{ $activity->subject->razon_social }}
                                                            @else
                                                                ID: {{ $activity->subject_id }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">No especificado</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($activity->properties->count() > 0)
                                                <button class="btn btn-sm btn-text-secondary rounded-pill btn-icon"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#activityDetailsModal{{ $activity->id }}">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                                
                                                <!-- Modal de detalles -->
                                                <div class="modal fade" id="activityDetailsModal{{ $activity->id }}" tabindex="-1">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Detalles de Actividad</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-12 mb-3">
                                                                        <h6>Cambios realizados:</h6>
                                                                        @if($activity->properties->has('attributes'))
                                                                            <div class="table-responsive">
                                                                                <table class="table table-bordered">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Campo</th>
                                                                                            <th>Valor</th>
                                                                                            @if($activity->properties->has('old'))
                                                                                                <th>Valor anterior</th>
                                                                                            @endif
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        @foreach($activity->properties['attributes'] as $key => $value)
                                                                                            <tr>
                                                                                                <td>{{ $key }}</td>
                                                                                                <td>
                                                                                                    @if(is_array($value))
                                                                                                        <pre class="mb-0">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                                                                    @else
                                                                                                        {{ $value }}
                                                                                                    @endif
                                                                                                </td>
                                                                                                @if($activity->properties->has('old'))
                                                                                                    <td>
                                                                                                        @if(isset($activity->properties['old'][$key]))
                                                                                                            @if(is_array($activity->properties['old'][$key]))
                                                                                                                <pre class="mb-0">{{ json_encode($activity->properties['old'][$key], JSON_PRETTY_PRINT) }}</pre>
                                                                                                            @else
                                                                                                                {{ $activity->properties['old'][$key] }}
                                                                                                            @endif
                                                                                                        @else
                                                                                                            <span class="text-muted">No existía</span>
                                                                                                        @endif
                                                                                                    </td>
                                                                                                @endif
                                                                                            </tr>
                                                                                        @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        @else
                                                                            <p class="text-muted">No hay cambios registrados</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                                    Cerrar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">Sin detalles</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <i class="ti ti-file-off ti-lg mb-2"></i>
                                            <p>No se encontraron actividades</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                </div>

                <div class="card-footer">
                   {{ $activities->links('vendor.pagination.materialize') }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', function () {
        // Escuchar eventos de Livewire para mostrar notificaciones
        Livewire.on('activityDeleted', function () {
            toastr.success('Actividad eliminada correctamente');
        });
    });
</script>
@endpush