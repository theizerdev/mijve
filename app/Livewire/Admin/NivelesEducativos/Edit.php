<?php

namespace App\Livewire\Admin\NivelesEducativos;

use App\Models\NivelEducativo;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Edit extends Component
{
    public NivelEducativo $nivel;
    public $nombre = '';
    public $descripcion = '';
    public $costo = 0;
    public $numero_cuotas = 0;
    public $cuota_inicial = 0;
    public $status = true;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'costo' => 'required|numeric|min:0',
        'numero_cuotas' => 'required|integer|min:0',
        'cuota_inicial' => 'required|numeric|min:0',
        'status' => 'boolean'
    ];

    public function mount(NivelEducativo $nivel)
    {
        Gate::authorize('update', $nivel);

        $this->nivel = $nivel;
        $this->nombre = $nivel->nombre;
        $this->descripcion = $nivel->descripcion;
        $this->costo = $nivel->costo;
        $this->numero_cuotas = $nivel->numero_cuotas;
        $this->cuota_inicial = $nivel->cuota_inicial;
        $this->status = (bool)$nivel->status;

        $this->rules['nombre'] = 'required|string|max:255|unique:niveles_educativos,nombre,' . $nivel->id;
    }

    public function save()
    {
        $this->validate();
        //dd($this->cuota_inicial);
        $this->nivel->update([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'costo' => $this->costo,
            'numero_cuotas' => $this->numero_cuotas,
            'cuota_inicial' => $this->cuota_inicial,
            'status' => $this->status
        ]);

        session()->flash('message', 'Nivel educativo actualizado correctamente.');
        return redirect()->route('admin.niveles-educativos.index');
    }

    public function render()
    {
        return view('livewire.admin.niveles-educativos.edit')
            ->layout('components.layouts.admin');
    }
}
