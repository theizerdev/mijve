<?php

namespace App\Livewire\Admin\Turnos;

use App\Models\Turno;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Edit extends Component
{
    public Turno $turno;
    public $nombre;
    public $descripcion;
    public $hora_inicio;
    public $hora_fin;
    public $status;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'required|string|max:255',
        'hora_inicio' => 'required|date_format:H:i',
        'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        'status' => 'required|in:0,1'
    ];

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio',
        'descripcion.required' => 'La descripción es obligatoria',
        'hora_inicio.required' => 'La hora de inicio es obligatoria',
        'hora_fin.required' => 'La hora de fin es obligatoria',
        'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio',
        'status.required' => 'El estado es obligatorio'
    ];

    public function mount(Turno $turno)
    {
        Gate::authorize('edit turnos', Turno::class);

        $this->turno = $turno;
        $this->nombre = $turno->nombre;
        $this->descripcion = $turno->descripcion;
        $this->hora_inicio = $turno->hora_inicio->format('H:i');
        $this->hora_fin = $turno->hora_fin->format('H:i');
        $this->status = $turno->status;
    }

    public function update()
    {
        $this->validate();

        $this->turno->update([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'status' => $this->status
        ]);

        session()->flash('message', 'Turno actualizado exitosamente');
        return redirect()->route('admin.turnos.index');
    }

    public function render()
    {
        return view('livewire.admin.turnos.edit')
            ->layout('components.layouts.admin');
    }
}