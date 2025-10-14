<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = Group::withCount('candidates')
        ->whereIsIndividual(false)
        ->includesCandidate( $request->candidate_id )
        ->with(['program'])
        ->get();

        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required',
            'program_id' => 'required'
        ]);

        $data = Group::create(array_merge($data, ['is_individual'=>0]));

        if( $request->filled('candidates') ){
            $data->candidates()->sync( $request->candidates );
        }
        return response()->json(compact('data'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $data = $group->load(['candidates', 'plans.subcategory.parent']);
        return response()->json(compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $data = $request->validate(['name'=>'required']);
        $group->update($data);

        if( $request->filled('candidates') ){
            $group->candidates()->sync( $request->candidates );
        }

        return response()->json(['data'=>$group]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $group->delete();
        return response()->json([], 204);
    }
}
