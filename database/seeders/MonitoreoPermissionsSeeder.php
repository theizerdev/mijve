<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MonitoreoPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'access monitoreo',
            'view monitoreo servidor',
            'view monitoreo base-datos',
            'view monitoreo estudiantes',
            'view monitoreo accesos',
            'export monitoreo accesos',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
