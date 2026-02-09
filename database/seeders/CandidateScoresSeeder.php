<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CandidateScoresSeeder extends Seeder
{
    public function run(): void
    {
        $candidateId = 55;
        $planId = 33;

        // 1. Obtener las actividades vinculadas al plan 32
        $activityIds = DB::table('activity_plan')
            ->where('plan_id', $planId)
            ->pluck('activity_id');

        if ($activityIds->isEmpty()) {
            $this->command->warn("El plan {$planId} no tiene actividades asociadas.");
            return;
        }

        // 2. Definir el periodo: del 1 al 28 de febrero de 2026
        $period = CarbonPeriod::create('2026-02-01', '2026-02-28');

        $dataToInsert = [];

        foreach ($period as $date) {
            foreach ($activityIds as $activityId) {
                $dataToInsert[] = [
                    'candidate_id' => $candidateId,
                    'activity_id'  => $activityId,
                    'score'        => rand(70, 100), // Score aleatorio entre 70 y 100
                    'date'         => $date->format('Y-m-d'),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }

            // Insertamos en bloques de 100 para no saturar la memoria si hay muchas actividades
            if (count($dataToInsert) >= 100) {
                DB::table('activity_daily_scores')->insert($dataToInsert);
                $dataToInsert = [];
            }
        }

        // Insertar el remanente
        if (!empty($dataToInsert)) {
            DB::table('activity_daily_scores')->insert($dataToInsert);
        }

        $this->command->info("Seeder completado: Scores generados para el candidato {$candidateId} en el plan {$planId}.");
    }
}