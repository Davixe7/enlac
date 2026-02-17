<?php

namespace Database\Seeders;

use App\Models\ActivityDailyScore;
use App\Models\ActivityPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DailyScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 5 planes para el grupo personal del candidato con id 55

        $candidateId = 55;
        $startDate = now()->subMonth();
        $endDate = now();

        // Obtener el grupo personal del candidato
        $personalGroupId = DB::table('groups')
            ->join('candidate_group', 'groups.id', '=', 'candidate_group.group_id')
            ->where('candidate_group.candidate_id', $candidateId)
            ->where('groups.is_individual', true)
            ->value('groups.id');

        if (!$personalGroupId) {
            throw new \Exception("No personal group found for candidate with id {$candidateId}");
        }

        // Obtener categorías disponibles para planes de ese grupo
        $categories = DB::table('plan_categories')->where('parent_id', null)->pluck('id')->toArray();

        for ($i = 1; $i <= 6; $i++) {
            // Seleccionar una categoría al azar para el plan
            $categoryId = $categories[$i - 1];

            // Crear el plan
            $planId = DB::table('plans')->insertGetId([
                'name'        => 'Plan Auto ' . ($i),
                'group_id'    => $personalGroupId,
                'category_id' => $categoryId,
                'subcategory_id' => $categoryId,
                'start_date'  => $startDate,
                'end_date'    => $endDate,
                'created_at'  => now(),
                'updated_at'  => now(),
                'status'      => 1
            ]);

            // Obtener 5 actividades de la misma categoría
            $activities = DB::table('activities')
                ->where('plan_category_id', $categoryId)
                ->inRandomOrder()
                ->limit(5)
                ->get();

            foreach ($activities as $activity) {
                $date = $startDate->copy();
                $activityPlan = ActivityPlan::create([
                    'plan_id'      => $planId,
                    'activity_id'  => $activity->id,
                    'daily_goal'   => trim($activity->goal_type) == 'Normal' ? rand(1,100) : null
                ]);

                $totalDays = $startDate->diffInDays($endDate);
                for ($ii = 0; $ii < $totalDays; $ii++) {
                    // Insertar activity_daily_score para el día actual
                    ActivityDailyScore::create([
                        'candidate_id' => $candidateId,
                        'activity_plan_id' => $activityPlan->id,
                        'score'        => trim($activity->goal_type) == 'Dominio' ? ['presentada', 'en proceso', 'dominada', 'ninguna'][rand(0, 3)] : rand(0,100),
                        'date'         => $date,
                        'closed'       => false,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                    $date->addDay();
                }
            }
        }
    }
}
