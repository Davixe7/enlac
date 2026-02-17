<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;

class CandidateStatusUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(Candidate $candidate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Candidate $candidate)
    {
        $data = $request->validate([
            'status'              => 'required',
            'entry_date'          => 'required_if:status,programado',
            'program_id'          => 'required_if:status,programado'
        ]);

        if( $request->filled('entry_date') ){
            //Notificar ingreso programado
        }

        $candidate->updateStatus($request->status);
        $candidate->update($request->only(['entry_date', 'program_id']));

        return response()->json([], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidate $candidate)
    {
        //
    }
}
