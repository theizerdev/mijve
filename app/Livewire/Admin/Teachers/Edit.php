<?php

namespace App\Livewire\Admin\Teachers;

use App\Models\Teacher;
use App\Models\User;
use App\Traits\HasDynamicLayout;
use Livewire\Component;

class Edit extends Component
{
    use HasDynamicLayout;

    public Teacher $teacher;
    public $user_id;
    public $name;
    public $email;
    public $employee_code;
    public $specialization;
    public $degree;
    public $years_experience;
    public $hire_date;
    public $is_active = true;
    public $notes;

    protected function rules()
    {
        return [
            'user_id' => 'required|exists:users,id|unique:teachers,user_id,' . $this->teacher->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:teachers,email,' . $this->teacher->id,
            'employee_code' => 'required|string|max:20|unique:teachers,employee_code,' . $this->teacher->id,
            'specialization' => 'required|string|max:100',
            'degree' => 'required|string|max:100',
            'years_experience' => 'required|integer|min:0|max:50',
            'hire_date' => 'required|date|before_or_equal:today',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000'
        ];
    }

    protected function messages()
    {
        return [
            'user_id.required' => 'El usuario es obligatorio.',
            'user_id.exists' => 'El usuario seleccionado no existe.',
            'user_id.unique' => 'Este usuario ya está asignado a otro profesor.',
            'name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'employee_code.required' => 'El código de empleado es obligatorio.',
            'employee_code.unique' => 'Este código de empleado ya está en uso.',
            'specialization.required' => 'La especialización es obligatoria.',
            'degree.required' => 'El título académico es obligatorio.',
            'years_experience.required' => 'Los años de experiencia son obligatorios.',
            'years_experience.integer' => 'Los años de experiencia deben ser un número entero.',
            'years_experience.max' => 'Los años de experiencia no pueden exceder 50.',
            'hire_date.required' => 'La fecha de contratación es obligatoria.',
            'hire_date.before_or_equal' => 'La fecha de contratación no puede ser futura.'
        ];
    }

    public function mount(Teacher $teacher)
    {
        if (!auth()->check()) {
            abort(403, 'Debes estar autenticado para acceder a esta página.');
        }
        
        if (!auth()->user()->can('edit teachers')) {
            abort(403, 'No tienes permiso para editar profesores.');
        }

        $this->teacher = $teacher;
        $this->user_id = $teacher->user_id;
        $this->name = $teacher->name;
        $this->email = $teacher->email;
        $this->employee_code = $teacher->employee_code;
        $this->specialization = $teacher->specialization;
        $this->degree = $teacher->degree;
        $this->years_experience = $teacher->years_experience;
        $this->hire_date = $teacher->hire_date->format('Y-m-d');
        $this->is_active = $teacher->is_active;
        $this->notes = $teacher->notes;
    }

    public function save()
    {
        $this->validate();

        try {
            $this->teacher->update([
                'user_id' => $this->user_id,
                'name' => $this->name,
                'email' => $this->email,
                'employee_code' => $this->employee_code,
                'specialization' => $this->specialization,
                'degree' => $this->degree,
                'years_experience' => $this->years_experience,
                'hire_date' => $this->hire_date,
                'is_active' => $this->is_active,
                'notes' => $this->notes,
                'updated_by' => auth()->id()
            ]);

            session()->flash('message', 'Profesor actualizado correctamente.');
            return redirect()->route('admin.teachers.show', $this->teacher);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el profesor: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $users = User::whereDoesntHave('teacher')
            ->orWhere('id', $this->teacher->user_id)
            ->orderBy('name')
            ->get();

        return view('livewire.admin.teachers.edit', compact('users'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Editar Profesor: ' . $this->teacher->name;
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.teachers.index' => 'Profesores',
            'admin.teachers.show' => $this->teacher->name,
            '' => 'Editar'
        ];
    }
}