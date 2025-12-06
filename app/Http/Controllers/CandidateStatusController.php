<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateStatus;
use Illuminate\Http\Request;

class CandidateStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(['data'=>CandidateStatus::all()]);
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
    public function show(CandidateStatus $candidateStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CandidateStatus $candidateStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CandidateStatus $candidateStatus)
    {
        //
    }
}
