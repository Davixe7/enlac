<?php

namespace App\Http\Controllers;

use App\Models\ActivityDailyScore;
use App\Models\Attendance;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityDailyScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request){
        $mode = $request->mode ?: 'user';
        $date = $request->date ?: now()->format('Y-m-d');
        
        $data = Candidate::join('candidate_group', 'candidates.id', '=', 'candidate_group.candidate_id')
        ->join('plans', 'plans.group_id', '=', 'candidate_group.group_id')
        ->where('plans.category_id', $request->category_id)
        ->where('plans.start_date', '<=', $date)
        ->where('plans.end_date', '>=', $date)
        ->where('plans.status', '1')
        ->join('activity_plan', 'activity_plan.plan_id', '=', 'plans.id')
        ->join('activities', 'activities.id', '=', 'activity_plan.activity_id')
        ->leftJoin('activity_daily_scores', function($join) use ($date) {
            $join->on('activity_daily_scores.activity_id', '=', 'activity_plan.activity_id')
            ->on('activity_daily_scores.candidate_id', '=', 'candidates.id')
            ->whereDate('activity_daily_scores.date', '=', $date);
        })
        ->select(
            'candidates.id as candidate_id',
            'candidates.first_name',
            'candidates.middle_name',
            'candidates.last_name',
            DB::raw("CONCAT_WS(' ', LPAD(candidates.id, 5, '0'), candidates.first_name, candidates.middle_name, candidates.last_name) as candidate_name"),

            'activities.id as activity_id',
            'activities.name as activity_name',
            'activities.goal_type as activity_goal_type',

            'activity_plan.final_goal as activity_final_goal',

            'activity_daily_scores.score',
            'activity_daily_scores.closed',
            'activity_daily_scores.date',
            'activity_daily_scores.id',
        )
        ->get();

        $groupByKey = $mode == 'user' ? 'candidate_name' : 'activity_name';

        $data = $data->groupBy($groupByKey)
        ->map(function($items, $key)use($request, $mode){
            return [
                'name' => $key,
                'id'   => $items[0][ $mode == 'user' ? 'candidate_id' : 'activity_id'],
                'scores' => $items->values()->map(function($score) use ($key, $request){
                    return [
                        'id'           => $score->id,
                        'activity_id'  => $score->activity_id,
                        'candidate_id' => $score->candidate_id,
                        'closed'       => $score->closed ?: false,
                        'score'        => $score->score,
                        'candidate' => [ 'id' => $score->candidate_id, 'name' => $score->candidate_name],
                        'activity' => [
                            'id'        => $score->activity_id,
                            'name'      => $score->activity_name,
                            'goal_type' => $score->activity_goal_type,
                            'plan_category_id' => $request->category_id,
                            'final_goal' => $score->activity_final_goal
                        ],
                    ];
                })
            ];
        })
        ->values();

        return response()->json(compact('data'));
    }

    public function index2(Request $request)
    {
        $date = $request->date ?: now()->format('Y-m-d');
        $data = ActivityDailyScore::where('date', $date)
        ->filterByCandidate($request->candidate_id)
        ->filterByActivity($request->activity_id)
        ->with('candidate', fn($c)=> $c->select(['first_name', 'last_name', 'middle_name', 'id']))
        ->with('activity')
        ->get();
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validación
        $validated = $request->validate([
            'scores'                => 'required|array|min:1',
            'scores.*.activity_id'  => 'required|exists:activities,id',
            'scores.*.candidate_id' => 'required|exists:candidates,id',
            'scores.*.score'        => 'required', // Ajusta según tu escala
            'scores.*.closed'       => 'nullable|boolean',
        ]);

        $today = \Carbon\Carbon::now()->toDateString(); // '2026-01-18'

        try {
            DB::beginTransaction();

            Attendance::updateOrCreate([
                'candidate_id' => $request->scores[0]['candidate_id'],
                'work_area_id' => $request->scores[0]['activity']['plan_category_id'],
                'date'         => $today,
            ],
            ['status' => 'present']
            );

            foreach ($validated['scores'] as $item) {
                ActivityDailyScore::updateOrCreate(
                    [
                        'candidate_id' => $item['candidate_id'],
                        'activity_id'  => $item['activity_id'],
                        'date'         => $today,
                    ],
                    [
                        'score'       => $item['score'],
                        'closed'      => $request->closed ?: $item['closed']
                    ]
                );
            }

            DB::commit();

            return response()->json(['date' => $today], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al procesar los datos',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ActivityDailyScore $activityDailyScore)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ActivityDailyScore $activityDailyScore)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActivityDailyScore $activityDailyScore)
    {
        //
    }
}
