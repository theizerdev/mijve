<?php

namespace App\Livewire\Admin\SchoolPeriods;

use App\Models\SchoolPeriod;
use Livewire\Component;

class Show extends Component
{
    public $schoolPeriod;

    public function mount(SchoolPeriod $schoolPeriod)
    {
        $this->schoolPeriod = $schoolPeriod;
    }

    public function render()
    {
        return view('livewire.admin.school-periods.show')
            ->layout('components.layouts.admin');
    }
}