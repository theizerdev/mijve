<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\ActiveSession;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $dateRange = 'year';
    
    protected $queryString = ['dateRange'];
    
    public function render()
    {
        // Obtener datos de resumen
        $totalUsers = User::count();
        $totalEmpresas = Empresa::count();
        $totalSucursales = Sucursal::count();
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();
        $totalActiveSessions = ActiveSession::where('is_active', true)->count();
        
        // Obtener datos para gráficos según el rango de fechas seleccionado
        $usersByPeriod = $this->getUsersByPeriod();
        $sessionsByStatus = $this->getSessionsByStatus();
        $usersByEmpresa = $this->getUsersByEmpresa();
        $loginsByPeriod = $this->getLoginsByPeriod();
        
        // Obtener datos de comparación
        $comparisonData = $this->getComparisonData();
        
        // Obtener información del servidor
        $serverInfo = $this->getServerInfo();
        
        // Obtener últimas sesiones
        $recentSessions = ActiveSession::with('user')
            ->orderBy('last_activity', 'desc')
            ->take(5)
            ->get();
            
        // Obtener estadísticas de permisos
        $permissionsStats = $this->getPermissionsStats();

        return view('livewire.super-admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalEmpresas' => $totalEmpresas,
            'totalSucursales' => $totalSucursales,
            'totalRoles' => $totalRoles,
            'totalPermissions' => $totalPermissions,
            'totalActiveSessions' => $totalActiveSessions,
            'usersByPeriod' => $usersByPeriod,
            'sessionsByStatus' => $sessionsByStatus,
            'usersByEmpresa' => $usersByEmpresa,
            'loginsByPeriod' => $loginsByPeriod,
            'comparisonData' => $comparisonData,
            'serverInfo' => $serverInfo,
            'recentSessions' => $recentSessions,
            'permissionsStats' => $permissionsStats,
        ])->layout('components.layouts.admin', ['title' => 'Super Admin Dashboard']);
    }
    
    public function updatedDateRange()
    {
        // Este método se ejecuta cuando dateRange cambia
        $this->dispatch('dateRangeChanged');
    }
    
    private function getDateRangeConditions()
    {
        $startDate = null;
        $periodLabels = [];
        
        switch ($this->dateRange) {
            case 'week':
                $startDate = now()->subDays(7);
                $periodLabels = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                break;
            case 'month':
                $startDate = now()->subDays(30);
                // Para simplificar, usaremos semanas en un mes
                $periodLabels = ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'];
                break;
            case 'quarter':
                $startDate = now()->subMonths(3);
                // Usaremos meses en un trimestre
                $periodLabels = [
                    now()->subMonths(2)->format('M'),
                    now()->subMonths(1)->format('M'),
                    now()->format('M')
                ];
                break;
            case 'year':
            default:
                $startDate = now()->subMonths(12);
                $periodLabels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                break;
        }
        
        return [
            'startDate' => $startDate,
            'labels' => $periodLabels
        ];
    }
    
    private function getUsersByPeriod()
    {
        $dateInfo = $this->getDateRangeConditions();
        $startDate = $dateInfo['startDate'];
        $labels = $dateInfo['labels'];
        
        if ($this->dateRange === 'week') {
            // Para una semana, agrupamos por día
            $users = User::selectRaw('DAYOFWEEK(created_at) as day, COUNT(*) as count')
                ->where('created_at', '>=', $startDate)
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('count', 'day')
                ->toArray();
                
            // Rellenar días faltantes con 0
            $result = [];
            for ($i = 1; $i <= 7; $i++) {
                $result[] = $users[$i] ?? 0;
            }
            
            return [
                'data' => $result,
                'labels' => $labels
            ];
        } elseif ($this->dateRange === 'month') {
            // Para un mes, agrupamos por semana
            $users = User::selectRaw('WEEK(created_at) as week, COUNT(*) as count')
                ->where('created_at', '>=', $startDate)
                ->groupBy('week')
                ->orderBy('week')
                ->pluck('count', 'week')
                ->toArray();
                
            // Rellenar semanas faltantes con 0
            $result = [];
            $currentWeek = (int) now()->subDays(30)->format('W');
            for ($i = 0; $i < 4; $i++) {
                $weekNumber = $currentWeek + $i;
                $result[] = $users[$weekNumber] ?? 0;
            }
            
            return [
                'data' => $result,
                'labels' => $labels
            ];
        } elseif ($this->dateRange === 'quarter') {
            // Para un trimestre, agrupamos por mes
            $users = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->where('created_at', '>=', $startDate)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();
                
            // Rellenar meses faltantes con 0
            $result = [];
            for ($i = 2; $i <= 4; $i++) { // Ajustar según el trimestre actual
                $month = now()->subMonths(4 - $i)->month;
                $result[] = $users[$month] ?? 0;
            }
            
            return [
                'data' => $result,
                'labels' => $labels
            ];
        } else {
            // Para un año, agrupamos por mes (comportamiento original)
            $users = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->where('created_at', '>=', $startDate)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();
                
            // Rellenar meses faltantes con 0
            $result = [];
            for ($i = 1; $i <= 12; $i++) {
                $result[] = $users[$i] ?? 0;
            }
            
            return [
                'data' => $result,
                'labels' => $labels
            ];
        }
    }
    
    private function getLoginsByPeriod()
    {
        $dateInfo = $this->getDateRangeConditions();
        $startDate = $dateInfo['startDate'];
        $labels = $dateInfo['labels'];
        
        if ($this->dateRange === 'week') {
            // Para una semana, agrupamos por día
            $logins = ActiveSession::selectRaw('DAYOFWEEK(login_at) as day, COUNT(*) as count')
                ->where('login_at', '>=', $startDate)
                ->whereNotNull('login_at')
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('count', 'day')
                ->toArray();
                
            // Rellenar días faltantes con 0
            $result = [];
            for ($i = 1; $i <= 7; $i++) {
                $result[] = $logins[$i] ?? 0;
            }
            
            return [
                'data' => $result,
                'labels' => $labels
            ];
        } elseif ($this->dateRange === 'month') {
            // Para un mes, agrupamos por semana
            $logins = ActiveSession::selectRaw('WEEK(login_at) as week, COUNT(*) as count')
                ->where('login_at', '>=', $startDate)
                ->whereNotNull('login_at')
                ->groupBy('week')
                ->orderBy('week')
                ->pluck('count', 'week')
                ->toArray();
                
            // Rellenar semanas faltantes con 0
            $result = [];
            $currentWeek = (int) now()->subDays(30)->format('W');
            for ($i = 0; $i < 4; $i++) {
                $weekNumber = $currentWeek + $i;
                $result[] = $logins[$weekNumber] ?? 0;
            }
            
            return [
                'data' => $result,
                'labels' => $labels
            ];
        } elseif ($this->dateRange === 'quarter') {
            // Para un trimestre, agrupamos por mes
            $logins = ActiveSession::selectRaw('MONTH(login_at) as month, COUNT(*) as count')
                ->where('login_at', '>=', $startDate)
                ->whereNotNull('login_at')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();
                
            // Rellenar meses faltantes con 0
            $result = [];
            for ($i = 2; $i <= 4; $i++) { // Ajustar según el trimestre actual
                $month = now()->subMonths(4 - $i)->month;
                $result[] = $logins[$month] ?? 0;
            }
            
            return [
                'data' => $result,
                'labels' => $labels
            ];
        } else {
            // Para un año, agrupamos por mes (comportamiento original)
            $logins = ActiveSession::selectRaw('MONTH(login_at) as month, COUNT(*) as count')
                ->where('login_at', '>=', $startDate)
                ->whereNotNull('login_at')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();
                
            // Rellenar meses faltantes con 0
            $result = [];
            for ($i = 1; $i <= 12; $i++) {
                $result[] = $logins[$i] ?? 0;
            }
            
            return [
                'data' => $result,
                'labels' => $labels
            ];
        }
    }
    
    private function getComparisonData()
    {
        $dateInfo = $this->getDateRangeConditions();
        $startDate = $dateInfo['startDate'];
        
        // Para comparación, siempre usamos un período anterior del mismo tamaño
        $periodLength = match($this->dateRange) {
            'week' => 7,
            'month' => 30,
            'quarter' => 90,
            default => 365, // year
        };
        
        $previousStartDate = clone $startDate;
        $previousStartDate->subDays($periodLength);
        
        // Obtener datos del período actual
        $currentPeriodUsers = User::where('created_at', '>=', $startDate)->count();
        $currentPeriodSessions = ActiveSession::where('login_at', '>=', $startDate)->count();
        
        // Obtener datos del período anterior
        $previousPeriodUsers = User::whereBetween('created_at', [$previousStartDate, $startDate])->count();
        $previousPeriodSessions = ActiveSession::whereBetween('login_at', [$previousStartDate, $startDate])->count();
        
        // Calcular porcentajes de cambio
        $usersChange = $previousPeriodUsers > 0 ? (($currentPeriodUsers - $previousPeriodUsers) / $previousPeriodUsers) * 100 : 0;
        $sessionsChange = $previousPeriodSessions > 0 ? (($currentPeriodSessions - $previousPeriodSessions) / $previousPeriodSessions) * 100 : 0;
        
        return [
            'users' => [
                'current' => $currentPeriodUsers,
                'previous' => $previousPeriodUsers,
                'change' => round($usersChange, 2)
            ],
            'sessions' => [
                'current' => $currentPeriodSessions,
                'previous' => $previousPeriodSessions,
                'change' => round($sessionsChange, 2)
            ]
        ];
    }
    
    private function getServerInfo()
    {
        return [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'database' => DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME),
            'server_software' => request()->server('SERVER_SOFTWARE'),
            'server_os' => php_uname('s') . ' ' . php_uname('r'),
            'memory_usage' => round(memory_get_usage(true)/1048576, 2).' MB',
            'max_execution_time' => ini_get('max_execution_time') . ' segundos'
        ];
    }
    
    private function getSessionsByStatus()
    {
        $active = ActiveSession::where('is_active', true)->count();
        $inactive = ActiveSession::where('is_active', false)->count();
        
        return [
            'active' => $active,
            'inactive' => $inactive
        ];
    }
    
    private function getUsersByEmpresa()
    {
        return User::join('empresas', 'users.empresa_id', '=', 'empresas.id')
            ->selectRaw('empresas.razon_social as empresa, COUNT(users.id) as count')
            ->groupBy('empresas.razon_social')
            ->pluck('count', 'empresa')
            ->toArray();
    }
    
    private function getPermissionsStats()
    {
        // Obtener conteo de permisos por rol
        $roles = Role::withCount('permissions')->get();
        
        $data = [];
        foreach ($roles as $role) {
            $data[$role->name] = $role->permissions_count;
        }
        
        return $data;
    }
}