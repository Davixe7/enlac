<?php

namespace App\Http\Controllers;

use App\Http\Resources\EquinetherapyScheduleResource;
use App\Models\Ride;
use App\Models\EquinetherapySchedule;
use Illuminate\Http\Request;

class EquineRidesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = EquinetherapySchedule::where('date', now()
        ->format('Y-m-d'))
        ->with('candidate')
        ->get();

        return EquinetherapyScheduleResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Ride $ride)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ride $ride)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ride $ride)
    {
        //
    }
}
