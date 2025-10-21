<?php

namespace App\Livewire\Admin\SchoolYears;

use App\Models\SchoolYear;
use Livewire\Component;

class Show extends Component
{
    public $schoolYear;

    public function mount(SchoolYear $schoolYear)
    {
        $this->schoolYear = $schoolYear;
    }

    public function render()
    {
        return view('livewire.admin.school-years.show')
         ->layout('components.layouts.admin', [
                'title' => 'Detalle del año escolar'
        ]);
    }
}
