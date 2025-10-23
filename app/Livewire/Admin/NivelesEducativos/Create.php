<?php

namespace App\Livewire\Admin\NivelesEducativos;

use App\Models\NivelEducativo;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Create extends Component
{
    public $nombre = '';
    public $descripcion = '';
    public $costo = 0;
    public $numero_cuotas = 0;
    public $cuota_inicial = 0;
    public $status = true;

    protected $rules = [
        'nombre' => 'required|string|max:255|unique:niveles_educativos,nombre',
        'descripcion' => 'nullable|string',
        'costo' => 'required|numeric|min:0',
        'numero_cuotas' => 'required|integer|min:0',
        'cuota_inicial' => 'required|numeric|min:0',
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
            'costo' => $this->costo,
            'numero_cuotas' => $this->numero_cuotas,
            'cuota_inicial' => $this->cuota_inicial,
            'status' => $this->status
        ]);

        session()->flash('message', 'Nivel educativo creado correctamente.');
        return redirect()->route('admin.niveles-educativos.index');
    }

    public function render()
    {
        return view('livewire.admin.niveles-educativos.create')
            ->layout('components.layouts.admin');
    }
}
