<?php

namespace App\Livewire\Admin\ConductRecords;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\ConductRecord;
use App\Models\Student;
use App\Models\Section;
use App\Models\SchoolPeriod;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    use HasDynamicLayout;

    public $student_id = '';
    public $school_period_id = '';
    public $section_id = '';
    public $date;
    public $type = 'neutral';
    public $severity = 'low';
    public $category = '';
    public $description = '';
    public $actions_taken = '';
    public $parent_notified = '';
    public $parent_notification_date = '';

    protected $rules = [
        'student_id' => 'required|exists:students,id',
        'school_period_id' => 'required|exists:school_periods,id',
        'date' => 'required|date',
        'type' => 'required|in:positive,negative,neutral,warning,sanction',
        'severity' => 'required|in:low,medium,high,critical',
        'description' => 'required|min:10',
    ];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        $activePeriod = SchoolPeriod::where('is_active', true)->first();
        if ($activePeriod) {
            $this->school_period_id = $activePeriod->id;
        }
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        ConductRecord::create([
            'empresa_id' => $user->empresa_id,
            'sucursal_id' => $user->sucursal_id,
            'student_id' => $this->student_id,
            'school_period_id' => $this->school_period_id,
            'section_id' => $this->section_id ?: null,
            'date' => $this->date,
            'type' => $this->type,
            'severity' => $this->severity,
            'category' => $this->category ?: null,
            'description' => $this->description,
            'actions_taken' => $this->actions_taken ?: null,
            'parent_notified' => $this->parent_notified ?: null,
            'parent_notification_date' => $this->parent_notification_date ?: null,
            'registered_by' => $user->id,
        ]);

        session()->flash('message', 'Registro de conducta guardado correctamente.');
        return redirect()->route('admin.conduct-records.index');
    }

    public function render()
    {
        $students = Student::orderBy('apellidos')->orderBy('nombres')->get();
        $sections = Section::active()->orderBy('nombre')->get();
        $schoolPeriods = SchoolPeriod::orderBy('year', 'desc')->get();
        $types = ConductRecord::getTypes();
        $typeColors = ConductRecord::getTypeColors();
        $severities = ConductRecord::getSeverities();
        $categories = ConductRecord::getCategories();

        return view('livewire.admin.conduct-records.create', compact(
            'students', 'sections', 'schoolPeriods', 'types', 'typeColors', 'severities', 'categories'
        ))->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Registrar Observación';
    }
}
