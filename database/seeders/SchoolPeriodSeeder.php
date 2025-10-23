<?php

namespace Database\Seeders;

use App\Models\SchoolPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SchoolPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $periods = [
            [
                'name' => '2025-2026',
                'start_date' => '2025-03-01',
                'end_date' => '2025-07-31',
                'description' => 'Periodo escolar 2025-2026',
                'is_active' => true,
                'is_current' => true,
            ],
        ];

        foreach ($periods as $period) {
            SchoolPeriod::updateOrCreate(
                ['name' => $period['name']],
                $period
            );
        }
    }
}
