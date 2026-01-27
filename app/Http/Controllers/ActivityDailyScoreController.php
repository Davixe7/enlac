<?php

namespace App\Http\Controllers;

use App\Models\ActivityDailyScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityDailyScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $date = $request->date ?: now()->format('Y-m-d');
        $data = ActivityDailyScore::where('date', $date)
        ->filterByCandidate($request->candidate_id)
        ->filterByActivity($request->activity_id)
        ->with(['candidate', 'activity'])
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
        ]);

        $today = \Carbon\Carbon::now()->toDateString(); // '2026-01-18'

        try {
            DB::beginTransaction();

            foreach ($validated['scores'] as $item) {
                ActivityDailyScore::updateOrCreate(
                    [
                        'candidate_id' => $item['candidate_id'],
                        'activity_id'  => $item['activity_id'],
                        'date'         => $today,
                    ],
                    [
                        'score'       => $item['score']
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
