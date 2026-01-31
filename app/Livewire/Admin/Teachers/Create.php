<?php

namespace App\Livewire\Admin\Teachers;

use App\Models\Teacher;
use App\Models\User;
use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class Create extends Component
{
    use HasDynamicLayout;

    // User fields
    public $name = '';
    public $email = '';
    public $username = '';
    
    // Teacher fields
    public $employee_code = '';
    public $specialization = '';
    public $degree = '';
    public $years_experience = '';
    public $hire_date = '';
    public $is_active = true;
    public $notes = '';

    protected $rules = [
        // User validation rules
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:teachers,email',
        'username' => 'required|string|max:50|unique:users,username',
        
        // Teacher validation rules
        'employee_code' => 'required|string|max:20|unique:teachers,employee_code',
        'specialization' => 'required|string|max:100',
        'degree' => 'required|string|max:100',
        'years_experience' => 'required|integer|min:0|max:50',
        'hire_date' => 'required|date|before_or_equal:today',
        'is_active' => 'boolean',
        'notes' => 'nullable|string|max:500'
    ];

    protected $messages = [
        // User validation messages
        'name.required' => 'El nombre es obligatorio.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'El correo electrónico debe ser válido.',
        'email.unique' => 'Este correo electrónico ya está registrado.',
        'username.required' => 'El nombre de usuario es obligatorio.',
        'username.unique' => 'Este nombre de usuario ya está en uso.',
        
        // Teacher validation messages
        'employee_code.required' => 'El código de empleado es obligatorio.',
        'employee_code.unique' => 'Este código de empleado ya está en uso.',
        'specialization.required' => 'La especialización es obligatoria.',
        'degree.required' => 'El título es obligatorio.',
        'years_experience.required' => 'Los años de experiencia son obligatorios.',
        'years_experience.integer' => 'Los años de experiencia deben ser un número entero.',
        'years_experience.min' => 'Los años de experiencia no pueden ser negativos.',
        'years_experience.max' => 'Los años de experiencia no pueden exceder 50 años.',
        'hire_date.required' => 'La fecha de contratación es obligatoria.',
        'hire_date.before_or_equal' => 'La fecha de contratación no puede ser futura.'
    ];

    public function mount()
    {
        if (!auth()->check()) {
            abort(403, 'Debes estar autenticado para acceder a esta página.');
        }
        
        if (!auth()->user()->can('create teachers')) {
            abort(403, 'No tienes permiso para crear profesores.');
        }
    }

    public function save()
    {
        $this->validate();

        try {
            // Crear el rol de Profesor si no existe
            $profesorRole = Role::firstOrCreate(['name' => 'Profesor']);

            // Crear primero el usuario
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'username' => $this->username,
                'password' => Hash::make('temp123'), // Contraseña temporal
                'status' => true,
                'email_verified_at' => now(),
            ]);

            // Asignar el rol de Profesor
            $user->assignRole($profesorRole);

            // Crear el profesor asociado al usuario
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'name' => $this->name,
                'email' => $this->email,
                'employee_code' => strtoupper($this->employee_code),
                'specialization' => $this->specialization,
                'degree' => $this->degree,
                'years_experience' => $this->years_experience,
                'hire_date' => $this->hire_date,
                'is_active' => $this->is_active,
                'notes' => $this->notes,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            session()->flash('message', 'Profesor creado correctamente. El usuario ha sido creado con contraseña temporal: temp123');
            return redirect()->route('admin.teachers.show', $teacher);

        } catch (\Exception $e) {
            dd($e);
            session()->flash('error', 'Error al crear el profesor: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.teachers.create')
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Crear Profesor';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.teachers.index' => 'Profesores',
            'admin.teachers.create' => 'Crear'
        ];
    }
}