<?php

namespace App\Livewire\Admin\Extensiones;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Extension;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;

class Edit extends Component
{
    use HasDynamicLayout;

    public $extension;
    public $empresa_id = '';
    public $sucursal_id = '';
    public $nombre = '';
    public $zona = '';
    public $distrito = '';
    public $status = '';

    public $empresas;
    public $sucursales = [];

    protected function rules()
    {
        return [
            'empresa_id' => 'nullable|exists:empresas,id',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'nombre' => 'required|string|min:3|max:100',
            'zona' => 'nullable|string|max:100',
            'distrito' => 'nullable|string|max:100',
            'status' => 'required|in:Activo,Inactivo,Pendiente',
        ];
    }

    public function mount(Extension $extension)
    {
        // if (!Auth::user()->can('edit extensiones')) {
        //     abort(403, 'No tienes permiso para editar extensiones.');
        // }

        $this->extension = $extension;
        $this->empresa_id = $extension->empresa_id;
        $this->sucursal_id = $extension->sucursal_id;
        $this->nombre = $extension->nombre;
        $this->zona = $extension->zona;
        $this->distrito = $extension->distrito;
        $this->status = $extension->status;

        $this->empresas = Empresa::where('status', true)->get();
        
        if ($this->empresa_id) {
            $this->sucursales = Sucursal::where('empresa_id', $this->empresa_id)->where('status', true)->get();
        }
    }

    public function updatedEmpresaId($value)
    {
        if ($value) {
            $this->sucursales = Sucursal::where('empresa_id', $value)->where('status', true)->get();
        } else {
            $this->sucursales = [];
        }
        $this->sucursal_id = '';
    }

    public function save()
    {
        $this->validate();

        $this->extension->update([
            'empresa_id' => $this->empresa_id ?: null,
            'sucursal_id' => $this->sucursal_id ?: null,
            'nombre' => $this->nombre,
            'zona' => $this->zona,
            'distrito' => $this->distrito,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Extensión actualizada correctamente.');

        return redirect()->route('admin.extensiones.index');
    }

    public function render()
    {
        return view('livewire.admin.extensiones.edit')->layout($this->getLayout());
    }
}
