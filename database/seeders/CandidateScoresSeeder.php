<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Candidate;
use App\Models\Plan;
use App\Models\PlanCategory;
use Illuminate\Database\Seeder;
use Carbon\CarbonPeriod;

class CandidateScoresSeeder extends Seeder
{
    public function run(): void
    {
        $candidate     = Candidate::find(55);
        $candidate->attendances()->delete();
        $groupId       = $candidate->groups()->first()->id;
        $planCategory  = PlanCategory::find(1);
        $activities    = Activity::where('plan_category_id', $planCategory->id)->where('goal_type', 'Normal')->limit(5)->get();
        $end           = now()->endOfMonth();
        $start         = now()->startOfYear();
        
        Plan::where('group_id', $groupId)
        ->where('category_id', $planCategory->id)
        ->delete();

        $plan          = Plan::create([
            'category_id'    => $planCategory->id,
            'subcategory_id' => $planCategory->id,
            'group_id'       => $groupId,
            'name'           => 'Plan Seed 01',
            'status'         => 1,
            'start_date'     => $start,
            'end_date'       => $end
        ]);

        foreach( $activities as $activity ){
            $plan->activityPlans()->create([
                'activity_id' => $activity->id,
                'daily_goal'  => rand(10, 100),
                'final_goal'  => null
            ]);
        }

        $activityPlans = $plan->activityPlans()->get();

        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $date) {
            if( $date->isWeekend() ){ continue; }
            $candidate->attendances()->create([
                'type'             => 'area',
                'plan_category_id' => $planCategory->id,
                'date'             => $date,
                'status'           => 'present'
            ]);

            $candidate->attendances()->create([
                'type'             => 'daily',
                'plan_category_id' => null,
                'date'             => $date,
                'status'           => 'present'
            ]);

            foreach ($activityPlans as $activityPlan) {
                $activityPlan->scores()->create([
                    'candidate_id' => $candidate->id,
                    'score'        => rand(1, 100),
                    'date'         => $date->format('Y-m-d'),
                ]);
            }
        }

        $this->command->info("Seeder completado: Scores generados para el candidato {$candidate->id} en el plan {$plan->id}.");
    }
}