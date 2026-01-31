<?php

namespace App\Livewire\Admin\Classroom;

use App\Models\Classroom;
use App\Models\Empresa;
use App\Models\Sucursal;
use Livewire\Component;
use App\Traits\HasDynamicLayout;

class Edit extends Component
{
    use HasDynamicLayout;
    public $classroom;
    public $nombre, $codigo, $ubicacion, $capacidad, $tipo_aula, $recursos = [];
    public $is_active;
    public $empresa_id, $sucursal_id;

    protected $rules = [
        'nombre' => 'required|string|max:100',
        'codigo' => 'required|string|max:20',
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
        'capacidad.required' => 'La capacidad es obligatoria.',
        'capacidad.min' => 'La capacidad debe ser al menos 1.',
        'capacidad.max' => 'La capacidad no puede exceder 200 estudiantes.',
    ];

    public function mount(Classroom $classroom)
    {
        $this->classroom = $classroom;
        $this->nombre = $classroom->nombre;
        $this->codigo = $classroom->codigo;
        $this->ubicacion = $classroom->ubicacion;
        $this->capacidad = $classroom->capacidad;
        $this->tipo_aula = $classroom->tipo_aula;
        $this->recursos = $classroom->recursos ?: [];
        $this->is_active = $classroom->is_active;
        $this->empresa_id = $classroom->empresa_id;
        $this->sucursal_id = $classroom->sucursal_id;
    }

    public function updatedEmpresaId()
    {
        $this->sucursal_id = null;
    }

    public function update()
    {
        $rules = $this->rules;
        $rules['codigo'] = 'required|string|max:20|unique:classrooms,codigo,' . $this->classroom->id;
        
        $this->validate($rules);

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
            'updated_by' => auth()->id(),
        ];

        $this->classroom->update($data);
        
        session()->flash('message', 'Aula actualizada exitosamente.');
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

        return $this->renderWithLayout('livewire.admin.classroom.edit', [
            'empresas' => $empresas,
            'sucursales' => $sucursales,
        ], [
            'title' => 'Editar Aula',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.classrooms.index' => 'Aulas',
                'admin.classrooms.edit' => 'Editar'
            ]
        ]);
    }
}