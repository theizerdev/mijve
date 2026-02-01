<?php

namespace App\Livewire\Admin\Certificates;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;

class Show extends Component
{
    use HasDynamicLayout;

    public $certificate;

    public function mount(Certificate $certificate)
    {
        $this->certificate = $certificate->load(['student', 'matricula', 'schoolPeriod', 'empresa', 'sucursal', 'issuedBy']);
    }

    public function downloadPdf()
    {
        $pdf = Pdf::loadView('pdf.certificate', [
            'certificate' => $this->certificate,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'certificado-' . $this->certificate->certificate_number . '.pdf');
    }

    public function render()
    {
        return view('livewire.admin.certificates.show')
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Certificado #' . $this->certificate->certificate_number;
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.certificates.index' => 'Certificados',
        ];
    }
}
