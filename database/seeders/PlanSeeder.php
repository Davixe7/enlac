<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plan_types = ['Fisico', 'Academico', 'Expresión Artística'];
        foreach($plan_types as $planTypeName){
            PlanType::create([
                'label' => $planTypeName,
                'name'  => Str::slug($planTypeName, '_')
            ]);
        }

        $fisicos = [
            "Programa Intensivo de 6 meses (antes normalización)",
            "Programa Asistido con Líder",
            "Matutino",
            "Vespertino",
            "Programa en Casa a Distancia",
            "Estimulación Temprana",
            "Asistido con Padres"
        ];

        foreach($fisicos as $planName){
            Plan::create([
                'plan_type_id' => 1,
                'name' => $planName
            ]);
        }

        $academico = [
            "Normalización",
            "Comunidad Infantil",
            "Casa de Niños",
            "Taller Intermedio Casa de Niños",
            "Taller",
            "Sabor Alegría"
        ];

        foreach($academico as $planName){
            Plan::create([
                'plan_type_id' => 2,
                'name' => $planName
            ]);
        }

        $artistico = [
            "Normalización",
            "For Babies",
            "In Time Protocol A",
            "In Time Protocol B",
            "In Time Protocol C",
            "In Time Protocol D",
            "Level One",
            "Spectrum",
            "Classic"
        ];

        foreach($artistico as $planName){
            Plan::create([
                'plan_type_id' => 3,
                'name' => $planName
            ]);
        }


    }
}
