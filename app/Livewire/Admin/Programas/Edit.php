<?php

namespace App\Livewire\Admin\Programas;

use Livewire\Component;
use App\Models\Programa;
use App\Models\NivelEducativo;

class Edit extends Component
{
    public $programa;
    public $nombre;
    public $descripcion;
    public $nivel_educativo_id;
    public $activo;

    public function mount(Programa $programa)
    {
        // Verificar permiso para editar programas
        if (!auth()->user()->can('edit programas')) {
            session()->flash('error', 'No tienes permiso para editar programas.');
            return redirect()->route('admin.programas.index');
        }

        $this->programa = $programa;
        $this->nombre = $programa->nombre;
        $this->descripcion = $programa->descripcion;
        $this->nivel_educativo_id = $programa->nivel_educativo_id;
        $this->activo = $programa->activo;
    }

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'nivel_educativo_id' => 'required|exists:niveles_educativos,id',
            'activo' => 'boolean'
        ];
    }

    public function update()
    {
        // Verificar permiso para editar programas
        if (!auth()->user()->can('edit programas')) {
            session()->flash('error', 'No tienes permiso para editar programas.');
            return;
        }

        $this->validate();

        try {
            $this->programa->update([
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
                'nivel_educativo_id' => $this->nivel_educativo_id,
                'activo' => $this->activo
            ]);

            session()->flash('message', 'Programa actualizado correctamente.');
            return redirect()->route('admin.programas.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el programa: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $nivelesEducativos = NivelEducativo::where('status', true)->get();

        return view('livewire.admin.programas.edit', compact('nivelesEducativos'))
            ->layout('components.layouts.admin', [
                'title' => 'Editar Programa',
                'description' => 'Actualizar información del programa académico'
            ]);
    }
}