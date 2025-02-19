<?php

namespace Database\Seeders;

use App\Models\BrainFunction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrainFunctionseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BrainFunction::create([
            'name' => 'Auditory',
        ]);

        BrainFunction::create([
            'name' => 'Visual',
        ]);

        BrainFunction::create([
            'name' => 'Tactile',
        ]);

        BrainFunction::create([
            'name' => 'Motor',
        ]);

        BrainFunction::create([
            'name' => 'Language',
        ]);
    }
}
