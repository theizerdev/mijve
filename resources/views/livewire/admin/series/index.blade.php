<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Series de Documentos</h5>
                <small class="text-muted">Gestión de numeración de documentos</small>
            </div>
            @can('create series')
            <a href="{{ route('admin.series.create') }}" class="btn btn-primary">
                <i class="ri-add-line me-1"></i> Nueva Serie
            </a>
            @endcan
        </div>

        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri-search-line"></i></span>
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Buscar por serie...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="tipo_documento" class="form-select">
                        <option value="">Todos los tipos</option>
                        @foreach($tipos as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <span class="badge bg-label-primary">Total: {{ $series->total() }}</span>
                        <span class="badge bg-label-success">Activas: {{ $series->where('activo', true)->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th><i class="ri-file-text-line me-1"></i>Tipo</th>
                            <th><i class="ri-hashtag me-1"></i>Serie</th>
                            <th><i class="ri-number-1 me-1"></i>Correlativo</th>
                            <th><i class="ri-building-line me-1"></i>Empresa</th>
                            <th><i class="ri-community-line me-1"></i>Sucursal</th>
                            <th><i class="ri-toggle-line me-1"></i>Estado</th>
                            <th width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($series as $serie)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @php
                                        $iconos = [
                                            'factura' => 'ri-file-text-line text-primary',
                                            'boleta' => 'ri-receipt-line text-info',
                                            'nota_credito' => 'ri-file-reduce-line text-warning',
                                            'recibo' => 'ri-file-copy-line text-success'
                                        ];
                                    @endphp
                                    <i class="{{ $iconos[$serie->tipo_documento] ?? 'ri-file-line' }} me-2"></i>
                                    <span>{{ $tipos[$serie->tipo_documento] ?? $serie->tipo_documento }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-dark fs-6">{{ $serie->serie }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-medium">{{ str_pad($serie->correlativo_actual, $serie->longitud_correlativo, '0', STR_PAD_LEFT) }}</span>
                                    <small class="text-muted">Longitud: {{ $serie->longitud_correlativo }}</small>
                                </div>
                            </td>
                            <td>{{ $serie->empresa->nombre ?? '-' }}</td>
                            <td>{{ $serie->sucursal->nombre ?? '-' }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           wire:click="toggleActivo({{ $serie->id }})" 
                                           {{ $serie->activo ? 'checked' : '' }}
                                           id="switch{{ $serie->id }}">
                                    <label class="form-check-label" for="switch{{ $serie->id }}">
                                        <span class="badge bg-label-{{ $serie->activo ? 'success' : 'secondary' }}">
                                            {{ $serie->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @can('edit series')
                                    <a href="{{ route('admin.series.edit', $serie) }}" 
                                       class="btn btn-sm btn-icon btn-text-secondary rounded-pill" 
                                       title="Editar">
                                        <i class="ri-edit-line ri-20px"></i>
                                    </a>
                                    @endcan
                                    @can('delete series')
                                    <button wire:click="delete({{ $serie->id }})" 
                                            wire:confirm="¿Eliminar la serie {{ $serie->serie }}?" 
                                            class="btn btn-sm btn-icon btn-text-danger rounded-pill" 
                                            title="Eliminar">
                                        <i class="ri-delete-bin-7-line ri-20px"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ri-file-list-3-line ri-48px text-muted mb-2"></i>
                                    <h6 class="text-muted">No hay series registradas</h6>
                                    <p class="text-muted mb-0">Crea tu primera serie para comenzar</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($series->hasPages())
            <div class="mt-4 d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Mostrando {{ $series->firstItem() }} a {{ $series->lastItem() }} de {{ $series->total() }} resultados
                </div>
                <div>
                    {{ $series->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Alertas -->
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <i class="ri-check-line me-2"></i>{{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
</div>
