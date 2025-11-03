# 📋 Template Estándar para Index.blade.php (Basado en Sucursales)

## 🎯 Estructura Unificada

Todos los archivos `index.blade.php` deben seguir esta estructura:

```blade
<div>
    <!-- 1. ALERTAS -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 2. ESTADÍSTICAS -->
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $total }}</h4>
                            <p class="mb-0">Total [Items]</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-[icono]-line ri-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Más estadísticas... -->
    </div>

    <!-- 3. CARD PRINCIPAL -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- 3.1 HEADER -->
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">[Título del Módulo]</h5>
                            <p class="mb-0">[Descripción breve]</p>
                        </div>
                        @can('create [modulo]')
                        <div>
                            <a href="{{ route('admin.[modulo].create') }}" class="btn btn-primary">
                                <i class="ri ri-add-line"></i> Nuevo [Item]
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>

                <!-- 3.2 FILTROS -->
                <div class="card-header border-bottom">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" placeholder="Buscar..."
                                   wire:model.live.debounce.300ms="search">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model.live="status">
                                <option value="">Todos</option>
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mostrar</label>
                            <select class="form-select" wire:model.live="perPage">
                                <option value="10">10 por página</option>
                                <option value="25">25 por página</option>
                                <option value="50">50 por página</option>
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

                <!-- 3.3 TABLA -->
                <div class="card-datatable table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('campo')" style="cursor: pointer;">
                                    Campo
                                    @if($sortBy === 'campo')
                                        <i class="ri ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                                    @endif
                                </th>
                                <!-- Más columnas... -->
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                <td>{{ $item->campo }}</td>
                                <!-- Más celdas... -->
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ri ri-more-2-line"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            @can('edit [modulo]')
                                            <a class="dropdown-item" href="{{ route('admin.[modulo].edit', $item) }}">
                                                <i class="ri ri-pencil-line me-1"></i> Editar
                                            </a>
                                            @endcan
                                            @can('delete [modulo]')
                                            <button type="button" class="dropdown-item text-danger"
                                                    wire:click="delete({{ $item->id }})"
                                                    wire:confirm="¿Eliminar este elemento?">
                                                <i class="ri ri-delete-bin-line me-1"></i> Eliminar
                                            </button>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="X" class="text-center">No se encontraron elementos</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- 3.4 PAGINACIÓN -->
                <div class="card-footer">
                   {{ $items->links('vendor.pagination.materialize') }}
                </div>
            </div>
        </div>
    </div>
</div>
```

## 🎨 Elementos Estándar

### 1. Alertas con Iconos
- ✅ Success: `ri-check-line`
- ❌ Error: `ri-error-warning-line`
- ⚠️ Warning: `ri-alert-line`
- ℹ️ Info: `ri-information-line`

### 2. Estadísticas (Cards)
- Border coloreado a la izquierda
- Icono con fondo semi-transparente
- Números grandes y descriptivos

### 3. Filtros Estándar
- Búsqueda con icono
- Estado (Activo/Inactivo)
- Paginación (10, 25, 50)
- Botones: Limpiar y Exportar

### 4. Tabla
- Header con iconos
- Ordenamiento clickeable
- Acciones con tooltips
- Estado vacío descriptivo

### 5. Switch Toggle para Estado
```blade
<div class="form-check form-switch">
    <input class="form-check-input" 
           type="checkbox" 
           wire:click="toggleStatus({{ $item->id }})" 
           {{ $item->activo ? 'checked' : '' }}
           id="switch{{ $item->id }}">
    <label class="form-check-label" for="switch{{ $item->id }}">
        <span class="badge bg-label-{{ $item->activo ? 'success' : 'secondary' }}">
            {{ $item->activo ? 'Activo' : 'Inactivo' }}
        </span>
    </label>
</div>
```

## 📊 Iconos por Módulo

- **Empresas**: `ri-building-line`
- **Sucursales**: `ri-community-line`
- **Usuarios**: `ri-user-line`
- **Estudiantes**: `ri-user-3-line`
- **Series**: `ri-file-list-3-line`
- **Pagos**: `ri-bank-card-line`
- **Matrículas**: `ri-graduation-cap-line`
- **Conceptos**: `ri-price-tag-3-line`

## 🔧 Componentes Livewire Estándar

Todos los componentes Index deben tener:

```php
use WithPagination, Exportable;

public $search = '';
public $status = '';
public $sortBy = 'created_at';
public $sortDirection = 'desc';
public $perPage = 10;

protected $queryString = [
    'search' => ['except' => ''],
    'status' => ['except' => ''],
    'sortBy' => ['except' => 'created_at'],
    'sortDirection' => ['except' => 'desc'],
    'perPage' => ['except' => 10]
];

public function updatingSearch() { $this->resetPage(); }
public function updatingStatus() { $this->resetPage(); }

public function sortBy($field)
{
    if ($this->sortBy === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortDirection = 'asc';
    }
    $this->sortBy = $field;
}

public function clearFilters()
{
    $this->search = '';
    $this->status = '';
    $this->sortBy = 'created_at';
    $this->sortDirection = 'desc';
    $this->perPage = 10;
    $this->resetPage();
}

public function toggleStatus($model)
{
    $model->status = $model->status === 'active' ? 'inactive' : 'active';
    $model->save();
    session()->flash('message', 'Estado actualizado correctamente.');
}

public function delete($model)
{
    $model->delete();
    session()->flash('message', 'Elemento eliminado correctamente.');
    $this->resetPage();
}
```rtBy($field)
{
    if ($this->sortBy === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortBy = $field;
        $this->sortDirection = 'asc';
    }
}

public function toggleStatus($id)
{
    $item = Model::find($id);
    $item->update(['activo' => !$item->activo]);
    session()->flash('message', 'Estado actualizado correctamente');
}

public function export()
{
    // Lógica de exportación
}
```