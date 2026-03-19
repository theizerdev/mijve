<?php

namespace App\Livewire\Admin\Participantes;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Participante;
use App\Models\Empresa;
use App\Models\Actividad;
use App\Models\Extension;
use Illuminate\Support\Facades\Auth;
use App\Traits\Exportable;
use App\Services\WhatsAppService;

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

        if (!Auth::user()->hasRole('Super Administrador')) {
            $query->forUser();
        }

        $user = Auth::user();
        $extensionIds = \App\Models\Extension::where('user_id', $user->id)->pluck('id');
        $isLeaderRole = $user->hasRole('Líder de Jóvenes') || $user->hasRole('Lider de Jovenes');
        $isAdminRole = $user->hasRole(['Super Administrador', 'Administrador']);
        if (($isLeaderRole || $extensionIds->isNotEmpty()) && !$isAdminRole) {
            if ($extensionIds->count() > 0) {
                $query->whereIn('extension_id', $extensionIds);
            } else {
                $query->whereRaw('1=0');
            }
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

        $user = Auth::user();
        $isSuper = $user->hasRole('Super Administrador');
        $isLeaderRole = $user->hasRole('Líder de Jóvenes') || $user->hasRole('Lider de Jovenes');
        $isAdminRole = $user->hasRole('Administrador');
        $extensionIds = \App\Models\Extension::where('user_id', $user->id)->pluck('id');

        $baseQuery = $isSuper
            ? Participante::query()
            : Participante::forUser();

        if (($isLeaderRole || $extensionIds->isNotEmpty()) && !($isSuper || $isAdminRole)) {
            if ($extensionIds->count() > 0) {
                $baseQuery = $baseQuery->whereIn('extension_id', $extensionIds);
            } else {
                $baseQuery = $baseQuery->whereRaw('1=0');
            }
        }

        $totalParticipantes = (clone $baseQuery)->count();
        $participantesActivos = (clone $baseQuery)->where('status', true)->count();
        $participantesInactivos = (clone $baseQuery)->where('status', false)->count();

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

    public function delete($id)
    {
        $user = Auth::user();
        if (!($user->hasRole('Super Administrador') || $user->hasRole('Administrador'))) {
            session()->flash('error', 'No tienes permiso para eliminar participantes.');
            return;
        }

        $participante = Participante::find($id);
        if (!$participante) {
            session()->flash('error', 'Participante no encontrado.');
            return;
        }

        $participante->delete();
        session()->flash('message', 'Participante eliminado correctamente.');
        $this->resetPage();
    }

    public function sendWelcomeMessage($id)
    {
        $user = Auth::user();
        if (!($user->hasRole('Super Administrador') || $user->hasRole('Administrador'))) {
            session()->flash('error', 'No tienes permiso para enviar mensajes.');
            return;
        }

        $participante = Participante::with(['actividad', 'extension'])->find($id);
        if (!$participante) {
            session()->flash('error', 'Participante no encontrado.');
            return;
        }

        $telefonoRaw = $participante->telefono_principal ?: $participante->telefono_alternativo;
        if (empty($telefonoRaw)) {
            session()->flash('error', 'El participante no tiene teléfono registrado.');
            return;
        }

        $extension = Extension::with('lider', 'empresa.pais')->find($participante->extension_id);
        if (!$extension) {
            session()->flash('error', 'No se encontró la extensión del participante.');
            return;
        }

        $telefono = preg_replace('/[^0-9]/', '', (string) $telefonoRaw);
        $codigoPais = '58';
        if ($extension->empresa && $extension->empresa->pais) {
            $codigoPais = preg_replace('/[^0-9]/', '', $extension->empresa->pais->codigo_telefonico ?? '58');
        }
        if (str_starts_with($telefono, '0')) {
            $telefono = $codigoPais . substr($telefono, 1);
        } elseif (!str_starts_with($telefono, $codigoPais)) {
            $telefono = $codigoPais . $telefono;
        }

        $nombreParticipante = trim($participante->nombres . ' ' . $participante->apellidos);
        $nombreLider = $extension->lider?->name ?: 'tu líder';
        $actividadNombre = $participante->actividad->nombre ?? 'la actividad';

        $mensaje = "✅ Confirmación de Registro\n\n";
        $mensaje .= "Hola {$nombreParticipante}, hemos recibido tu registro para la actividad {$actividadNombre}.\n\n";
        $mensaje .= "Para completar tu inscripción, por favor comunícate con tu Líder {$nombreLider}.\n";
        $mensaje .= "El aporte de la actividad es de 20 USD en efectivo.\n";
        $mensaje .= "Podemos coordinar la forma de pago si lo necesitas. 🙌";

        $empresaId = $participante->empresa_id ?: ($extension->empresa?->id ?? null);
        $whatsapp = new WhatsAppService($empresaId);
        $result = $whatsapp->sendMessage($telefono, $mensaje);

        if ($result) {
            session()->flash('message', 'Mensaje de bienvenida enviado correctamente.');
        } else {
            session()->flash('error', 'No se pudo enviar el mensaje de bienvenida.');
        }
    }
}
