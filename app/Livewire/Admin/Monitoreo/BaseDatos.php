<?php

namespace App\Livewire\Admin\Monitoreo;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class BaseDatos extends Component
{
    public $lastUpdate;
    
    public function mount()
    {
        abort_unless(auth()->user()->can('view monitoreo base-datos'), 403);
        $this->lastUpdate = now()->format('H:i:s');
    }
    
    #[On('refresh-base-datos')]
    public function refreshData()
    {
        $this->lastUpdate = now()->format('H:i:s');
    }

    public function render()
    {
        $dbInfo = [
            'driver' => config('database.default'),
            'connection' => DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME),
            'database' => DB::connection()->getDatabaseName(),
            'version' => DB::select('SELECT VERSION() as version')[0]->version ?? 'N/A',
        ];
        
        $tables = DB::select('SHOW TABLE STATUS');
        $totalSize = 0;
        $tableStats = [];
        
        foreach ($tables as $table) {
            $size = ($table->Data_length + $table->Index_length) / 1048576;
            $totalSize += $size;
            $tableStats[] = [
                'name' => $table->Name,
                'rows' => $table->Rows,
                'size' => round($size, 2),
                'engine' => $table->Engine,
            ];
        }
        
        usort($tableStats, fn($a, $b) => $b['size'] <=> $a['size']);
        
        $dbInfo['total_size'] = round($totalSize, 2);
        $dbInfo['total_tables'] = count($tables);
        
        $tableStats = collect($tableStats);
        
        return view('livewire.admin.monitoreo.base-datos', compact('dbInfo', 'tableStats'))
            ->layout('components.layouts.admin', ['title' => 'Monitoreo de Base de Datos']);
    }
}
