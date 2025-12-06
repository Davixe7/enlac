<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CandidateStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('candidate_statuses')->insert([
            ['name' => 'pendiente',  'label'  => 'Pendiente'],
            ['name' => 'rechazado',  'label'  => 'Rechazado'],
            ['name' => 'aceptado',   'label'  => 'Aceptado'],
            ['name' => 'listo',      'label'  => 'Listo'],
            ['name' => 'programado', 'label'  => 'Programado'],
            ['name' => 'activo',     'label'  => 'Activo'],
            ['name' => 'graduado',   'label'  => 'Graduado'],
            ['name' => 'fallecido',  'label'  => 'Fallecido'],
        ]);
    }
}
