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
        Program::create(['name' => 'Programa escolarizado matutino']);
        Program::create(['name' => 'Programa escolarizado vespertino']);
        Program::create(['name' => 'Programa asistido con sombra']);
        Program::create(['name' => 'Programa asistido con padres']);
        Program::create(['name' => 'Sabor alegría']);
        Program::create(['name' => 'Estimulación temprana']);
        Program::create(['name' => 'Programa intensivo de 6 meses']);
        Program::create(['name' => 'Programa en casa a distancia']);
        Program::create(['name' => 'Programa comunidad infantil']);
    }
}
