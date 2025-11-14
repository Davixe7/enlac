<?php

namespace App\Http\Controllers;

use App\Http\Resources\CandidateResource;
use App\Http\Resources\EquinotherapyScheduleResource;
use App\Models\Candidate;
use App\Models\EquinotherapySchedule;
use Illuminate\Http\Request;

class EquinotherapyScheduleController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $schedules = EquinotherapySchedule::with('candidate')
            ->whereDate('date', $date)
            ->orderBy('start_time')
            ->get();

        $comodines = Candidate::whereHas('equinotherapy_schedules', function ($q) use ($date) {
            $q->whereDate('date', $date)->where('is_comodin', true);
        })->get();

        return response()->json([
            'schedules' => EquinotherapyScheduleResource::collection($schedules),
            'comodines' => CandidateResource::collection($comodines)
        ]);

    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'candidate_id' => 'nullable|exists:candidates,id',
            'is_comodin' => 'boolean'
        ]);

        $schedule = EquinotherapySchedule::create($data);

        return new EquinotherapyScheduleResource($schedule->load('candidate'));
    }

    public function availableBeneficiaries(Request $request)
    {
        $date = $request->input('date');

        $beneficiaries = Candidate::where('requires_equinotherapy', true)
        ->whereDoesntHave('equinotherapy_schedules', function ($q) use ($date) {
            $q->whereDate('date', $date);
        })
        ->get();

        return CandidateResource::collection($beneficiaries);
    }



}
