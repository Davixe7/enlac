<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = Issue::filterByDate($request->date)->with('user')->get();
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_area_id' => 'required|exists:work_areas,id',
            'user_id'      => 'required|exists:users,id',
            'type'         => 'required|string',
            'comments'     => 'required|string',
            'media'        => 'nullable|array',
            'date'         => 'required|date'
        ]);

        $data = $validated;
        unset($data['media']);

        $issue = Issue::create($data);

        if( !array_key_exists('media', $validated) ){
            return response()->json(['data'=>$issue]);
        }

        foreach($validated['media'] as $attachment){
            $issue->addMedia( $attachment )->toMediaCollection('attachments');
        }
        return response()->json(['data'=>$issue]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Issue $issues)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Issue $issues)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Issue $issues)
    {
        //
    }
}
