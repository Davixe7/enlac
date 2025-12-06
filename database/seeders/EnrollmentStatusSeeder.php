<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EnrollmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $statuses2 = [
            [
                'name' => 'pending_entry',
                'label' => 'Pendiente de Ingresar',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'ready_for_entry',
                'label' => 'Listo para Ingresar',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'ready_to_schedule',
                'label' => 'Listo para Programar Ingreso',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'entry_scheduled',
                'label' => 'Ingreso Programado',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
          /*   [
                'name' => 'complete',
                'label' => 'Ingreso Completado',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ] */
        ];

        DB::table('enrollment_statuses')->insert($statuses);
    }
}