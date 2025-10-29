<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Activitylog\Models\Activity;
use Livewire\WithPagination;
use App\Models\User;

class ActivityLog extends Component
{
    use WithPagination;

    public $search = '';
    public $userFilter = '';
    public $dateRange = '';
    public $perPage = 10;

    protected $queryString = ['search', 'userFilter', 'dateRange'];

    public function render()
    {
        $activities = Activity::with(['causer', 'subject'])
            ->when($this->search, function ($query) {
                $query->where('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('causer', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->userFilter, function ($query) {
                $query->where('causer_id', $this->userFilter);
            })
            ->when($this->dateRange, function ($query) {
                $dates = match($this->dateRange) {
                    'today' => [now()->startOfDay(), now()->endOfDay()],
                    'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
                    'week' => [now()->startOfWeek(), now()->endOfWeek()],
                    'month' => [now()->startOfMonth(), now()->endOfMonth()],
                    default => null,
                };

                if ($dates) {
                    $query->whereBetween('created_at', $dates);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $users = User::orderBy('name')->get();

        return view('livewire.admin.activity-log', compact('activities', 'users'))
            ->layout('components.layouts.admin', ['title' => 'Seguimiento de Actividades']);
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->userFilter = '';
        $this->dateRange = '';
    }
    
    public function getActionColor($action)
    {
        return match($action) {
            'created' => 'success',
            'updated' => 'warning',
            'deleted' => 'danger',
            default => 'primary',
        };
    }

    public function export()
    {
        $activities = Activity::with(['causer', 'subject'])
            ->when($this->search, function ($query) {
                $query->where('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('causer', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->userFilter, function ($query) {
                $query->where('causer_id', $this->userFilter);
            })
            ->when($this->dateRange, function ($query) {
                $dates = match($this->dateRange) {
                    'today' => [now()->startOfDay(), now()->endOfDay()],
                    'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
                    'week' => [now()->startOfWeek(), now()->endOfWeek()],
                    'month' => [now()->startOfMonth(), now()->endOfMonth()],
                    default => null,
                };

                if ($dates) {
                    $query->whereBetween('created_at', $dates);
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Fecha y Hora',
            'Usuario',
            'Acción',
            'Elemento',
            'ID del Elemento',
            'Detalles'
        ];

        $data = $activities->map(function ($activity) {
            return [
                'created_at' => $activity->created_at->format('d/m/Y H:i:s'),
                'user' => $activity->causer ? $activity->causer->name : 'Sistema',
                'action' => $activity->description,
                'subject_type' => $activity->subject ? class_basename($activity->subject_type) : 'N/A',
                'subject_id' => $activity->subject_id,
                'properties' => json_encode($activity->properties, JSON_UNESCAPED_UNICODE)
            ];
        });

        $filename = 'registro_actividades_' . now()->format('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function () use ($headers, $data) {
            $file = fopen('php://output', 'w');
            
            // Escribir encabezados
            fputcsv($file, $headers);
            
            // Escribir datos
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}