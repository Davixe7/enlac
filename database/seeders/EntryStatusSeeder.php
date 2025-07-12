<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EntryStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Pendiente de ingresar',
            'Listo para ingresar',
            'Programar ingreso',
            'Activo',
            'Permiso temporal',
            'Prueba de vida',
            'Inactivo',
        ];

        foreach( $statuses as $status){
            DB::table('entry_statuses')->insert([
                'label' => $status,
                'slug' => Str::slug( $status, '_' )
            ]);
        }
    }
}
