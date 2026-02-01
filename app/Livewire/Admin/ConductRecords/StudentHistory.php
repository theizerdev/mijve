<?php

namespace App\Livewire\Admin\ConductRecords;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\ConductRecord;
use App\Models\Student;
use App\Models\SchoolPeriod;

class StudentHistory extends Component
{
    use HasDynamicLayout;

    public Student $student;
    public $school_period_id = '';
    public $type = '';
    public $records = [];
    public $stats = [];

    public function mount(Student $student)
    {
        $this->student = $student;
        $activePeriod = SchoolPeriod::where('is_active', true)->first();
        if ($activePeriod) {
            $this->school_period_id = $activePeriod->id;
        }
        $this->loadData();
    }

    public function updatedSchoolPeriodId()
    {
        $this->loadData();
    }

    public function updatedType()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $query = ConductRecord::where('student_id', $this->student->id)
            ->with(['section', 'registeredByUser']);

        if ($this->school_period_id) {
            $query->where('school_period_id', $this->school_period_id);
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        $this->records = $query->orderBy('date', 'desc')->get();

        // Estadísticas
        $allRecords = ConductRecord::where('student_id', $this->student->id);
        if ($this->school_period_id) {
            $allRecords->where('school_period_id', $this->school_period_id);
        }

        $this->stats = [
            'total' => $allRecords->count(),
            'positive' => $allRecords->clone()->where('type', 'positive')->count(),
            'negative' => $allRecords->clone()->whereIn('type', ['negative', 'warning', 'sanction'])->count(),
            'warnings' => $allRecords->clone()->where('type', 'warning')->count(),
            'sanctions' => $allRecords->clone()->where('type', 'sanction')->count(),
            'unresolved' => $allRecords->clone()->where('resolved', false)->whereIn('type', ['negative', 'warning', 'sanction'])->count(),
        ];
    }

    public function render()
    {
        $schoolPeriods = SchoolPeriod::orderBy('year', 'desc')->get();
        $types = ConductRecord::getTypes();

        return view('livewire.admin.conduct-records.student-history', compact('schoolPeriods', 'types'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Historial de Conducta - ' . $this->student->nombres . ' ' . $this->student->apellidos;
    }
}
