<?php

namespace Database\Seeders;

use App\Models\WorkArea;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workAreas = [
            'Medico',
            'Nutrición',
            'Psicología',
            'Comunicación',
            'Programa escucha'
        ];

        foreach ($workAreas as $area) {
            WorkArea::create(['name' => $area]);
        }
    }
}
