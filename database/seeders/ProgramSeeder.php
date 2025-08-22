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
        Program::create(['price'=>12000,'name' => 'Programa escolarizado matutino']);
        Program::create(['price'=>12000,'name' => 'Programa escolarizado vespertino']);
        Program::create(['price'=>12000,'name' => 'Programa asistido con Líder']);
        Program::create(['price'=>12000,'name' => 'Programa asistido con padres']);
        Program::create(['price'=>12000,'name' => 'Sabor alegría']);
        Program::create(['price'=>12000,'name' => 'Estimulación temprana']);
        Program::create(['price'=>12000,'name' => 'Programa intensivo de 6 meses']);
        Program::create(['price'=>12000,'name' => 'Programa en casa a distancia']);
        Program::create(['price'=>12000,'name' => 'Programa comunidad infantil']);
    }
}
