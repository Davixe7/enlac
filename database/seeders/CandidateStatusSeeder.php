<?php

namespace Database\Seeders;

use App\Models\Candidate;
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
            ['name' => 'pendiente',        'label'  => 'Pendiente'],
            ['name' => 'rechazado',        'label'  => 'Rechazado'],
            ['name' => 'aceptado',         'label'  => 'Pendiente de ingresar'],
            ['name' => 'listo',            'label'  => 'Listo para ingresar'],
            ['name' => 'programado',       'label'  => 'Ingreso programado'],
            ['name' => 'activo',           'label'  => 'Activo'],
            ['name' => 'graduado',         'label'  => 'Graduado'],
            ['name' => 'fallecido',        'label'  => 'Fallecido'],
            ['name' => 'exenlac',          'label'  => 'Ex-Enlac'],
            ['name' => 'inactivo',         'label'  => 'Inactivo'],
            ['name' => 'prueba_vida',      'label'  => 'Prueba de vida'],
            ['name' => 'permiso_temporal', 'label'  => 'Permiso temporal'],
        ]);

        $candidates = Candidate::get();
        $candidates->each(function($c){
            $c->update([
                'candidate_status_id' => is_null($c->admission_status) ? 1 : $c->admission_status + 2
            ]);
        });
    }
}
