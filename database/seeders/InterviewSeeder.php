<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Interview;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory;

class InterviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $candidates = Candidate::all();
        $faker = Factory::create();

        $frasesMedicas = [
            "El paciente reporta sentirse mejor hoy.",
            "La presión arterial está ligeramente elevada.",
            "Se requieren más pruebas.",
            "La dosis del medicamento fue ajustada.",
            "Cita de seguimiento programada.",
            "Se discutieron las opciones de tratamiento con el paciente.",
            "La condición del paciente es estable.",
            "Se recomienda una dieta saludable y ejercicio.",
            "Alergias registradas.",
            "No se reportaron reacciones adversas.",
            "El pronóstico es bueno.",
            "Monitorear los síntomas de cerca.",
            "Se recomienda la remisión a un especialista.",
            "El paciente está de buen ánimo.",
            "El procedimiento quirúrgico fue exitoso."
        ];

        foreach ($candidates as $candidate) {
            Interview::create([
                'parent_name' => 'John Doe',
                'apgar_rank' => rand(1,10),
                'candidate_id' => $candidate->id,
                'sphincter' => rand(true, false),
                'signed_at' => $faker->date(),
                'observation' => $faker->randomElement($frasesMedicas),
            ]);
        }
    }
}
