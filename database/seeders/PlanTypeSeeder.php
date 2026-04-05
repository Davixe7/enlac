<?php

namespace Database\Seeders;

use App\Models\PlanCategory;
use App\Models\PlanType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlanTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            1 => [
                'Programa de Organización Neurológica (6 meses)',
                'Programa Asistido con Líder o con Padres',
                'Matutino',
                'Vespertino',
                'Programa en Casa a Distancia',
                'Estimulación Temprana'
            ],

            2 => [
                'Normalización',
                'Normalización para Niños con Autismo',
                'Comunidad Infantil',
                'Casa de Niños',
                'Taller Intermedio',
                'Taller 1: Rojo',
                'Taller 1: Azul',
                'Sabor Alegría'
            ],

            3 => [
                'Estimulación Temprana: de 3 meses a 1 año neurológico',
                'Comunidad Infantil: de 1 año a 3 años neurológicos',
                'Casa de Niños: de 3 a 6 años neurológicos',
                'Taller Intermedio',
                'Taller 1: de 6 a 9 años neurológicos',
                'Instrumento: Normalización',
                'Instrumento Nivel 1: A partir de los 3 años',
                'Instrumento Nivel 2',
                'Instrumento Nivel 3',
                'Instrumento Nivel 4',
                'Instrumento Nivel 5',
                'Instrumento Nivel 6'
            ],

            4 => ['General'],
            5 => ['General'],
            6 => ['General'],

            7 => [
                'Normalización',
                'For Babies',
                'In Time',
                'Spectrum',
                'Level One Nature'
            ]
        ];

        $data = [];

        foreach( $items as $planCategoryId => $planTypes ){
            foreach($planTypes as $planTypeLabel){
                $data[] = [
                    'plan_category_id' => $planCategoryId,
                    'label'            => $planTypeLabel,
                    'name'             => Str::slug($planTypeLabel, '_', 'es')
                ];
            }
        }

        DB::table('plan_types')->insert($data);
    }
}
