<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BeneficiaryScoreReportController extends Controller
{
    public function daily(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required'
        ]);

        $date = $request->input('date', now()->toDateString());
        $candidate_id = $request->input('candidate_id');

        $scores = DB::table('candidates')
            ->join('candidate_group', 'candidate_group.candidate_id', '=', 'candidates.id')
            ->join('plans', 'plans.group_id', '=', 'candidate_group.group_id')
            ->join('plan_categories', 'plans.category_id', '=', 'plan_categories.id')
            ->join('activity_plan', 'activity_plan.plan_id', '=', 'plans.id')
            ->join('activities', 'activity_plan.activity_id', '=', 'activities.id')
            ->leftJoin('activity_daily_scores', function ($join) use ($candidate_id, $date) {
                $join->on('activity_plan.id', '=', 'activity_daily_scores.activity_plan_id')
                    ->where('activity_daily_scores.candidate_id', '=', $candidate_id)
                    ->where('activity_daily_scores.date', '=', $date);
            })
            ->select(
                'plan_categories.id as plan_category_id',
                'plan_categories.label as plan_category_label',
                'plans.id as plan_id',
                'plans.name as plan_name',
                'activities.id as activity_id',
                'activities.name as activity_name',
                'activities.goal_type',
                'activities.measurement_unit',
                'activity_plan.daily_goal',
                'activity_daily_scores.score as score',
                'activity_daily_scores.color as color',
                'activity_daily_scores.closed as closed',
                'activity_daily_scores.activity_plan_id as activity_plan_id',
            )
            ->where('candidates.id','=', $candidate_id)
            ->where('plans.start_date', '<=', $date)
            ->where('plans.end_date', '>=', $date)
            ->orderBy('plans.name')
            ->orderBy('activities.name')
            ->get();

        // Agrupar por plan y luego mapear actividades a cada plan
        $grouped = [];
        foreach ($scores as $item) {
            $plan_id = $item->plan_id;
            if (!isset($grouped[$plan_id])) {
                $grouped[$plan_id] = [
                    'plan_id'       => $item->plan_id,
                    'plan_name'     => $item->plan_name,
                    'category_name' => $item->plan_category_label,
                    'activities'    => [],
                ];
            }
            $grouped[$plan_id]['activities'][] = [
                'activity_id'      => $item->activity_id,
                'activity_name'    => $item->activity_name,
                'measurement_unit' => $item->measurement_unit,
                'daily_goal'       => $item->daily_goal,
                'goal_type'        => $item->goal_type,
                'score'            => $item->score,
                'closed'           => $item->closed,
                'color'            => $item->color
            ];
        }
        // Convert indexed array to simple array
        $plans = array_values($grouped);

        return response()->json(['data' => $plans]);
    }

    public function monthly(Request $request, Candidate $candidate)
    {
        $request->validate([
            'start_date'   => 'required|date',
            'end_date'     => 'required|date'
        ]);

        $data = Plan::whereHas('candidates', function ($q) use ($candidate) {
            $q->whereId($candidate->id);
        })
        ->where('start_date', '>=', $request->start_date)
        ->where('end_date', '<=', $request->end_date)
        ->where('status', 1)
        ->with('category')
        ->with('activities.scores', function ($q) use ($candidate, $request) {
            $q->whereCandidateId($candidate->id)
                ->where('date', '>=', $request->start_date)
                ->where('date', '<=', $request->end_date);
        })
        ->get();

        $data->each(function ($plan) {
            $plan->activities = $plan->activities->map(function ($activity) {
                $activity->total  = trim($activity->goal_type) == 'Dominio'
                ? $activity->scores->mode('score')
                : $activity->scores->sum('score');
                return $activity;
            });
        });

        return response()->json(compact('data'));
    }

    public function getColorAttribute($activity, $item, $prevValue)
    {
        $goalType = trim($activity->goal_type);

        if ($goalType == 'Dominio') {
            if ($item->score == 'dominada') {
                return 'positive';
            } else if ($item->score == 'presentada' || $item->score == 'en proceso') {
                return 'warning';
            } else {
                return 'negative';
            }
        }

        if ($goalType == 'Normal') {
            $rate = $item->score / $activity->pivot->daily_goal;
            if ($rate >= 66.67) {
                return 'positive';
            } else if ($rate >= 33.34 && $rate <= 66.66) {
                return 'warning';
            } else {
                return 'negative';
            }
        }

        if (($goalType == 'Incremental' || $goalType == 'Acumulada')) {
            if( is_null($prevValue) ){ return 'positive'; }

            if ($item->score > $prevValue) {
                return 'positive';
            }

            if( $item->score == $prevValue ){
                return 'warning';
            }

            return 'negative';
        }
    }
}
