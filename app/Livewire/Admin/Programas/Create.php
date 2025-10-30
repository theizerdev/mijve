<?php

namespace App\Livewire\Admin\Programas;

use Livewire\Component;
use App\Models\Programa;
use App\Models\NivelEducativo;
use App\Traits\Multitenantable;

class Create extends Component
{
    public $nombre;
    public $descripcion;
    public $nivel_educativo_id;
    public $activo = true;
    public $nivelesEducativos;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'nivel_educativo_id' => 'required|exists:niveles_educativos,id',
            'activo' => 'boolean'
        ];
    }

    public function mount()
    {
        $this->nivelesEducativos = NivelEducativo::where('status', true)->get();
    }

    public function store()
    {
        // Verificar permiso para crear programas
        if (!auth()->user()->can('create programas')) {
            session()->flash('error', 'No tienes permiso para crear programas.');
            return redirect()->route('admin.programas.index');
        }

        $this->validate();

        try {
            Programa::create([
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
                'nivel_educativo_id' => $this->nivel_educativo_id,
                'activo' => $this->activo,
                'empresa_id' => auth()->user()->empresa_id,
                'sucursal_id' => auth()->user()->sucursal_id,
            ]);

            session()->flash('message', 'Programa creado correctamente.');
            return redirect()->route('admin.programas.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el programa: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.programas.create')
            ->layout('components.layouts.admin', [
                'title' => 'Crear Programa',
                'description' => 'Registrar un nuevo programa académico'
            ]);
    }
}