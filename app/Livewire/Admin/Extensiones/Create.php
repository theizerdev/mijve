<?php

namespace App\Livewire\Admin\Extensiones;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Extension;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Create extends Component
{
    use HasDynamicLayout;

    public $empresa_id = '';
    public $sucursal_id = '';
    public $nombre = '';
    public $zona = '';
    public $distrito = '';
    public $status = 'Activo';

    // Datos del Líder
    public $lider_nombre = '';
    public $lider_telefono = '';

    public $empresas;
    public $sucursales = [];

    protected $rules = [
        'empresa_id' => 'nullable|exists:empresas,id',
        'sucursal_id' => 'nullable|exists:sucursales,id',
        'nombre' => 'required|string|min:3|max:100',
        'zona' => 'nullable|string|max:100',
        'distrito' => 'nullable|string|max:100',
        'status' => 'required|in:Activo,Inactivo,Pendiente',
        'lider_nombre' => 'required|string|min:5|max:100',
        'lider_telefono' => 'required|string|min:10|max:20',
    ];

    public function mount()
    {
        // if (!Auth::user()->can('create extensiones')) {
        //     abort(403, 'No tienes permiso para crear extensiones.');
        // }

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

        // Generar credenciales de usuario
        $nombreParts = explode(' ', trim($this->lider_nombre));
        $firstName = $nombreParts[0];
        $lastName = count($nombreParts) > 1 ? end($nombreParts) : $nombreParts[0];
        
        $baseUsername = strtolower(substr($firstName, 0, 1) . $lastName);
        $username = $baseUsername;
        $counter = 1;
        
        // Evitar duplicados de username
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $email = $username . '@mail.com';
        $password = 'password';

        // Validar si el email ya existe (aunque es poco probable con la lógica anterior, mejor prevenir)
        if (User::where('email', $email)->exists()) {
            session()->flash('error', 'Error generando email automático. Por favor intente con otro nombre.');
            return;
        }

        try {
            \DB::beginTransaction();

            // 1. Crear la Extensión
            $extension = Extension::create([
                'empresa_id' => $this->empresa_id ?: null,
                'sucursal_id' => $this->sucursal_id ?: null,
                'nombre' => $this->nombre,
                'zona' => $this->zona,
                'distrito' => $this->distrito,
                'status' => $this->status,
            ]);

            // 2. Crear el Usuario Líder
            $user = User::create([
                'name' => $this->lider_nombre,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'empresa_id' => $this->empresa_id ?: null,
                'sucursal_id' => $this->sucursal_id ?: null,
                'status' => true,
                'telefono' => $this->lider_telefono,
            ]);

            // 3. Asignar Rol
            $user->assignRole('Líder de Jóvenes');

            // 4. Vincular Usuario a la Extensión
            $extension->update(['user_id' => $user->id]);

            \DB::commit();

            // 4. Enviar Notificación WhatsApp
            $this->sendWhatsAppCredentials($user, $password, $extension);

            session()->flash('message', 'Extensión y usuario líder creados correctamente.');
            return redirect()->route('admin.extensiones.index');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error creando extensión/usuario: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    private function sendWhatsAppCredentials($user, $password, $extension = null)
    {
        try {
            if (empty($user->telefono)) return;

            // 1. Limpiar el teléfono de caracteres no numéricos
            $telefono = preg_replace('/[^0-9]/', '', $user->telefono);

            // 2. Obtener código de país desde la empresa (si existe)
            $codigoPais = '58'; // Default Venezuela
            if ($user->empresa && $user->empresa->pais) {
                // Asumiendo que el modelo Pais tiene un campo 'codigo_telefono' o similar
                // Limpiamos el código de país (ej: "+58" -> "58")
                $codigoPais = preg_replace('/[^0-9]/', '', $user->empresa->pais->codigo_telefono ?? '58');
            }

            // 3. Lógica de formateo específica
            // Si el teléfono comienza con '0' (ej: 0424...), quitamos el cero y agregamos el código país
            if (substr($telefono, 0, 1) === '0') {
                $telefono = $codigoPais . substr($telefono, 1);
            } 
            // Si el teléfono no comienza con el código de país, lo agregamos
            elseif (substr($telefono, 0, strlen($codigoPais)) !== $codigoPais) {
                $telefono = $codigoPais . $telefono;
            }

            $mensaje = "👋 *Bienvenido a MIJVE*\n\n";
            $mensaje .= "Hola *{$user->name}*, has sido registrado como Líder de Jóvenes";
            
            if ($extension) {
                $mensaje .= " para la extensión *{$extension->nombre}*";
            }
            
            $mensaje .= ".\n\n";
            $mensaje .= "🔐 *Tus credenciales de acceso:*\n";
            $mensaje .= "👤 Usuario: {$user->username}\n";
            $mensaje .= "🔑 Contraseña: {$password}\n\n";
            $mensaje .= "🔗 Accede aquí: " . url('/login');

            $whatsapp = new WhatsAppService($user->empresa_id);
            $whatsapp->sendMessage($telefono, $mensaje);

        } catch (\Exception $e) {
            \Log::error('Error enviando credenciales WhatsApp: ' . $e->getMessage());
            // No detenemos el flujo si falla el mensaje, solo logueamos
        }
    }

    public function render()
    {
        return view('livewire.admin.extensiones.create')->layout($this->getLayout());
    }
}
