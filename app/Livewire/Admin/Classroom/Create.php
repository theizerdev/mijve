<?php

namespace App\Livewire\Admin\Classroom;

use App\Models\Classroom;
use App\Models\Empresa;
use App\Models\Sucursal;
use Livewire\Component;
use App\Traits\HasDynamicLayout;

class Create extends Component
{
    use HasDynamicLayout;
    public $nombre, $codigo, $ubicacion, $capacidad = 30, $tipo_aula = 'regular', $recursos = [];
    public $is_active = true;
    public $empresa_id, $sucursal_id;

    protected $rules = [
        'nombre' => 'required|string|max:100',
        'codigo' => 'required|string|max:20|unique:classrooms,codigo',
        'ubicacion' => 'nullable|string|max:200',
        'capacidad' => 'required|integer|min:1|max:200',
        'tipo_aula' => 'required|in:regular,laboratorio,taller,auditorio,biblioteca,otro',
        'recursos' => 'nullable|array',
        'is_active' => 'boolean',
        'empresa_id' => 'nullable|exists:empresas,id',
        'sucursal_id' => 'nullable|exists:sucursales,id',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre del aula es obligatorio.',
        'codigo.required' => 'El código del aula es obligatorio.',
        'codigo.unique' => 'Este código ya está en uso.',
        'capacidad.required' => 'La capacidad es obligatoria.',
        'capacidad.min' => 'La capacidad debe ser al menos 1.',
        'capacidad.max' => 'La capacidad no puede exceder 200 estudiantes.',
    ];

    public function mount()
    {
        $this->empresa_id = auth()->user()->empresa_id;
    }

    public function updatedEmpresaId()
    {
        $this->sucursal_id = null;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'ubicacion' => $this->ubicacion,
            'capacidad' => $this->capacidad,
            'tipo_aula' => $this->tipo_aula,
            'recursos' => $this->recursos ?: null,
            'is_active' => $this->is_active,
            'empresa_id' => $this->empresa_id,
            'sucursal_id' => $this->sucursal_id,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ];

        Classroom::create($data);
        
        session()->flash('message', 'Aula creada exitosamente.');
        return redirect()->route('admin.classrooms.index');
    }

    public function render()
    {
        $empresas = Empresa::all();
        $sucursales = Sucursal::when($this->empresa_id, function ($query) {
            $query->where('empresa_id', $this->empresa_id);
        }, function ($query) {
            if (auth()->user()->empresa_id) {
                $query->where('empresa_id', auth()->user()->empresa_id);
            }
        })->get();

        return $this->renderWithLayout('livewire.admin.classroom.create', [
            'empresas' => $empresas,
            'sucursales' => $sucursales,
        ], [
            'title' => 'Crear Aula',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.classrooms.index' => 'Aulas',
                'admin.classrooms.create' => 'Crear'
            ]
        ]);
    }
}