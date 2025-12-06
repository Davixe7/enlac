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
            'candidate_status_id' => 'required',
            'entry_date'          => 'required_if:candidate_status_id,5',
            'program_id'          => 'required_if:candidate_status_id,5'
        ]);

        if( $request->filled('entry_date') ){
            //Notificar ingreso programado
        }

        $candidate->update($data);

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
