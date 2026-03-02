<?php

namespace App\Livewire\Admin\Participantes;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Participante;
use App\Models\Empresa;
use App\Models\Actividad;
use Illuminate\Support\Facades\Auth;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination, Exportable, HasDynamicLayout;

    public $search = '';
    public $actividad_id = '';
    public $empresa_id = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'actividad_id' => ['except' => ''],
        'empresa_id' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        // Verificar permiso para ver participantes
        if (!Auth::user()->can('access participantes')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingActividadId()
    {
        $this->resetPage();
    }

    public function updatingEmpresaId()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortBy = $field;
    }

    protected function getExportQuery()
    {
        return $this->getBaseQuery();
    }

    protected function getExportHeaders(): array
    {
        return ['ID', 'Empresa', 'Nombres', 'Apellidos', 'Cédula', 'Actividad', 'Edad', 'Teléfono'];
    }

    protected function formatExportRow($participante): array
    {
        return [
            $participante->id,
            $participante->empresa->razon_social ?? 'N/A',
            $participante->nombres,
            $participante->apellidos,
            $participante->cedula,
            $participante->actividad->nombre ?? 'N/A',
            $participante->edad,
            $participante->telefono_principal
        ];
    }

    private function getBaseQuery()
    {
        $query = Participante::with(['empresa', 'actividad', 'extension']);

        // Aplicar scope solo si no es Super Administrador
        if (!Auth::user()->hasRole('Super Administrador')) {
            $query->forUser();
        }

        // Líder de Jóvenes: solo ve participantes de su extensión
        if (Auth::user()->hasRole('Líder de Jóvenes')) {
            $extensionIds = \App\Models\Extension::where('user_id', Auth::id())->pluck('id');
            $query->whereIn('extension_id', $extensionIds);
        }

        return $query
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('nombres', 'like', '%' . $this->search . '%')
                      ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                      ->orWhere('cedula', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->actividad_id, function ($query) {
                $query->where('actividad_id', $this->actividad_id);
            })
            ->when($this->empresa_id, function ($query) {
                $query->where('empresa_id', $this->empresa_id);
            });
    }

    public function render()
    {
        $participantes = $this->getBaseQuery()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        // Calcular estadísticas
        $totalParticipantes = Auth::user()->hasRole('Super Administrador') 
            ? Participante::count() 
            : Participante::forUser()->count();
        $participantesActivos = Auth::user()->hasRole('Super Administrador')
            ? Participante::where('status', true)->count()
            : Participante::forUser()->where('status', true)->count();
        $participantesInactivos = Auth::user()->hasRole('Super Administrador')
            ? Participante::where('status', false)->count()
            : Participante::forUser()->where('status', false)->count();

        return view('livewire.admin.participantes.index', [
            'participantes' => $participantes,
            'empresas' => Empresa::where('status', true)->get(),
            'actividades' => Actividad::where('status', 'Activo')->get(),
            'totalParticipantes' => $totalParticipantes,
            'participantesActivos' => $participantesActivos,
            'participantesInactivos' => $participantesInactivos
        ])->layout($this->getLayout());
    }

    public function toggleStatus(Participante $participante)
    {
        if (!Auth::user()->can('edit participantes')) {
            session()->flash('error', 'No tienes permiso para editar participantes.');
            return;
        }

        $participante->status = !$participante->status;
        $participante->save();

        session()->flash('message', 'Estado de participante actualizado correctamente.');
    }
}
