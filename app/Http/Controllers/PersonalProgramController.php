<?php

namespace App\Http\Controllers;

use App\Http\Resources\PersonalProgramResource;
use App\Http\Resources\PlanActivitiesResource;
use App\Models\PersonalProgram;
use Illuminate\Http\Request;

class PersonalProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if( $request->filled('candidate_id') ){
            $data = PersonalProgram::whereCandidateId($request->candidate_id)->with(['plan', 'plan_type'])->get();
        }else {
            $data = PersonalProgram::with(['plan', 'plan_type'])->get();
        }
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'plan_id'      => 'required|exists:plans,id',
            'plan_type_id' => 'required|exists:plan_types,id',
            'candidate_id' => 'required|exists:candidates,id',
            'name'         => 'required',
            'activities'   => 'required|array',
            'status'       => 'nullable'
        ]);
        
        unset($data['activities']);
        $data = PersonalProgram::create($data);
        $data->activities()->attach($request->activities);
        return response()->json(compact('data'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PersonalProgram $personalProgram)
    {
        $data = $personalProgram->load(['activities', 'plan_type', 'plan']);
        return new PersonalProgramResource($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PersonalProgram $personalProgram)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PersonalProgram $personalProgram)
    {
        //
    }
}
