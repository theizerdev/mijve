<?php

namespace App\Livewire\Admin\Empresas;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Empresa;

class Edit extends Component
{
    public $empresa;
    public $razon_social = '';
    public $documento = '';
    public $direccion = '';
    public $latitud = '';
    public $longitud = '';
    public $address = '';
    public $representante_legal = '';
    public $status = true;
    public $telefono = '';
    public $email = '';

    protected $rules = [
        'razon_social' => 'required|string|max:255',
        'documento' => 'required|string',
        'direccion' => 'nullable|string',
        'latitud' => 'required|numeric|between:-90,90',
        'longitud' => 'required|numeric|between:-180,180',
        'address' => 'nullable|string',
        'representante_legal' => 'nullable|string|max:255',
        'status' => 'boolean',
        'telefono' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
    ];

    #[On('location-updated')]
    public function updateLocation($latitude, $longitude, $address)
    {
        $this->latitud = $latitude;
        $this->longitud = $longitude;
        $this->address = $address;
        $this->direccion = $address;
    }

    public function mount(Empresa $empresa)
    {
        $this->empresa = $empresa;
        $this->razon_social = $empresa->razon_social;
        $this->documento = $empresa->documento;
        $this->direccion = $empresa->direccion;
        $this->latitud = $empresa->latitud ?: -12.0464;
        $this->longitud = $empresa->longitud ?: -77.0428;
        $this->address = $empresa->direccion;
        $this->representante_legal = $empresa->representante_legal;
        $this->status = $empresa->status;
        $this->telefono = $empresa->telefono;
        $this->email = $empresa->email;

        // Actualizar la regla de validación para permitir el documento actual
        $this->rules['documento'] = 'required|string|unique:empresas,documento,' . $empresa->id;
    }

    public function save()
    {
        $this->validate();

        $this->empresa->update([
            'razon_social' => $this->razon_social,
            'documento' => $this->documento,
            'direccion' => $this->address ?: $this->direccion,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
            'representante_legal' => $this->representante_legal,
            'status' => $this->status,
            'telefono' => $this->telefono,
            'email' => $this->email,
        ]);

        session()->flash('message', 'Empresa actualizada correctamente.');

        return redirect()->route('admin.empresas.index');
    }

    public function render()
    {
        return view('livewire.admin.empresas.edit')
            ->layout('components.layouts.admin', [
                'title' => 'Editar Empresa'
            ]);
    }
}