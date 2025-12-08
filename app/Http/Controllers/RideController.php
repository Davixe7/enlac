<?php

namespace App\Http\Controllers;

use App\Http\Resources\RideResource;
use App\Models\Candidate;
use App\Models\Ride;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RideController extends Controller
{
    /**
     * Lista de beneficiarios que requieren transporte
     */
    public function index(Request $request)
    {
        $date = $request->date ?: now()->format('Y-m-d');

        if( $request->type == 'rubio' ){
            $candidates = Candidate::whereRequiresTransport(1)
            ->beneficiaries()
            ->basic(['requires_transport'])
            ->with(['todaysRide', 'locationDetail'])
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
        ->with(['candidate.locationDetail'])
        ->get();

        return RideResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(Ride $ride)
    {
        return response()->json($ride);
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ride $ride)
    {
        $ride->delete();
        return response()->json([], 204);
    }
}
