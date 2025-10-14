<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanCategory;
use App\Models\PlanType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlanCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plan_types = ['Físico', 'Académico', 'Expresión Artística', 'Natación', 'Equinoterapia', 'Gimnasia'];
        foreach($plan_types as $planTypeName){
            PlanCategory::create([
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
            PlanCategory::create([
                'parent_id' => 1,
                'name' => $planName,
                'label' => $planName
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
            PlanCategory::create([
                'parent_id' => 2,
                'name' => $planName,
                'label' => $planName
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
            PlanCategory::create([
                'parent_id' => 3,
                'name' => $planName,
                'label' => $planName
            ]);
        }


    }
}
