<?php

namespace App\Livewire\Admin\Schedules;

use App\Models\Schedule;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use App\Traits\HasDynamicLayout;

class Show extends Component
{
    use HasDynamicLayout;
    
    public $schedule;

    public function mount(Schedule $schedule)
    {
        $this->schedule = $schedule;
      
        // Cargar relaciones necesarias
        $this->schedule->load([
            'section' => function ($query) {
                $query->with([
                    'empresa:id,nombre',
                    'sucursal:id,nombre',
                    'periodoEscolar:id,nombre',
                    'programa:id,nombre,codigo',
                    'profesorGuia:id,nombre,email',
                    'aula:id,nombre,capacidad'
                ]);
            },
            'aula' => function ($query) {
                $query->with(['empresa:id,nombre', 'sucursal:id,nombre']);
            },
            'creador:id,nombre,email'
        ]);
    }

    public function getDayNameProperty()
    {
        $days = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];

        return $days[$this->schedule->day] ?? 'Desconocido';
    }

    public function getDurationProperty()
    {
        $start = strtotime($this->schedule->hora_inicio);
        $end = strtotime($this->schedule->hora_fin);
        $duration = ($end - $start) / 60; // en minutos

        if ($duration >= 60) {
            $hours = floor($duration / 60);
            $minutes = $duration % 60;
            return $hours . 'h ' . $minutes . 'm';
        }

        return $duration . ' minutos';
    }

    public function getStatusBadgeClassProperty()
    {
        return $this->schedule->estado ? 'badge bg-success' : 'badge bg-danger';
    }

    public function getStatusTextProperty()
    {
        return $this->schedule->estado ? 'Activo' : 'Inactivo';
    }

    public function edit()
    {
        return redirect()->route('admin.schedules.edit', $this->schedule->id);
    }

    public function delete()
    {
        if (!Gate::allows('manage-company', $this->schedule->section->empresa_id)) {
            session()->flash('error', 'No tienes permisos para eliminar este horario.');
            return;
        }

        $this->dispatch('confirm-delete', [
            'title' => '¿Estás seguro?',
            'text' => '¿Deseas eliminar este horario?',
            'method' => 'confirmDelete',
            'params' => []
        ]);
    }

    public function confirmDelete()
    {
        try {
            $this->schedule->delete();
            
            session()->flash('message', 'Horario eliminado exitosamente.');
            
            return redirect()->route('admin.schedules.index');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el horario: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.schedules.show', [
            'schedule' => $this->schedule,
            'dayName' => $this->dayName,
            'duration' => $this->duration,
            'statusBadgeClass' => $this->statusBadgeClass,
            'statusText' => $this->statusText,
        ], [
            'title' => 'Detalles del Horario',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.schedules.index' => 'Horarios',
                'admin.schedules.show' => 'Detalles'
            ]
        ]);
    }
}