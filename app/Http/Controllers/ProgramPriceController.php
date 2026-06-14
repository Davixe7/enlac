<?php

namespace App\Http\Controllers;

use App\Models\ProgramPrice;
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

        if( $request->valid_since == now()->format('Y-m-d') ){
            $current = ProgramPrice::where('program_id', $request->program_id)
            ->current()
            ->first();

            if( $current ){
                $current->update('valid_until', now()->subDay()->format('Y-m-d'));
            }
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

        if( $request->valid_since == now()->format('Y-m-d') ){
            $current = ProgramPrice::whereProgramId($request->program_id)
            ->where('valid_since', '<=', now())
            ->whereNull('valid_until')
            ->first();

            if( $current ){
                $current->update(['valid_until' => now()->subDay()->format('Y-m-d')]);
            }
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
