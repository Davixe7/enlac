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
        /* $candidate = Candidate::findOrFail($request->candidate_id);
        $personalGroup = $candidate->groups()->whereIsIndividual(1)->first();
        if(!$personalGroup){
            $personalGroup = Group::create(['is_individual'=>1, 'name'=>$candidate->id]);
            $personalGroup->candidates()->attach($request->candidate_id);
        }
        return PersonalProgramResource::collection($personalGroup->plans->orderBy('created_at')); */

        $groups = Group::whereIsIndividual(1);
        if( $request->except ){
            $groups->whereDoesntHave('candidates', function($query) use ($request){
                $query->whereNotIn('id', $request->except);
            });
        }
        $programs = Plan::with(['activities', 'group.candidates'])
        ->whereIn('group_id', $groups->pluck('id')->toArray())
        ->get();
        return response()->json(['data'=>$programs]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function copy(Request $request, Plan $plan)
    {
        $newPlan = Plan::create([
            'group_id'       => $request->group_id,
            'name'           => $request->name,
            'category_id'    => $plan->category_id,
            'subcategory_id' => $plan->subcategory_id,
            'start_date'     => $plan->start_date,
            'end_date'       => $plan->end_date,
            'status'         => $plan->status,
        ]);

        $sourceActivities = $plan->activities;
        $newAssociations = $sourceActivities->mapWithKeys(function ($activity) {
            $pivotData = $activity->pivot->toArray();
            unset($pivotData['plan_id']);
            unset($pivotData['activity_id']);
            return [$activity->id => $pivotData];
        })->all();

        $newPlan->activities()->attach($newAssociations);

        return response()->json(['data'=>$newPlan->load(['category', 'subcategory'])]);
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
