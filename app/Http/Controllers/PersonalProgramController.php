<?php

namespace App\Http\Controllers;

use App\Http\Resources\PersonalProgramResource;
use App\Models\Candidate;
use App\Models\Group;
use App\Models\Plan;
use Illuminate\Http\Request;

class PersonalProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $candidate = Candidate::findOrFail($request->candidate_id);
        $personalGroup = $candidate->groups()->whereIsIndividual(1)->first();
        if(!$personalGroup){
            $personalGroup = Group::create(['is_individual'=>1, 'name'=>$candidate->id]);
            $personalGroup->candidates()->attach($request->candidate_id);
        }
        return PersonalProgramResource::collection($personalGroup->plans->orderBy('created_at'));
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
    public function show(Plan $plan)
    {
        return new PersonalProgramResource($plan->load(['subcategory.parent', 'group']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
