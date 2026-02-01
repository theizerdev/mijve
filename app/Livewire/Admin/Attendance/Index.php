<?php

namespace App\Livewire\Admin\Attendance;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Attendance;
use App\Models\Section;
use App\Models\SchoolPeriod;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;

    public $search = '';
    public $section_id = '';
    public $school_period_id = '';
    public $status = '';
    public $date_from = '';
    public $date_to = '';
    public $sortBy = 'date';
    public $sortDirection = 'desc';
    public $perPage = 25;

    protected $queryString = [
        'search' => ['except' => ''],
        'section_id' => ['except' => ''],
        'school_period_id' => ['except' => ''],
        'status' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
    ];

    public function mount()
    {
        $this->date_from = now()->startOfMonth()->format('Y-m-d');
        $this->date_to = now()->format('Y-m-d');
    }

    public function getStatsProperty()
    {
        $query = Attendance::query();
        
        if ($this->date_from && $this->date_to) {
            $query->whereBetween('date', [$this->date_from, $this->date_to]);
        }

        $total = $query->count();
        $present = $query->clone()->where('status', 'present')->count();
        $absent = $query->clone()->where('status', 'absent')->count();
        $late = $query->clone()->where('status', 'late')->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'attendance_rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
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
            $this->sortDirection = 'asc';
        }
        $this->sortBy = $field;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->section_id = '';
        $this->school_period_id = '';
        $this->status = '';
        $this->date_from = now()->startOfMonth()->format('Y-m-d');
        $this->date_to = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function render()
    {
        $attendances = Attendance::with(['student', 'section', 'schoolPeriod', 'registeredBy'])
            ->when($this->search, function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('nombres', 'like', '%' . $this->search . '%')
                      ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                      ->orWhere('codigo', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->section_id, fn($q) => $q->where('section_id', $this->section_id))
            ->when($this->school_period_id, fn($q) => $q->where('school_period_id', $this->school_period_id))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->date_from, fn($q) => $q->whereDate('date', '>=', $this->date_from))
            ->when($this->date_to, fn($q) => $q->whereDate('date', '<=', $this->date_to))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $sections = Section::active()->orderBy('nombre')->get();
        $schoolPeriods = SchoolPeriod::where('is_active', true)->orderBy('year', 'desc')->get();
        $statuses = Attendance::getStatuses();

        return view('livewire.admin.attendance.index', compact('attendances', 'sections', 'schoolPeriods', 'statuses'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Control de Asistencia';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.attendance.index' => 'Asistencia'
        ];
    }
}
