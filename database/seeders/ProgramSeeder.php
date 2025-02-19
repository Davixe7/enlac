<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Program::create(['name' => 'Programa de Apoyo Escolar']);
        Program::create(['name' => 'Programa de Estimulación Temprana']);
        Program::create(['name' => 'Programa de Salud Infantil']);
        Program::create(['name' => 'Programa de Desarrollo Deportivo']);
        Program::create(['name' => 'Programa de Arte y Cultura']);
        Program::create(['name' => 'Programa de Educación Ambiental']);
        Program::create(['name' => 'Programa de Habilidades Sociales']);
        Program::create(['name' => 'Programa de Orientación Vocacional']);
    }
}
