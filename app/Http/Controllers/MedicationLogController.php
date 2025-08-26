<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Medication;
use App\Models\MedicationLog;
use Illuminate\Http\Request;

class MedicationLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Candidate $candidate)
    {
        $candidate->load(['medication_logs.medication']);
        $data = $candidate->medication_logs->sortByDesc('created_at')->values();
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Medication $medication, Request $request)
    {
        $medication->update(['status'=>$request->status]);
        $data = $medication->statusLogs()->create([
            'dose'   => $medication->dose,
            'status' => $medication->status,
        ]);

        return response()->json(compact('data'), 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicationLog $medicationLog)
    {
        $medicationLog->delete();
        return response()->json([], 204);
    }
}
