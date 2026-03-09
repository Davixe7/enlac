<?php

namespace App\Http\Controllers;

use App\Http\Resources\RideResource;
use App\Models\Candidate;
use App\Models\Ride;
use Illuminate\Http\Request;

class RideController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ?: now()->format('Y-m-d');

        if( $request->type == 'rubio' ){
            $candidates = Candidate::whereRequiresTransport(1)
            ->beneficiaries()
            ->basic(['requires_transport'])
            ->with(['todaysRide', 'locationDetail', 'legalGuardian'])
            ->get();

            $rides = $candidates->map(function($candidate){
                $ride = $candidate->todaysRide;
                unset($candidate->todaysRide);
                $ride->candidate = $candidate;
                return $ride;
            });

            return RideResource::collection($rides);
        }

        $data = Ride::whereType($request->type)
        ->where('date', $date)
        ->with(['candidate.locationDetail', 'candidate.legalGuardian'])
        ->get();

        return RideResource::collection($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'           => 'required',
            'candidate_id'   => 'required',
            'start_time'     => 'nullable',
            'end_time'       => 'nullable',
            'departure_time' => 'nullable',
            'return_time'    => 'nullable',
            'comments'       => 'nullable|string',
            'type'           => 'in:equine,rubio',
        ]);

        $ride = Ride::create($validated);

        $ride->load(['candidate'=>fn($q)=>$q->basic()]);

        return new RideResource($ride);
    }

    public function show(Ride $ride)
    {
        return response()->json($ride);
    }

    public function update(Request $request, Ride $ride)
    {
        $validated = $request->validate([
            'start_time' => 'nullable',
            'end_time'   => 'nullable',
            'departure_time' => 'nullable',
            'return_time'   => 'nullable',
            'comments'   => 'nullable|string',
            'type'       => 'in:equine,rubio',
        ]);

        $ride->update($validated);

        return new RideResource($ride);
    }

    public function destroy(Ride $ride)
    {
        $ride->delete();
        return response()->json([], 204);
    }
}
