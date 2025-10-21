<?php

namespace App\Livewire\Admin\SchoolYears;

use App\Models\SchoolYear;
use Livewire\Component;

class Create extends Component
{
    public $name;
    public $start_date;
    public $end_date;
    public $is_active = true;
    public $description;

    protected $rules = [
        'name' => 'required|string|max:255|unique:school_years',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'is_active' => 'boolean',
        'description' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'name.unique' => 'Ya existe un año escolar con este nombre.',
        'start_date.required' => 'La fecha de inicio es obligatoria.',
        'end_date.required' => 'La fecha de fin es obligatoria.',
        'end_date.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
    ];

    public function render()
    {
        return view('livewire.admin.school-years.create')
        ->layout('components.layouts.admin', [
                'title' => 'Nuevo año escolar'
        ]);
    }

    public function store()
    {
        $this->validate();

        // Verificar si hay solapamiento de fechas con otros años escolares
        $overlapping = SchoolYear::where(function ($query) {
            $query->whereBetween('start_date', [$this->start_date, $this->end_date])
                ->orWhereBetween('end_date', [$this->start_date, $this->end_date])
                ->orWhere(function ($q) {
                    $q->where('start_date', '<=', $this->start_date)
                        ->where('end_date', '>=', $this->end_date);
                });
        })->exists();

        if ($overlapping) {
            $this->addError('start_date', 'Ya existe un año escolar que se solapa con estas fechas.');
            $this->addError('end_date', 'Ya existe un año escolar que se solapa con estas fechas.');
            return;
        }

        SchoolYear::create([
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Año escolar creado exitosamente.');
        return redirect()->route('admin.school-years.index');
    }
}
