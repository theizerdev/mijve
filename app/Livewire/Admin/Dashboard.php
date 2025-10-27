<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Student;
use App\Models\StudentAccessLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class Dashboard extends Component
{
    public $dateRange = 'month';
    
    protected $queryString = ['dateRange'];
    
    public function updatedDateRange()
    {
        Cache::forget('dashboard_stats_' . auth()->id() . '_' . $this->dateRange);
        $this->dispatch('dateRangeChanged');
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
        
        return view('livewire.admin.dashboard', array_merge($stats, [
            'studentsInside' => max(0, $studentsInside),
            'recentAccess' => $recentAccess,
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
