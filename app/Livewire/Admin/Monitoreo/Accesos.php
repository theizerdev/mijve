<?php

namespace App\Livewire\Admin\Monitoreo;

use Livewire\Component;
use App\Models\StudentAccessLog;
use Carbon\Carbon;

class Accesos extends Component
{
    public $startDate;
    public $endDate;
    
    public function mount()
    {
        abort_unless(auth()->user()->can('view monitoreo accesos'), 403);
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }
    
    public function exportExcel()
    {
        abort_unless(auth()->user()->can('export monitoreo accesos'), 403);
        
        return response()->streamDownload(function () {
            $logs = StudentAccessLog::with('student')
                ->whereBetween('access_time', [$this->startDate, $this->endDate])
                ->orderBy('access_time', 'desc')
                ->get();
            
            $csv = fopen('php://output', 'w');
            fputcsv($csv, ['Fecha', 'Hora', 'Código', 'Estudiante', 'Grado', 'Sección', 'Tipo', 'Registrado Por']);
            
            foreach ($logs as $log) {
                fputcsv($csv, [
                    $log->access_time->format('Y-m-d'),
                    $log->access_time->format('H:i:s'),
                    $log->student->codigo,
                    $log->student->nombres . ' ' . $log->student->apellidos,
                    $log->student->grado,
                    $log->student->seccion,
                    ucfirst($log->type),
                    $log->registeredBy->name ?? 'N/A',
                ]);
            }
            
            fclose($csv);
        }, 'accesos_' . now()->format('Y-m-d_His') . '.csv');
    }
    
    public function render()
    {
        $stats = [
            'total' => StudentAccessLog::whereBetween('access_time', [$this->startDate, $this->endDate])->count(),
            'entradas' => StudentAccessLog::where('type', 'entrada')->whereBetween('access_time', [$this->startDate, $this->endDate])->count(),
            'salidas' => StudentAccessLog::where('type', 'salida')->whereBetween('access_time', [$this->startDate, $this->endDate])->count(),
        ];
        
        $byDay = StudentAccessLog::selectRaw('DATE(access_time) as date, COUNT(*) as count')
            ->whereBetween('access_time', [$this->startDate, $this->endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $byHour = StudentAccessLog::selectRaw('HOUR(access_time) as hour, COUNT(*) as count')
            ->whereBetween('access_time', [$this->startDate, $this->endDate])
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();
            
        $recent = StudentAccessLog::with(['student', 'registeredBy'])
            ->whereBetween('access_time', [$this->startDate, $this->endDate])
            ->orderBy('access_time', 'desc')
            ->take(20)
            ->get();
        
        return view('livewire.admin.monitoreo.accesos', compact('stats', 'byDay', 'byHour', 'recent'))
            ->layout('components.layouts.admin', ['title' => 'Monitoreo de Accesos']);
    }
}
