<?php

namespace App\Livewire\Admin\ConductRecords;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ConductRecord;
use App\Models\Section;
use App\Models\SchoolPeriod;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;

    public $search = '';
    public $section_id = '';
    public $school_period_id = '';
    public $type = '';
    public $severity = '';
    public $resolved = '';
    public $sortBy = 'date';
    public $sortDirection = 'desc';
    public $perPage = 25;

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'severity' => ['except' => ''],
        'resolved' => ['except' => ''],
    ];

    public function getStatsProperty()
    {
        $query = ConductRecord::query();
        if ($this->school_period_id) {
            $query->where('school_period_id', $this->school_period_id);
        }

        return [
            'total' => $query->count(),
            'positive' => $query->clone()->where('type', 'positive')->count(),
            'negative' => $query->clone()->whereIn('type', ['negative', 'warning', 'sanction'])->count(),
            'unresolved' => $query->clone()->where('resolved', false)->whereIn('type', ['negative', 'warning', 'sanction'])->count(),
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'desc';
        }
        $this->sortBy = $field;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->section_id = '';
        $this->type = '';
        $this->severity = '';
        $this->resolved = '';
        $this->resetPage();
    }

    public function delete($id)
    {
        ConductRecord::findOrFail($id)->delete();
        session()->flash('message', 'Registro eliminado correctamente.');
    }

    public function render()
    {
        $records = ConductRecord::with(['student', 'section', 'schoolPeriod', 'registeredByUser'])
            ->when($this->search, function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('nombres', 'like', '%' . $this->search . '%')
                      ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                      ->orWhere('codigo', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->section_id, fn($q) => $q->where('section_id', $this->section_id))
            ->when($this->school_period_id, fn($q) => $q->where('school_period_id', $this->school_period_id))
            ->when($this->type, fn($q) => $q->where('type', $this->type))
            ->when($this->severity, fn($q) => $q->where('severity', $this->severity))
            ->when($this->resolved !== '', fn($q) => $q->where('resolved', $this->resolved === '1'))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $sections = Section::active()->orderBy('nombre')->get();
        $schoolPeriods = SchoolPeriod::orderBy('year', 'desc')->get();
        $types = ConductRecord::getTypes();
        $severities = ConductRecord::getSeverities();

        return view('livewire.admin.conduct-records.index', compact('records', 'sections', 'schoolPeriods', 'types', 'severities'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Libro de Vida';
    }
}
