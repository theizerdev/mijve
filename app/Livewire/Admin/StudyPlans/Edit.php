<?php

namespace App\Livewire\Admin\StudyPlans;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\StudyPlan;
use App\Models\Programa;
use App\Models\NivelEducativo;

class Edit extends Component
{
    use HasDynamicLayout;

    public StudyPlan $studyPlan;
    
    public $name = '';
    public $code = '';
    public $description = '';
    public $program_id = '';
    public $educational_level_id = '';
    public $total_credits = 0;
    public $total_hours = 0;
    public $duration_years = 0;
    public $duration_semesters = 0;
    public $status = 'active';
    public $effective_date = '';
    public $expiration_date = '';
    public $is_default = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:50|unique:study_plans,code,{studyPlan}',
        'description' => 'nullable|string|max:1000',
        'program_id' => 'required|exists:programas,id',
        'educational_level_id' => 'required|exists:nivel_educativos,id',
        'total_credits' => 'nullable|integer|min:0',
        'total_hours' => 'nullable|integer|min:0',
        'duration_years' => 'nullable|integer|min:0',
        'duration_semesters' => 'nullable|integer|min:0',
        'status' => 'required|in:active,inactive',
        'effective_date' => 'nullable|date',
        'expiration_date' => 'nullable|date|after_or_equal:effective_date',
        'is_default' => 'boolean',
    ];

    public function mount(StudyPlan $studyPlan)
    {
        $this->studyPlan = $studyPlan;
        $this->name = $studyPlan->name;
        $this->code = $studyPlan->code;
        $this->description = $studyPlan->description;
        $this->program_id = $studyPlan->program_id;
        $this->educational_level_id = $studyPlan->educational_level_id;
        $this->total_credits = $studyPlan->total_credits;
        $this->total_hours = $studyPlan->total_hours;
        $this->duration_years = $studyPlan->duration_years;
        $this->duration_semesters = $studyPlan->duration_semesters;
        $this->status = $studyPlan->status;
        $this->effective_date = $studyPlan->effective_date?->format('Y-m-d');
        $this->expiration_date = $studyPlan->expiration_date?->format('Y-m-d');
        $this->is_default = $studyPlan->is_default;
    }

    public function save()
    {
        $this->validate();

        try {
            // Si este plan va a ser por defecto, desactivar otros planes por defecto del mismo programa y nivel
            if ($this->is_default && !$this->studyPlan->is_default) {
                StudyPlan::where('program_id', $this->program_id)
                    ->where('educational_level_id', $this->educational_level_id)
                    ->where('is_default', true)
                    ->where('id', '!=', $this->studyPlan->id)
                    ->update(['is_default' => false]);
            }

            $this->studyPlan->update([
                'name' => $this->name,
                'code' => $this->code,
                'description' => $this->description,
                'program_id' => $this->program_id,
                'educational_level_id' => $this->educational_level_id,
                'total_credits' => $this->total_credits,
                'total_hours' => $this->total_hours,
                'duration_years' => $this->duration_years,
                'duration_semesters' => $this->duration_semesters,
                'status' => $this->status,
                'effective_date' => $this->effective_date,
                'expiration_date' => $this->expiration_date,
                'is_default' => $this->is_default,
                'updated_by' => auth()->id(),
            ]);

            session()->flash('message', 'Plan de estudio actualizado correctamente.');
            return redirect()->route('admin.study-plans.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el plan de estudio: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $programs = Programa::orderBy('nombre')->get();
        $educationalLevels = NivelEducativo::orderBy('nombre')->get();

        return view('livewire.admin.study-plans.edit', compact('programs', 'educationalLevels'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Editar Plan de Estudio: ' . $this->studyPlan->name;
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.study-plans.index' => 'Planes de Estudio',
            '#' => 'Editar'
        ];
    }
}