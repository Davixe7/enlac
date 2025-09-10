<?php

namespace App\Http\Controllers;

use App\Http\Resources\PersonalProgramResource;
use App\Http\Resources\PlanActivitiesResource;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = Plan::whereGroupId($request->group_id)->with(['category.subcategory'])->get();
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'plan_category_id' => 'required|exists:plan_categories,id',
            'candidate_id'     => 'nullable|exists:candidates,id',
            'name'             => 'required',
            'activities'       => 'required|array',
            'status'           => 'nullable',
            'group_id'         => 'required'
        ]);
        
        unset($data['activities']);
        $data = Plan::create($data);
        $data->activities()->attach($request->activities);
        $data->load('activities');
        return response()->json(compact('data'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Plan $plan)
    {
        $data = $plan->load(['activities', 'plan_category']);
        return new PersonalProgramResource($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plan $personalProgram)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $personalProgram)
    {
        //
    }
}
