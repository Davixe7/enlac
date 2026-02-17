<?php

namespace App\Http\Controllers;

use App\Http\Resources\BeneficiaryAttendanceResource;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function candidates(Request $request)
    {
        $categoryId = $request->plan_category_id;
        $targetDate = $request->date ?: now();

        $candidates = Candidate::query()
            ->select([
                'candidates.id',
                'candidates.first_name',
                'candidates.middle_name',
                'candidates.last_name',
            ])
            ->addSelect([
                'total_activities' => Activity::selectRaw('count(*)')
                    ->join('activity_plan', 'activities.id',    '=', 'activity_plan.activity_id')
                    ->join('plans', 'activity_plan.plan_id',    '=', 'plans.id')
                    ->join('candidate_group', 'plans.group_id', '=', 'candidate_group.group_id')
                    ->whereColumn('candidate_group.candidate_id', 'candidates.id')
                    ->where('plans.category_id', $categoryId)
            ])
            ->addSelect([
                'done_activities' => Activity::selectRaw('count(*)')
                    ->join('activity_plan', 'activities.id', '=', 'activity_plan.activity_id')
                    ->join('plans', 'activity_plan.plan_id', '=', 'plans.id')
                    ->join('candidate_group', 'plans.group_id', '=', 'candidate_group.group_id')
                    ->join('activity_daily_scores', function($join) use ($targetDate) {
                        $join->on('activities.id', '=', 'activity_daily_scores.activity_id')
                            ->on('candidate_group.candidate_id', '=', 'activity_daily_scores.candidate_id')
                            ->whereDate('activity_daily_scores.date', $targetDate);
                    })
                    ->whereColumn('candidate_group.candidate_id', 'candidates.id')
                    ->where('plans.category_id', $categoryId)
            ])
            ->whereHas('groups.plans', function($q) use ($categoryId) {
                $q
                ->where('category_id', $categoryId)
                ->where('start_date', '<=', now()->format('Y-m-d'))
                ->where('end_date', '>=', now()->format('Y-m-d'));
            })
            ->get();

        return BeneficiaryAttendanceResource::collection($candidates);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $date = $request->date ?: now();
        $data = Attendance::where('date', $request->date)->wherePlanCategoryId($request->plan_category_id)->get();
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validación
        $validated = $request->validate([
            'attendances'                    => 'required|array|min:1',
            'attendances.*.plan_category_id' => 'required|exists:plan_categories,id',
            'attendances.*.candidate_id'     => 'required|exists:candidates,id',
            'attendances.*.status'           => 'required', // Ajusta según tu escala
        ]);

        $today = \Carbon\Carbon::now()->toDateString(); // '2026-01-18'

        try {
            DB::beginTransaction();

            foreach ($validated['attendances'] as $item) {
                Attendance::updateOrCreate(
                    [
                        'candidate_id'     => $item['candidate_id'],
                        'plan_category_id' => $item['plan_category_id'],
                        'date'             => $today,
                        'type'             => 'area',
                    ],
                    ['status' => $item['status']]
                );

                if( $item['status'] == 'present'){
                    Attendance::updateOrCreate(
                        ['candidate_id' => $item['candidate_id'], 'date' => $today, 'type'=>'daily'],
                        ['status'       => 'present']
                    );
                }
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

    public function update(Request $request, Attendance $attendance){
        $data = $request->validate([
            'absence_justification_type' => 'required',
        ]);

        $attendance->update($data);

        return response()->json(['data'=>$attendance], 200);
    }
}
