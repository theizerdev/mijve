<?php

namespace App\Livewire\Admin\Monitoreo;

use Livewire\Component;
use Livewire\Attributes\On;

class Servidor extends Component
{
    public $lastUpdate;
    
    public function mount()
    {
        abort_unless(auth()->user()->can('view monitoreo servidor'), 403);
        $this->lastUpdate = now()->format('H:i:s');
    }
    
    #[On('refresh-servidor')]
    public function refreshData()
    {
        $this->lastUpdate = now()->format('H:i:s');
    }

    public function render()
    {
        $serverInfo = [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'server_software' => request()->server('SERVER_SOFTWARE') ?? 'N/A',
            'server_os' => php_uname('s') . ' ' . php_uname('r'),
            'memory_usage' => round(memory_get_usage(true)/1048576, 2),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'disk_free_space' => round(disk_free_space('/') / 1073741824, 2),
            'disk_total_space' => round(disk_total_space('/') / 1073741824, 2),
        ];
        
        $serverInfo['disk_usage_percent'] = round(($serverInfo['disk_total_space'] - $serverInfo['disk_free_space']) / $serverInfo['disk_total_space'] * 100, 2);
        
        return view('livewire.admin.monitoreo.servidor', compact('serverInfo'))
            ->layout('components.layouts.admin', ['title' => 'Monitoreo del Servidor']);
    }
}
