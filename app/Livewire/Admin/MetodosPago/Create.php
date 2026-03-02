<?php

namespace App\Livewire\Admin\MetodosPago;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\MetodoPago;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    use HasDynamicLayout;

    public $empresa_id = '';
    public $sucursal_id = '';
    public $tipo_pago = 'Pago Móvil';
    public $banco = '';
    public $nombre = '';
    public $apellido = '';
    public $cedula = '';
    public $telefono = '';
    public $numero_cuenta = '';
    public $tipo_cuenta = '';

    public $empresas;
    public $sucursales = [];

    protected function rules()
    {
        $rules = [
            'empresa_id' => 'nullable|exists:empresas,id',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'tipo_pago' => 'required|in:Divisa,Pago Móvil,Transferencia Bancaria',
        ];

        if ($this->tipo_pago === 'Pago Móvil') {
            $rules['cedula'] = 'required|string|max:20';
            $rules['telefono'] = 'required|string|max:20';
            $rules['banco'] = 'required|string|max:100';
        } elseif ($this->tipo_pago === 'Transferencia Bancaria') {
            $rules['nombre'] = 'required|string|max:100';
            $rules['apellido'] = 'required|string|max:100';
            $rules['cedula'] = 'required|string|max:20';
            $rules['numero_cuenta'] = 'required|string|max:50';
            $rules['tipo_cuenta'] = 'required|in:Ahorro,Corriente';
            $rules['banco'] = 'required|string|max:100';
        }

        return $rules;
    }

    public function mount()
    {
        if (!Auth::user()->can('create metodos_pago')) {
            abort(403, 'No tienes permiso para crear métodos de pago.');
        }

        $this->empresas = Empresa::where('status', true)->get();
        
        if (Auth::user()->empresa_id) {
            $this->empresa_id = Auth::user()->empresa_id;
            $this->updatedEmpresaId($this->empresa_id);
        }
        
        if (Auth::user()->sucursal_id) {
            $this->sucursal_id = Auth::user()->sucursal_id;
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

        MetodoPago::create([
            'empresa_id' => auth()->user()->empresa_id ?: null,
            'sucursal_id' => auth()->user()->sucursal_id ?: null,
            'tipo_pago' => $this->tipo_pago,
            'banco' => $this->banco,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'cedula' => $this->cedula,
            'telefono' => $this->telefono,
            'numero_cuenta' => $this->numero_cuenta,
            'tipo_cuenta' => $this->tipo_cuenta,
            'status' => true
        ]);

        session()->flash('message', 'Método de pago creado correctamente.');

        return redirect()->route('admin.metodos-pago.index');
    }

    public function render()
    {
        return view('livewire.admin.metodos-pago.create')->layout($this->getLayout());
    }
}
