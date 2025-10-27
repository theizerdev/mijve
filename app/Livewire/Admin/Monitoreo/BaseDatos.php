<?php

namespace App\Livewire\Admin\Monitoreo;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class BaseDatos extends Component
{
    public function mount()
    {
        abort_unless(auth()->user()->can('view monitoreo base-datos'), 403);
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
        
        return view('livewire.admin.monitoreo.base-datos', compact('dbInfo', 'tableStats'))
            ->layout('components.layouts.admin', ['title' => 'Monitoreo de Base de Datos']);
    }
}
