<?php

namespace App\Livewire\Admin\NivelesEducativos;

use App\Models\NivelEducativo;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Create extends Component
{
    public $nombre = '';
    public $descripcion = '';
    public $status = true;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'status' => 'boolean',
    ];

    public function mount()
    {
        if (!auth()->user()->can('create', NivelEducativo::class)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function save()
    {
        $this->validate();

        NivelEducativo::create([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'status' => $this->status
        ]);

        session()->flash('message', 'Nivel Educativo creado exitosamente.');
        return redirect()->route('admin.niveles-educativos.index');
    }

    public function render()
    {
        return view('livewire.admin.niveles-educativos.create')
            ->layout('components.layouts.admin', [
                'title' => 'Crear Nivel Educativo',
                'breadcrumb' => [
                    'admin.dashboard' => 'Dashboard',
                    'admin.niveles-educativos.index' => 'Niveles Educativos',
                    'admin.niveles-educativos.create' => 'Crear'
                ]
            ]);
    }
}