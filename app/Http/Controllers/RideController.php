<?php

namespace App\Http\Controllers;

use App\Http\Resources\RideResource;
use App\Models\Candidate;
use App\Models\Ride;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\RidesExport;
use Maatwebsite\Excel\Facades\Excel;

class RideController extends Controller
{
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

    public function report(Request $request){
        $start = $request->start_date;
        $end   = $request->end_date;

        $data = Ride::whereBetween('date', [$start, $end])
        ->where('type', $request->type)
        ->with('candidate.locationDetail')
        ->get();

        return response()->json(compact('data'));
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

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
            'type'       => 'nullable|in:equine,rubio'
        ]);

        $query = Ride::whereBetween('date', [$request->start_date, $request->end_date]);

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $fileName = 'reporte-traslados-' . $request->type . '-' . now()->format('d-m-Y') . '.xlsx';

        return Excel::download(new RidesExport($query), $fileName);
    }
}
