<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Student;
use App\Models\StudentAccessLog;
use App\Models\Pago;
use App\Models\Matricula;
use App\Models\SchoolPeriod;
use App\Models\PaymentSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $dateRange = 'month';
    public $showAlerts = true;
    public $showFinancial = true;
    public $showAcademic = true;
    public $showAccess = true;
    public $showCharts = true;
    public $vencidas =0;

    protected $queryString = ['dateRange'];

    public function mount()
    {
        // Verificar permisos para cada sección del dashboard
        $this->showAlerts = auth()->user()->can('dashboard.alerts');
        $this->showFinancial = auth()->user()->can('dashboard.financial');
        $this->showAcademic = auth()->user()->can('dashboard.academic');
        $this->showAccess = auth()->user()->can('dashboard.access');
        $this->showCharts = auth()->user()->can('dashboard.charts');
        // Contar pagos vencidos
        $this->vencidas = PaymentSchedule::where('estado', 'pendiente')
            ->where('fecha_vencimiento', '<', today())
            ->count();

        // Limpiar cache para forzar recarga de datos
        Cache::forget('dashboard_stats_' . auth()->id() . '_' . $this->dateRange);
    }

    public function updatedDateRange()
    {
        Cache::forget('dashboard_stats_' . auth()->id() . '_' . $this->dateRange);
        $this->dispatch('dateRangeChanged');
    }

    public function toggleWidget($widget)
    {
        $this->{$widget} = !$this->{$widget};
    }

    public function exportDashboard()
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Dashboard Admin');

            // Obtener datos actuales directamente
            $pendingPayments = \App\Models\PaymentSchedule::where('estado', 'pendiente')
                ->where('fecha_vencimiento', '<', now())
                ->count();
            
            $alerts = [
                'pendingPayments' => $pendingPayments,
                'expiringEnrollments' => 0, // Simplificado por ahora
                'lowAttendanceStudents' => 0, // Simplificado por ahora
                'totalAlerts' => $pendingPayments
            ];
            
            $financialStats = $this->getFinancialStats();
            $academicStats = $this->getAcademicStats();
            $currentPeriod = \App\Models\SchoolPeriod::where('is_active', true)->first();

            // Encabezado principal
            $sheet->setCellValue('A1', 'DASHBOARD ADMINISTRATIVO');
            $sheet->mergeCells('A1:F1');
            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '0D6EFD']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'F8F9FA']]
            ]);

            // Información del reporte
            $sheet->setCellValue('A3', 'Fecha de generación:');
            $sheet->setCellValue('B3', now()->format('d/m/Y H:i:s'));
            $sheet->setCellValue('A4', 'Período de análisis:');
            $sheet->setCellValue('B4', ucfirst($this->dateRange));
            $sheet->setCellValue('A5', 'Usuario:');
            $sheet->setCellValue('B5', auth()->user()->name);

            $sheet->getStyle('A3:A5')->getFont()->setBold(true);

            // === MÉTRICAS PRINCIPALES ===
            $sheet->setCellValue('A7', 'MÉTRICAS PRINCIPALES');
            $sheet->mergeCells('A7:C7');
            $sheet->getStyle('A7')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'E9ECEF']]
            ]);

            $row = 9;
            $metrics = [
                ['Estudiantes Activos', \App\Models\Student::where('status', 1)->count(), 'estudiantes'],
                ['Entradas Hoy', \App\Models\StudentAccessLog::whereDate('access_time', today())->where('type', 'entrada')->count(), 'registros'],
                ['Salidas Hoy', \App\Models\StudentAccessLog::whereDate('access_time', today())->where('type', 'salida')->count(), 'registros'],
                ['Estudiantes Dentro', max(0, \App\Models\StudentAccessLog::whereDate('access_time', today())->where('type', 'entrada')->count() - \App\Models\StudentAccessLog::whereDate('access_time', today())->where('type', 'salida')->count()), 'estudiantes']
            ];

            foreach ($metrics as $metric) {
                $sheet->setCellValue('A' . $row, $metric[0]);
                $sheet->setCellValue('B' . $row, $metric[1]);
                $sheet->setCellValue('C' . $row, $metric[2]);
                $row++;
            }

            // === MÉTRICAS FINANCIERAS ===
            $row += 2;
            $sheet->setCellValue('A' . $row, 'MÉTRICAS FINANCIERAS');
            $sheet->mergeCells('A' . $row . ':C' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'D4EDDA']]
            ]);
            $row += 2;

            $financialMetrics = [
                ['Ingresos Totales', $financialStats['totalIncome'], '$'],
                ['Ingresos Hoy', $financialStats['todayIncome'], '$'],
                ['Monto por Cobrar', $financialStats['pendingIncome'], '$'],
                ['Cambio vs Anterior', $financialStats['incomeChange'] . '%', 'porcentaje']
            ];

            foreach ($financialMetrics as $metric) {
                $sheet->setCellValue('A' . $row, $metric[0]);
                $sheet->setCellValue('B' . $row, $metric[1]);
                $sheet->setCellValue('C' . $row, $metric[2]);
                $row++;
            }

            // === MÉTRICAS ACADÉMICAS ===
            $row += 2;
            $sheet->setCellValue('A' . $row, 'MÉTRICAS ACADÉMICAS');
            $sheet->mergeCells('A' . $row . ':C' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'CCE5FF']]
            ]);
            $row += 2;

            $academicMetrics = [
                ['Matrículas Activas', (int)$academicStats['activeEnrollments'], 'matrículas'],
                ['Total Matrículas', (int)$academicStats['totalEnrollments'], 'matrículas'],
                ['Período Actual', $currentPeriod ? $currentPeriod->nombre : 'N/A', 'período']
            ];

            foreach ($academicMetrics as $metric) {
                $sheet->setCellValue('A' . $row, $metric[0]);
                $sheet->setCellValue('B' . $row, $metric[1]);
                $sheet->setCellValue('C' . $row, $metric[2]);
                $row++;
            }

            // === ALERTAS ===
            $row += 2;
            $sheet->setCellValue('A' . $row, 'ALERTAS Y NOTIFICACIONES');
            $sheet->mergeCells('A' . $row . ':C' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'FFE6E6']]
            ]);
            $row += 2;

            $alertMetrics = [
                ['Cuotas Vencidas', (int)$alerts['pendingPayments'], 'cuotas'],
                ['Matrículas por Vencer', (int)$alerts['expiringEnrollments'], 'matrículas'],
                ['Baja Asistencia', (int)$alerts['lowAttendanceStudents'], 'estudiantes'],
                ['Total Alertas', (int)$alerts['totalAlerts'], 'alertas']
            ];

            foreach ($alertMetrics as $metric) {
                $sheet->setCellValue('A' . $row, $metric[0]);
                $sheet->setCellValue('B' . $row, $metric[1]);
                $sheet->setCellValue('C' . $row, $metric[2]);
                $row++;
            }

            // Encabezados de columnas
            $sheet->setCellValue('A8', 'Métrica');
            $sheet->setCellValue('B8', 'Valor');
            $sheet->setCellValue('C8', 'Unidad');
            $sheet->getStyle('A8:C8')->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
            ]);

            // Formato de moneda solo para métricas financieras (filas específicas)
            $financialStartRow = 15; // Ajustar según la posición real de métricas financieras
            $financialEndRow = 18;
            $sheet->getStyle('B' . $financialStartRow . ':B' . $financialEndRow)->getNumberFormat()->setFormatCode('$#,##0.00');

            // Configuración de columnas
            $sheet->getColumnDimension('A')->setWidth(25);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);

            // Pie de página
            $footerRow = $row + 2;
            $sheet->setCellValue('A' . $footerRow, 'Reporte generado por Sistema de Gestión Académica');
            $sheet->mergeCells('A' . $footerRow . ':C' . $footerRow);
            $sheet->getStyle('A' . $footerRow)->applyFromArray([
                'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '6C757D']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ]);

            $filename = 'dashboard_admin_' . now()->format('Y-m-d_His') . '.xlsx';

            return new \Symfony\Component\HttpFoundation\StreamedResponse(
                function () use ($spreadsheet) {
                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                    $writer->save('php://output');
                },
                200,
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Cache-Control' => 'max-age=0',
                    'Pragma' => 'public',
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Error exportando dashboard: ' . $e->getMessage());
            session()->flash('error', 'Error al generar el archivo Excel: ' . $e->getMessage());
            return;
        }
    }

    public function render()
    {
        $cacheKey = 'dashboard_stats_' . auth()->id() . '_' . $this->dateRange;

        $stats = Cache::remember($cacheKey, 300, function() {
            return [
                'totalStudents' => Student::where('status', 1)->count(),
                'todayEntries' => StudentAccessLog::whereDate('access_time', today())->where('type', 'entrada')->count(),
                'todayExits' => StudentAccessLog::whereDate('access_time', today())->where('type', 'salida')->count(),
                'accessByPeriod' => $this->getAccessByPeriod(),
                'entriesExitsByPeriod' => $this->getEntriesExitsByPeriod(),
                'studentsByGrade' => $this->getStudentsByGrade(),
                'studentsByLevel' => $this->getStudentsByLevel(),
                'accessByType' => $this->getAccessByType(),
                'topStudents' => $this->getTopStudents(),
                'comparisonData' => $this->getComparisonData(),
                'peakHours' => $this->getPeakHours(),
            ];
        });

        $recentAccess = $this->getRecentAccess();
        $studentsInside = $stats['todayEntries'] - $stats['todayExits'];

        // Nuevos datos mejorados
        $alerts = $this->getAlerts();
        $financialStats = $this->getFinancialStats();
        $academicStats = $this->getAcademicStats();
        $currentPeriod = SchoolPeriod::where('is_active', true)->first();

        return view('livewire.admin.dashboard', array_merge($stats, [
            'studentsInside' => max(0, $studentsInside),
            'recentAccess' => $recentAccess,
            'alerts' => $alerts,
            'financialStats' => $financialStats,
            'academicStats' => $academicStats,
            'currentPeriod' => $currentPeriod,
            'exportData' => $this->getExportData(),
        ]))->layout('components.layouts.admin', ['title' => 'Dashboard']);
    }

    private function getDateRangeConditions()
    {
        $startDate = match($this->dateRange) {
            'week' => now()->subDays(7),
            'month' => now()->subDays(30),
            'quarter' => now()->subMonths(3),
            'year' => now()->subMonths(12),
            default => now()->subDays(30),
        };

        $labels = match($this->dateRange) {
            'week' => ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            'month' => ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'],
            'quarter' => [now()->subMonths(2)->format('M'), now()->subMonths(1)->format('M'), now()->format('M')],
            'year' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            default => ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'],
        };

        return ['startDate' => $startDate, 'labels' => $labels];
    }

    private function getAccessByPeriod()
    {
        $dateInfo = $this->getDateRangeConditions();
        $startDate = $dateInfo['startDate'];
        $labels = $dateInfo['labels'];

        if ($this->dateRange === 'week') {
            $access = StudentAccessLog::selectRaw('DAYOFWEEK(access_time) as day, COUNT(*) as count')
                ->where('access_time', '>=', $startDate)
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('count', 'day')
                ->toArray();

            $result = [];
            for ($i = 1; $i <= 7; $i++) {
                $result[] = $access[$i] ?? 0;
            }
        } elseif ($this->dateRange === 'year') {
            $access = StudentAccessLog::selectRaw('MONTH(access_time) as month, COUNT(*) as count')
                ->where('access_time', '>=', $startDate)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();

            $result = [];
            for ($i = 1; $i <= 12; $i++) {
                $result[] = $access[$i] ?? 0;
            }
        } else {
            $access = StudentAccessLog::selectRaw('WEEK(access_time) as week, COUNT(*) as count')
                ->where('access_time', '>=', $startDate)
                ->groupBy('week')
                ->orderBy('week')
                ->pluck('count', 'week')
                ->toArray();

            $result = array_values($access);
            while (count($result) < count($labels)) {
                $result[] = 0;
            }
            $result = array_slice($result, 0, count($labels));
        }

        return ['data' => $result, 'labels' => $labels];
    }

    private function getStudentsByGrade()
    {
        return Student::selectRaw('grado, COUNT(*) as count')
            ->where('status', 1)
            ->groupBy('grado')
            ->orderBy('grado')
            ->pluck('count', 'grado')
            ->toArray();
    }

    private function getAccessByType()
    {
        $dateInfo = $this->getDateRangeConditions();

        $entries = StudentAccessLog::where('type', 'entrada')
            ->where('access_time', '>=', $dateInfo['startDate'])
            ->count();

        $exits = StudentAccessLog::where('type', 'salida')
            ->where('access_time', '>=', $dateInfo['startDate'])
            ->count();

        return ['entries' => $entries, 'exits' => $exits];
    }

    private function getAlerts()
    {
        $alerts = [
            'pendingPayments' => 0,
            'expiringEnrollments' => 0,
            'lowAttendanceStudents' => 0,
            'totalAlerts' => 0
        ];

        try {
            // Alertas de cuotas de pago vencidas (ya pasó la fecha)
            $pendingPayments = PaymentSchedule::where('estado', 'pendiente')
                ->where('fecha_vencimiento', '<', now())
                ->count();

            // Alertas de matrículas por vencer (basadas en la fecha de fin del período escolar)
            $expiringEnrollments = Matricula::where('estado', 'activo')
                ->whereHas('schoolPeriod', function($query) {
                    $query->where('end_date', '<=', now()->addDays(30));
                })
                ->count();

            // Estudiantes con baja asistencia (menos del 70% este mes)
            $lowAttendanceStudents = StudentAccessLog::where('access_time', '>=', now()->startOfMonth())
                ->where('type', 'entrada')
                ->select('student_id', DB::raw('count(*) as total_entries'))
                ->groupBy('student_id')
                ->having('total_entries', '<', 15) // Asumiendo 22 días hábiles por mes, 70% = 15.4
                ->count();

            $alerts['pendingPayments'] = $pendingPayments;
            $alerts['expiringEnrollments'] = $expiringEnrollments;
            $alerts['lowAttendanceStudents'] = $lowAttendanceStudents;
            $alerts['totalAlerts'] = $pendingPayments + $expiringEnrollments + $lowAttendanceStudents;

        } catch (\Exception $e) {
            \Log::error('Error al obtener alertas del dashboard: ' . $e->getMessage());
        }

        return $alerts;
    }

    private function getFinancialStats()
    {
        $dateInfo = $this->getDateRangeConditions();
        $startDate = $dateInfo['startDate'];

        try {
            // Ingresos del período actual - usando la nueva estructura
            $totalIncome = Pago::where('estado', 'aprobado')
                ->where('fecha', '>=', $startDate)
                ->sum('total');

            // Ingresos de hoy
            $todayIncome = Pago::where('estado', 'aprobado')
                ->whereDate('fecha', today())
                ->sum('total');

            // Pagos pendientes
            $pendingIncome = Pago::where('estado', 'pendiente')
                ->sum('total');

            // Pagos pendientes del cronograma de pagos (todas las cuotas pendientes)
            $pendingIncomeSchedule = PaymentSchedule::where('estado', 'pendiente')
                ->sum('monto');

            // Total de ingresos pendientes
            $totalPendingIncome = $pendingIncome + $pendingIncomeSchedule;

            // Período anterior para comparación
            $now = now();
            $currentPeriodDays = $startDate->diffInDays($now);
            $previousPeriodStart = $startDate->copy()->subDays($currentPeriodDays);
            $previousPeriodEnd = $startDate->copy();

            // Ingresos del período anterior
            $previousIncome = Pago::where('estado', 'aprobado')
                ->where('fecha', '>=', $previousPeriodStart)
                ->where('fecha', '<', $previousPeriodEnd)
                ->sum('total');

            // Calcular porcentaje de cambio
            $incomeChange = 0;
            if ($previousIncome > 0) {
                $incomeChange = round((($totalIncome - $previousIncome) / $previousIncome) * 100, 2);
            } elseif ($totalIncome > 0 && $previousIncome == 0) {
                $incomeChange = 100;
            }

            return [
                'totalIncome' => $totalIncome,
                'pendingIncome' => $totalPendingIncome,
                'incomeChange' => $incomeChange,
                'totalReceivable' => $totalIncome + $totalPendingIncome,
                'todayIncome' => $todayIncome,
            ];
        } catch (\Exception $e) {
            \Log::error('Error al obtener estadísticas financieras: ' . $e->getMessage());
            return [
                'totalIncome' => 0,
                'pendingIncome' => 0,
                'incomeChange' => 0,
                'totalReceivable' => 0,
                'todayIncome' => 0,
            ];
        }
    }

    private function getAcademicStats()
    {
        $currentPeriod = SchoolPeriod::where('is_active', true)->first();

        if (!$currentPeriod) {
            return [
                'totalEnrollments' => 0,
                'activeEnrollments' => 0,
                'averageGrade' => 0,
                'topStudents' => [],
            ];
        }

        // Estadísticas de matrículas
        $totalEnrollments = Matricula::where('periodo_id', $currentPeriod->id)->count();
        $activeEnrollments = Matricula::where('periodo_id', $currentPeriod->id)
            ->where('estado', 'activo')
            ->count();

        // Rendimiento académico (placeholder - se puede implementar cuando exista el módulo de notas)
        $averageGrade = 0; // Por ahora sin implementación

        // Estudiantes destacados (placeholder - se puede implementar cuando exista el módulo de notas)
        $topStudents = 0; // Por ahora sin implementación - mostrar 0

        return [
            'totalEnrollments' => $totalEnrollments,
            'activeEnrollments' => $activeEnrollments,
            'averageGrade' => round($averageGrade, 2),
            'topStudents' => $topStudents,
            'periodName' => $currentPeriod->nombre,
        ];
    }

    private function getExportData()
    {
        return [
            ['Estudiantes Activos', $this->getCacheData('totalStudents'), $this->dateRange],
            ['Entradas Hoy', $this->getCacheData('todayEntries'), 'Hoy'],
            ['Salidas Hoy', $this->getCacheData('todayExits'), 'Hoy'],
            ['Estudiantes Dentro', $this->getCacheData('studentsInside'), 'Actual'],
            ['Pagos Pendientes', $this->alerts['pendingPayments'] ?? 0, $this->dateRange],
            ['Ingresos del Período', $this->financialStats['totalIncome'] ?? 0, $this->dateRange],
        ];
    }

    private function getCacheData($key)
    {
        $cacheKey = 'dashboard_stats_' . auth()->id() . '_' . $this->dateRange;
        $stats = Cache::get($cacheKey, []);
        return $stats[$key] ?? 0;
    }

    private function getRecentAccess()
    {
        return StudentAccessLog::with(['student', 'registeredBy'])
            ->orderBy('access_time', 'desc')
            ->take(10)
            ->get();
    }

    private function getEntriesExitsByPeriod()
    {
        $dateInfo = $this->getDateRangeConditions();
        $startDate = $dateInfo['startDate'];
        $labels = $dateInfo['labels'];

        if ($this->dateRange === 'week') {
            $entries = StudentAccessLog::selectRaw('DAYOFWEEK(access_time) as day, COUNT(*) as count')
                ->where('access_time', '>=', $startDate)
                ->where('type', 'entrada')
                ->groupBy('day')
                ->pluck('count', 'day')->toArray();

            $exits = StudentAccessLog::selectRaw('DAYOFWEEK(access_time) as day, COUNT(*) as count')
                ->where('access_time', '>=', $startDate)
                ->where('type', 'salida')
                ->groupBy('day')
                ->pluck('count', 'day')->toArray();

            $entriesData = [];
            $exitsData = [];
            for ($i = 1; $i <= 7; $i++) {
                $entriesData[] = $entries[$i] ?? 0;
                $exitsData[] = $exits[$i] ?? 0;
            }
        } elseif ($this->dateRange === 'year') {
            $entries = StudentAccessLog::selectRaw('MONTH(access_time) as month, COUNT(*) as count')
                ->where('access_time', '>=', $startDate)
                ->where('type', 'entrada')
                ->groupBy('month')
                ->pluck('count', 'month')->toArray();

            $exits = StudentAccessLog::selectRaw('MONTH(access_time) as month, COUNT(*) as count')
                ->where('access_time', '>=', $startDate)
                ->where('type', 'salida')
                ->groupBy('month')
                ->pluck('count', 'month')->toArray();

            $entriesData = [];
            $exitsData = [];
            for ($i = 1; $i <= 12; $i++) {
                $entriesData[] = $entries[$i] ?? 0;
                $exitsData[] = $exits[$i] ?? 0;
            }
        } else {
            $entries = StudentAccessLog::selectRaw('WEEK(access_time) as week, COUNT(*) as count')
                ->where('access_time', '>=', $startDate)
                ->where('type', 'entrada')
                ->groupBy('week')
                ->pluck('count', 'week')->toArray();

            $exits = StudentAccessLog::selectRaw('WEEK(access_time) as week, COUNT(*) as count')
                ->where('access_time', '>=', $startDate)
                ->where('type', 'salida')
                ->groupBy('week')
                ->pluck('count', 'week')->toArray();

            $entriesData = array_values($entries);
            $exitsData = array_values($exits);

            while (count($entriesData) < count($labels)) $entriesData[] = 0;
            while (count($exitsData) < count($labels)) $exitsData[] = 0;

            $entriesData = array_slice($entriesData, 0, count($labels));
            $exitsData = array_slice($exitsData, 0, count($labels));
        }

        return ['entries' => $entriesData, 'exits' => $exitsData, 'labels' => $labels];
    }

    private function getStudentsByLevel()
    {
        return Student::join('niveles_educativos', 'students.nivel_educativo_id', '=', 'niveles_educativos.id')
            ->selectRaw('niveles_educativos.nombre as nivel, COUNT(students.id) as count')
            ->where('students.status', 1)
            ->groupBy('niveles_educativos.nombre')
            ->pluck('count', 'nivel')
            ->toArray();
    }

    private function getTopStudents()
    {
        $dateInfo = $this->getDateRangeConditions();

        return StudentAccessLog::with('student')
            ->select('student_id', \DB::raw('COUNT(*) as access_count'))
            ->where('access_time', '>=', $dateInfo['startDate'])
            ->groupBy('student_id')
            ->orderBy('access_count', 'desc')
            ->take(5)
            ->get();
    }

    private function getPeakHours()
    {
        $dateInfo = $this->getDateRangeConditions();

        return StudentAccessLog::selectRaw('HOUR(access_time) as hour, COUNT(*) as count')
            ->where('access_time', '>=', $dateInfo['startDate'])
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'hour' => str_pad($item->hour, 2, '0', STR_PAD_LEFT) . ':00',
                    'count' => $item->count
                ];
            });
    }

    private function getComparisonData()
    {
        $dateInfo = $this->getDateRangeConditions();
        $startDate = $dateInfo['startDate'];

        $periodLength = match($this->dateRange) {
            'week' => 7,
            'month' => 30,
            'quarter' => 90,
            'year' => 365,
            default => 30,
        };

        $previousStartDate = (clone $startDate)->subDays($periodLength);

        $currentAccess = StudentAccessLog::where('access_time', '>=', $startDate)->count();
        $previousAccess = StudentAccessLog::whereBetween('access_time', [$previousStartDate, $startDate])->count();

        $currentStudents = Student::where('created_at', '>=', $startDate)->count();
        $previousStudents = Student::whereBetween('created_at', [$previousStartDate, $startDate])->count();

        $accessChange = $previousAccess > 0 ? (($currentAccess - $previousAccess) / $previousAccess) * 100 : 0;
        $studentsChange = $previousStudents > 0 ? (($currentStudents - $previousStudents) / $previousStudents) * 100 : 0;

        return [
            'access' => ['current' => $currentAccess, 'change' => round($accessChange, 2)],
            'students' => ['current' => $currentStudents, 'change' => round($studentsChange, 2)],
        ];
    }
}
