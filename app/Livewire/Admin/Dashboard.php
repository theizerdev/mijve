<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Traits\HasDynamicLayout;
use App\Models\Participante;
use App\Models\Extension;
use App\Models\Pago;
use App\Models\Actividad;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;

class Dashboard extends Component
{
    use HasDynamicLayout;

    public $stats = [];
    public $recentParticipants = [];
    public $recentPayments = [];
    public $monthlyPaymentsChart = ['labels' => [], 'data' => []];
    public $participantsByExtensionChart = ['labels' => [], 'data' => []];
    public $paymentStatusChart = ['labels' => [], 'data' => []];
    public $recentActivity = [];
    public $isAdmin = false;
    public $isLider = false;

    public function mount()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $this->isAdmin = $user->hasRole(['Super Administrador', 'Administrador']);
        $this->isLider = $user->hasRole('Líder de Jóvenes');

        $this->loadStats();
        $this->loadChartData();
        $this->loadRecentActivity();
    }

    private function loadStats()
    {
        $user = Auth::user();

        if ($this->isAdmin) {
            $participantesQuery = Participante::forUser();
            $pagosQuery = Pago::forUser();
            $extensionesQuery = Extension::forUser();
            $actividadesQuery = Actividad::forUser();

            $totalParticipantes = $participantesQuery->count();
            $lastMonthParticipantes = Participante::forUser()
                ->where('created_at', '<', now()->startOfMonth())
                ->where('created_at', '>=', now()->subMonth()->startOfMonth())
                ->count();

            $this->stats = [
                'total_participantes' => $totalParticipantes,
                'total_extensiones' => $extensionesQuery->count(),
                'total_actividades' => $actividadesQuery->count(),
                'total_pagos' => Pago::forUser()->where('status', 'Aprobado')->sum('monto_euro'),
                'pagos_mes' => Pago::forUser()->where('status', 'Aprobado')->whereMonth('fecha_pago', now()->month)->whereYear('fecha_pago', now()->year)->sum('monto_euro'),
                'pagos_pendientes' => Pago::forUser()->where('status', 'Pendiente')->count(),
                'pagos_aprobados' => Pago::forUser()->where('status', 'Aprobado')->count(),
                'participantes_mes' => Participante::forUser()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
                'pagos_semana' => Pago::forUser()->whereBetween('fecha_pago', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'tendencia_participantes' => $lastMonthParticipantes > 0
                    ? round((($totalParticipantes - $lastMonthParticipantes) / $lastMonthParticipantes) * 100, 1)
                    : 0,
            ];

            $this->recentParticipants = Participante::forUser()->with('extension')->latest()->take(5)->get();
            $this->recentPayments = Pago::forUser()->with('participante')->latest()->take(5)->get();

        } elseif ($this->isLider) {
            $misExtensionesIds = Extension::where('user_id', $user->id)->pluck('id');

            $participantesQuery = Participante::forUser()->whereIn('extension_id', $misExtensionesIds);
            $totalParticipantes = $participantesQuery->count();

            $lastMonthParticipantes = Participante::forUser()
                ->whereIn('extension_id', $misExtensionesIds)
                ->where('created_at', '<', now()->startOfMonth())
                ->where('created_at', '>=', now()->subMonth()->startOfMonth())
                ->count();

            $participanteIds = Participante::forUser()->whereIn('extension_id', $misExtensionesIds)->pluck('id');

            $this->stats = [
                'total_participantes' => $totalParticipantes,
                'total_extensiones' => count($misExtensionesIds),
                'total_actividades' => Actividad::forUser()->count(),
                'total_pagos' => Pago::forUser()->whereIn('participante_id', $participanteIds)->where('status', 'Aprobado')->sum('monto_euro'),
                'pagos_mes' => Pago::forUser()->whereIn('participante_id', $participanteIds)->where('status', 'Aprobado')->whereMonth('fecha_pago', now()->month)->whereYear('fecha_pago', now()->year)->sum('monto_euro'),
                'pagos_pendientes' => Pago::forUser()->whereIn('participante_id', $participanteIds)->where('status', 'Pendiente')->count(),
                'pagos_aprobados' => Pago::forUser()->whereIn('participante_id', $participanteIds)->where('status', 'Aprobado')->count(),
                'mi_extension' => Extension::where('user_id', $user->id)->first(),
                'participantes_mes' => Participante::forUser()->whereIn('extension_id', $misExtensionesIds)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
                'pagos_semana' => Pago::forUser()->whereIn('participante_id', $participanteIds)->whereBetween('fecha_pago', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'tendencia_participantes' => $lastMonthParticipantes > 0
                    ? round((($totalParticipantes - $lastMonthParticipantes) / $lastMonthParticipantes) * 100, 1)
                    : 0,
            ];

            $this->recentParticipants = Participante::forUser()
                ->whereIn('extension_id', $misExtensionesIds)
                ->with(['extension', 'actividad'])
                ->latest()
                ->take(5)
                ->get();

            $this->recentPayments = Pago::forUser()
                ->whereIn('participante_id', $participanteIds)
                ->with('participante')
                ->latest()
                ->take(5)
                ->get();
        }
    }

    private function loadChartData()
    {
        $user = Auth::user();
        $misExtensionesIds = null;
        $participanteIds = null;

        if ($this->isLider) {
            $misExtensionesIds = Extension::where('user_id', $user->id)->pluck('id');
            $participanteIds = Participante::forUser()->whereIn('extension_id', $misExtensionesIds)->pluck('id');
        }

        // Monthly payments chart (last 6 months, approved only)
        try {
            $labels = [];
            $data = [];

            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->translatedFormat('M Y');

                $query = Pago::forUser()
                    ->where('status', 'Aprobado')
                    ->whereMonth('fecha_pago', $date->month)
                    ->whereYear('fecha_pago', $date->year);

                if ($participanteIds !== null) {
                    $query->whereIn('participante_id', $participanteIds);
                }

                $data[] = (float) $query->sum('monto_euro');
            }

            $this->monthlyPaymentsChart = ['labels' => $labels, 'data' => $data];
        } catch (\Exception $e) {
            $this->monthlyPaymentsChart = ['labels' => [], 'data' => []];
        }

        // Participants by extension chart (admin: top 10)
        try {
            $query = Participante::forUser()
                ->select('extension_id', DB::raw('COUNT(*) as total'))
                ->groupBy('extension_id')
                ->orderByDesc('total')
                ->limit(10);

            if ($misExtensionesIds !== null) {
                $query->whereIn('extension_id', $misExtensionesIds);
            }

            $results = $query->get();
            $extensionNames = Extension::whereIn('id', $results->pluck('extension_id'))->pluck('nombre', 'id');

            $this->participantsByExtensionChart = [
                'labels' => $results->map(fn($r) => $extensionNames[$r->extension_id] ?? 'Sin extensión')->toArray(),
                'data' => $results->pluck('total')->toArray(),
            ];
        } catch (\Exception $e) {
            $this->participantsByExtensionChart = ['labels' => [], 'data' => []];
        }

        // Payment status chart
        try {
            $query = Pago::forUser()
                ->select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status');

            if ($participanteIds !== null) {
                $query->whereIn('participante_id', $participanteIds);
            }

            $results = $query->get();

            $statusMap = ['Pendiente' => 0, 'Aprobado' => 0, 'Rechazado' => 0];
            foreach ($results as $result) {
                if (isset($statusMap[$result->status])) {
                    $statusMap[$result->status] = $result->total;
                }
            }

            $this->paymentStatusChart = [
                'labels' => array_keys($statusMap),
                'data' => array_values($statusMap),
            ];
        } catch (\Exception $e) {
            $this->paymentStatusChart = ['labels' => [], 'data' => []];
        }
    }

    private function loadRecentActivity()
    {
        try {
            $this->recentActivity = Activity::latest()->take(10)->get();
        } catch (\Exception $e) {
            $this->recentActivity = collect();
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard')->layout($this->getLayout());
    }
}
