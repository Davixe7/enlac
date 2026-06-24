<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramPrice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProgramPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if( $request->pending ){
            $data = ProgramPrice::whereProgramId($request->program_id)
            ->where('valid_since', '>', now())
            ->first();
            return response()->json(compact('data'));
        }

        $data = ProgramPrice::whereProgramId($request->program_id)->get();
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id'  => 'required',
            'price'       => 'required|numeric',
            'valid_since' => 'required|date'
        ]);

        $validSince = Carbon::parse($request->input('valid_since'));
        $validUntil = $validSince->copy()->subDay();

        if( $validSince->isToday() ){
            ProgramPrice::whereProgramId($request->program_id)
            ->current()
            ->update(['valid_until' => $validUntil]);

            Program::where('id', $request->program_id)->update(['price'=>$request->price]);
        }

        $programPrice = ProgramPrice::create($validated);

        return response()->json(['data'=>$programPrice], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProgramPrice $programPrice)
    {
        $validated = $request->validate([
            'price'       => 'sometimes|numeric',
            'valid_since' => 'sometimes|date'
        ]);

        $validSince = Carbon::parse($request->input('valid_since'));
        $validUntil = $validSince->copy()->subDay();

        if( $validSince->isToday() ){
            ProgramPrice::whereProgramId($programPrice->program_id)
            ->current()
            ->update(['valid_until' => $validUntil]);

            Program::where('id', $request->program_id)->update(['price'=>$request->price]);
        }

        $programPrice->update($validated);

        return response()->json(['data'=>$programPrice], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgramPrice $programPrice)
    {
        //
    }
}
