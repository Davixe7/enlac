<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $groups = Group::withCount('candidates')
        ->whereIsIndividual(false)
        ->includesCandidate( $request->candidate_id )
        ->with(['program', 'leader', 'assistant'])
        ->get();

        return GroupResource::collection($groups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required',
            'program_id' => 'required',
            'group_leader_id'  => 'nullable|exists:users,id',
            'assistant_id'     => 'nullable|exists:users,id'
        ]);

        $data = Group::create(array_merge($data, ['is_individual'=>0]));

        if( $request->filled('candidates') ){
            $data->candidates()->sync( $request->candidates );
        }
        return new GroupResource($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $group->load(['program', 'leader', 'assistant', 'candidates', 'plans.subcategory.parent']);
        return new GroupResource($group);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $data = $request->validate([
            'name'=>'required',
            'group_leader_id'  => 'nullable|exists:users,id',
            'assistant_id'     => 'nullable|exists:users,id'
        ]);

        $group->update($data);

        if( $request->filled('candidates') ){
            $group->candidates()->sync( $request->candidates );
        }

        return new GroupResource($group);
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
