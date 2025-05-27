<?php

namespace App\Http\Controllers;

use App\Http\Resources\Kardex as ResourcesKardex;
use App\Http\Resources\KardexResource;
use App\Models\Candidate;
use App\Models\Kardex;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kardexes = Kardex::with(['media'])->get();
        $data = $kardexes->groupBy('category')->map(function($kardex){
            return KardexResource::collection($kardex);
        });
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $candidate = Candidate::findOrFail( $request->candidate_id );

        $request->validate([
            'kardexes' => 'required|array',
            'kardexes.*' => 'required|file',
        ]);

        foreach( $request->kardexes as $key => $file ){
            $candidate->addMedia( $file )->toMediaCollection('kardex_' . $key);
        }

        return response()->json(['data'=>'success'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Kardex $kardex)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kardex $kardex)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kardex $kardex)
    {
        //
    }
}
