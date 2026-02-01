<?php

namespace App\Livewire\Admin\GradeReports;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\GradeReport;
use App\Models\Section;
use App\Models\SchoolPeriod;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;

    public $search = '';
    public $school_period_id = '';
    public $section_id = '';
    public $report_type = '';
    public $status = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 25;

    public function getStatsProperty()
    {
        return [
            'total' => GradeReport::count(),
            'draft' => GradeReport::where('status', 'draft')->count(),
            'approved' => GradeReport::where('status', 'approved')->count(),
            'published' => GradeReport::where('status', 'published')->count(),
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->school_period_id = '';
        $this->section_id = '';
        $this->report_type = '';
        $this->status = '';
        $this->resetPage();
    }

    public function delete($id)
    {
        $report = GradeReport::findOrFail($id);
        if ($report->status === 'draft') {
            $report->delete();
            session()->flash('message', 'Acta eliminada correctamente.');
        } else {
            session()->flash('error', 'Solo se pueden eliminar actas en estado borrador.');
        }
    }

    public function render()
    {
        $reports = GradeReport::with(['section', 'schoolPeriod', 'subject', 'evaluationPeriod', 'generatedByUser'])
            ->when($this->search, fn($q) => $q->where('report_number', 'like', '%' . $this->search . '%')
                ->orWhere('title', 'like', '%' . $this->search . '%'))
            ->when($this->school_period_id, fn($q) => $q->where('school_period_id', $this->school_period_id))
            ->when($this->section_id, fn($q) => $q->where('section_id', $this->section_id))
            ->when($this->report_type, fn($q) => $q->where('report_type', $this->report_type))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $sections = Section::active()->orderBy('nombre')->get();
        $schoolPeriods = SchoolPeriod::orderBy('year', 'desc')->get();
        $types = GradeReport::getTypes();
        $statuses = GradeReport::getStatuses();

        return view('livewire.admin.grade-reports.index', compact('reports', 'sections', 'schoolPeriods', 'types', 'statuses'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Actas de Notas';
    }
}
