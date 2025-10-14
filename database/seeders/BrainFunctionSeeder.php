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
        $brainFunctions = [
            'Capacidad Visual',
            'Capacidad Auditiva',
            'Capacidad Táctil',
            'Movilidad',
            'Lenguaje',
            'Capacidad Manual'
        ];
        
        foreach($brainFunctions as $functionName){
            BrainFunction::create(['name' => $functionName]);
        }
    }
}
