<?php

namespace App\Livewire\Admin\SchoolPeriods;

use App\Models\SchoolPeriod;
use Livewire\Component;

class Edit extends Component
{
    public $schoolPeriod;
    public $name;
    public $start_date;
    public $end_date;
    public $is_active;
    public $description;

    protected $rules = [
        'name' => 'required|string|max:255|unique:school_periods,name',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'is_active' => 'boolean',
        'description' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'name.unique' => 'Ya existe un periodo escolar con este nombre.',
        'start_date.required' => 'La fecha de inicio es obligatoria.',
        'end_date.required' => 'La fecha de fin es obligatoria.',
        'end_date.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
    ];

    public function mount(SchoolPeriod $schoolPeriod)
    {
        $this->schoolPeriod = $schoolPeriod;
        $this->name = $schoolPeriod->name;
        $this->start_date = $schoolPeriod->start_date->format('Y-m-d');
        $this->end_date = $schoolPeriod->end_date->format('Y-m-d');
        $this->is_active = $schoolPeriod->is_active;
        $this->description = $schoolPeriod->description;
    }

    public function render()
    {
        return view('livewire.admin.school-periods.edit')
            ->layout('components.layouts.admin');
    }

    public function update()
    {
        // Actualizar las reglas para ignorar el nombre único del propio modelo
        $this->rules['name'] = 'required|string|max:255|unique:school_periods,name,' . $this->schoolPeriod->id;

        $this->validate();

        // Verificar si hay solapamiento de fechas con otros periodos escolares
        $overlapping = SchoolPeriod::where('id', '!=', $this->schoolPeriod->id)
            ->where(function ($query) {
                $query->whereBetween('start_date', [$this->start_date, $this->end_date])
                    ->orWhereBetween('end_date', [$this->start_date, $this->end_date])
                    ->orWhere(function ($q) {
                        $q->where('start_date', '<=', $this->start_date)
                            ->where('end_date', '>=', $this->end_date);
                    });
            })->exists();

        if ($overlapping) {
            $this->addError('start_date', 'Ya existe un periodo escolar que se solapa con estas fechas.');
            $this->addError('end_date', 'Ya existe un periodo escolar que se solapa con estas fechas.');
            return;
        }

        $this->schoolPeriod->update([
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Periodo escolar actualizado exitosamente.');
        return redirect()->route('admin.school-periods.index');
    }
}