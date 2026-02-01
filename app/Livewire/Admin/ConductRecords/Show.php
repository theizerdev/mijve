<?php

namespace App\Livewire\Admin\ConductRecords;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\ConductRecord;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    use HasDynamicLayout;

    public ConductRecord $record;
    public $follow_up_notes = '';
    public $follow_up_date = '';
    public $resolution_notes = '';

    public function mount(ConductRecord $conductRecord)
    {
        $this->record = $conductRecord->load(['student', 'section', 'schoolPeriod', 'registeredByUser', 'resolvedByUser']);
        $this->follow_up_notes = $this->record->follow_up_notes ?? '';
        $this->follow_up_date = $this->record->follow_up_date ? $this->record->follow_up_date->format('Y-m-d') : '';
    }

    public function addFollowUp()
    {
        $this->validate([
            'follow_up_notes' => 'required|min:5',
        ]);

        $this->record->update([
            'follow_up_notes' => $this->follow_up_notes,
            'follow_up_date' => $this->follow_up_date ?: now(),
        ]);

        session()->flash('message', 'Seguimiento agregado correctamente.');
    }

    public function markAsResolved()
    {
        $this->validate([
            'resolution_notes' => 'required|min:5',
        ]);

        $this->record->update([
            'resolved' => true,
            'resolution_date' => now(),
            'resolution_notes' => $this->resolution_notes,
            'resolved_by' => Auth::id(),
        ]);

        session()->flash('message', 'Caso marcado como resuelto.');
        $this->record->refresh();
    }

    public function render()
    {
        return view('livewire.admin.conduct-records.show')
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Detalle de Observación';
    }
}
