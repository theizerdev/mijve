<?php

namespace App\Livewire\Admin\Certificates;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Certificate;
use App\Models\SchoolPeriod;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;

    public $search = '';
    public $certificate_type = '';
    public $school_period_id = '';
    public $status = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 25;

    protected $queryString = [
        'search' => ['except' => ''],
        'certificate_type' => ['except' => ''],
        'school_period_id' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function getStatsProperty()
    {
        return [
            'total' => Certificate::count(),
            'active' => Certificate::where('status', 'active')->count(),
            'this_month' => Certificate::whereMonth('issue_date', now()->month)->count(),
            'by_type' => Certificate::selectRaw('certificate_type, count(*) as total')
                ->groupBy('certificate_type')
                ->pluck('total', 'certificate_type')
                ->toArray(),
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
        $this->certificate_type = '';
        $this->school_period_id = '';
        $this->status = '';
        $this->resetPage();
    }

    public function revoke($id)
    {
        $certificate = Certificate::findOrFail($id);
        $certificate->update([
            'status' => Certificate::STATUS_REVOKED,
            'revocation_date' => now(),
            'revocation_reason' => 'Revocado por el administrador',
        ]);
        
        session()->flash('message', 'Certificado revocado correctamente.');
    }

    public function render()
    {
        $certificates = Certificate::with(['student', 'matricula', 'schoolPeriod', 'issuedBy'])
            ->when($this->search, function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('nombres', 'like', '%' . $this->search . '%')
                      ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                      ->orWhere('codigo', 'like', '%' . $this->search . '%');
                })->orWhere('certificate_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->certificate_type, fn($q) => $q->where('certificate_type', $this->certificate_type))
            ->when($this->school_period_id, fn($q) => $q->where('school_period_id', $this->school_period_id))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $schoolPeriods = SchoolPeriod::orderBy('year', 'desc')->get();
        $types = Certificate::getTypes();
        $statuses = Certificate::getStatuses();

        return view('livewire.admin.certificates.index', compact('certificates', 'schoolPeriods', 'types', 'statuses'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Constancias y Certificados';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.certificates.index' => 'Certificados'
        ];
    }
}
