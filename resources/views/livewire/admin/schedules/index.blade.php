<div>
    @section('title', 'Gestión de Horarios')

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gestión de Horarios</h3>
                    <div class="card-tools">
                        @can('schedules.create')
                            <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nuevo Horario
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="search">Búsqueda</label>
                                <input type="text" class="form-control" id="search" placeholder="Buscar..." wire:model.debounce.300ms="search">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="company_id">Empresa</label>
                                <select class="form-control" id="company_id" wire:model="company_id">
                                    <option value="">Todas las empresas</option>
                                    @foreach($companies as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="branch_id">Sucursal</label>
                                <select class="form-control" id="branch_id" wire:model="branch_id" @disabled(!$company_id)>
                                    <option value="">Todas las sucursales</option>
                                    @foreach($branches as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="school_period_id">Período Escolar</label>
                                <select class="form-control" id="school_period_id" wire:model="school_period_id">
                                    <option value="">Todos los períodos</option>
                                    @foreach($schoolPeriods as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="section_id">Sección</label>
                                <select class="form-control" id="section_id" wire:model="section_id" @disabled(!$company_id || !$branch_id)>
                                    <option value="">Todas las secciones</option>
                                    @foreach($sections as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="classroom_id">Aula</label>
                                <select class="form-control" id="classroom_id" wire:model="classroom_id" @disabled(!$company_id || !$branch_id)>
                                    <option value="">Todas las aulas</option>
                                    @foreach($classrooms as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="day">Día</label>
                                <select class="form-control" id="day" wire:model="day">
                                    <option value="">Todos los días</option>
                                    @foreach($days as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Estado</label>
                                <select class="form-control" id="status" wire:model="status">
                                    <option value="">Todos</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de horarios -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <a wire:click.prevent="sortBy('start_time')" role="button" href="#">
                                            Hora Inicio
                                            @if($sortField === 'start_time')
                                                @if($sortDirection === 'asc')
                                                    <i class="fas fa-sort-up"></i>
                                                @else
                                                    <i class="fas fa-sort-down"></i>
                                                @endif
                                            @else
                                                <i class="fas fa-sort"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a wire:click.prevent="sortBy('end_time')" role="button" href="#">
                                            Hora Fin
                                            @if($sortField === 'end_time')
                                                @if($sortDirection === 'asc')
                                                    <i class="fas fa-sort-up"></i>
                                                @else
                                                    <i class="fas fa-sort-down"></i>
                                                @endif
                                            @else
                                                <i class="fas fa-sort"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Día</th>
                                    <th>Sección</th>
                                    <th>Materia</th>
                                    <th>Profesor</th>
                                    <th>Aula</th>
                                    <th>Empresa</th>
                                    <th>Sucursal</th>
                                    <th>Estado</th>
                                    <th width="150">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $schedule)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                                        <td>
                                            @php
                                                $days = [
                                                    1 => 'Lunes',
                                                    2 => 'Martes',
                                                    3 => 'Miércoles',
                                                    4 => 'Jueves',
                                                    5 => 'Viernes',
                                                    6 => 'Sábado',
                                                    7 => 'Domingo',
                                                ];
                                            @endphp
                                            {{ $days[$schedule->day] ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $schedule->section->code }}</span><br>
                                            <small>{{ $schedule->section->name }}</small>
                                        </td>
                                        <td>{{ $schedule->section->subject->name ?? 'N/A' }}</td>
                                        <td>{{ $schedule->section->teacher->name ?? 'N/A' }}</td>
                                        <td>{{ $schedule->classroom->name ?? 'N/A' }}</td>
                                        <td>{{ $schedule->section->company->name ?? 'N/A' }}</td>
                                        <td>{{ $schedule->section->branch->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $schedule->status ? 'badge-success' : 'badge-danger' }}">
                                                {{ $schedule->status ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('schedules.show')
                                                    <a href="{{ route('admin.schedules.show', $schedule->id) }}" class="btn btn-sm btn-info" title="Ver">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('schedules.edit')
                                                    <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('schedules.destroy')
                                                    <button wire:click="confirmDelete({{ $schedule->id }})" class="btn btn-sm btn-danger" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                                @can('schedules.toggle')
                                                    <button wire:click="toggleStatus({{ $schedule->id }})" class="btn btn-sm {{ $schedule->status ? 'btn-secondary' : 'btn-success' }}" title="{{ $schedule->status ? 'Desactivar' : 'Activar' }}">
                                                        <i class="fas fa-{{ $schedule->status ? 'times' : 'check' }}"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">No se encontraron horarios</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Mostrando {{ $schedules->firstItem() }} a {{ $schedules->lastItem() }} de {{ $schedules->total() }} resultados
                        </div>
                        <div>
                            {{ $schedules->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="deleteModalText">¿Estás seguro de que deseas eliminar este horario?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('confirm-delete', data => {
                $('#deleteModal').modal('show');
                $('#deleteModalText').text(data.text || '¿Estás seguro de que deseas eliminar este registro?');
                
                $('#confirmDeleteBtn').off('click').on('click', function() {
                    Livewire.emit(data.method, ...data.params);
                    $('#deleteModal').modal('hide');
                });
            });

            Livewire.on('schedule-conflicts', data => {
                let conflictsText = 'Advertencia: Existen conflictos de horario con las siguientes secciones:\n\n';
                data.conflicts.forEach(conflict => {
                    conflictsText += `• ${conflict.section_code} - ${conflict.section_name} (${conflict.start_time} - ${conflict.end_time})\n`;
                });
                conflictsText += '\n¿Deseas continuar con el registro?';
                
                if (!confirm(conflictsText)) {
                    // Limpiar campos o tomar otra acción
                }
            });
        });
    </script>
    @endpush
</div>