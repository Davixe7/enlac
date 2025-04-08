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
            'name' => 'Capacidad Visual',
        ]);

        BrainFunction::create([
            'name' => 'Capacidad Auditiva',
        ]);

        BrainFunction::create([
            'name' => 'Capacidad Táctil',
        ]);

        BrainFunction::create([
            'name' => 'Movilidad',
        ]);

        BrainFunction::create([
            'name' => 'Lenguaje',
        ]);

        BrainFunction::create([
            'name' => 'Capacidad Manual',
        ]);
    }
}
