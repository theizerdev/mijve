<?php

namespace App\Livewire\Admin\GradeReports;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\GradeReport;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class Show extends Component
{
    use HasDynamicLayout;

    public GradeReport $report;

    public function mount(GradeReport $gradeReport)
    {
        $this->report = $gradeReport->load(['section', 'schoolPeriod', 'subject', 'evaluationPeriod', 'empresa', 'sucursal', 'generatedByUser', 'approvedByUser']);
    }

    public function approve()
    {
        if ($this->report->status !== 'generated') {
            session()->flash('error', 'Solo se pueden aprobar actas en estado "Generada".');
            return;
        }

        $this->report->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        session()->flash('message', 'Acta aprobada correctamente.');
        $this->report->refresh();
    }

    public function publish()
    {
        if ($this->report->status !== 'approved') {
            session()->flash('error', 'Solo se pueden publicar actas aprobadas.');
            return;
        }

        $this->report->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        session()->flash('message', 'Acta publicada correctamente.');
        $this->report->refresh();
    }

    public function downloadPdf()
    {
        $pdf = Pdf::loadView('pdf.grade-report', [
            'report' => $this->report,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'acta-' . $this->report->report_number . '.pdf');
    }

    public function render()
    {
        return view('livewire.admin.grade-reports.show')
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Acta #' . $this->report->report_number;
    }
}
