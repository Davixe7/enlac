<?php

namespace Database\Seeders;

use App\Models\BrainLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrainLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BrainLevel::create([
            'name' => 'Cortex Early',
            'grade' => 'A',
            'S' => 1,
            'P' => 2,
            'L' => 3,
        ]);

        BrainLevel::create([
            'name' => 'Cortex Initial',
            'grade' => 'B',
            'S' => 2,
            'P' => 3,
            'L' => 4,
        ]);

        BrainLevel::create([
            'name' => 'Cortex Primitive',
            'grade' => 'C',
            'S' => 3,
            'P' => 4,
            'L' => 5,
        ]);

        BrainLevel::create([
            'name' => 'Cortex Sophisticated',
            'grade' => 'D',
            'S' => 4,
            'P' => 5,
            'L' => 6,
        ]);
    }
}
