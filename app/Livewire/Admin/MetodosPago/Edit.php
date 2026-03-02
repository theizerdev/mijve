<?php

namespace App\Livewire\Admin\MetodosPago;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\MetodoPago;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;

class Edit extends Component
{
    use HasDynamicLayout;

    public $metodoPago;
    public $empresa_id = '';
    public $sucursal_id = '';
    public $tipo_pago = '';
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

    public function mount(MetodoPago $metodoPago)
    {
        if (!Auth::user()->can('edit metodos_pago')) {
            abort(403, 'No tienes permiso para editar métodos de pago.');
        }

        $this->metodoPago = $metodoPago;
        $this->empresa_id = $metodoPago->empresa_id;
        $this->sucursal_id = $metodoPago->sucursal_id;
        $this->tipo_pago = $metodoPago->tipo_pago;
        $this->banco = $metodoPago->banco;
        $this->nombre = $metodoPago->nombre;
        $this->apellido = $metodoPago->apellido;
        $this->cedula = $metodoPago->cedula;
        $this->telefono = $metodoPago->telefono;
        $this->numero_cuenta = $metodoPago->numero_cuenta;
        $this->tipo_cuenta = $metodoPago->tipo_cuenta;

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

        $this->metodoPago->update([
            'empresa_id' => $this->empresa_id ?: null,
            'sucursal_id' => $this->sucursal_id ?: null,
            'tipo_pago' => $this->tipo_pago,
            'banco' => $this->banco,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'cedula' => $this->cedula,
            'telefono' => $this->telefono,
            'numero_cuenta' => $this->numero_cuenta,
            'tipo_cuenta' => $this->tipo_cuenta,
        ]);

        session()->flash('message', 'Método de pago actualizado correctamente.');

        return redirect()->route('admin.metodos-pago.index');
    }

    public function render()
    {
        return view('livewire.admin.metodos-pago.edit')->layout($this->getLayout());
    }
}
