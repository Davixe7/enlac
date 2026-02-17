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
        if( $request->candidate_id ){
            $data = Issue::filterByDate($request->date)
            ->filterByCandidate($request->candidate_id)
            ->filterByDates($request->start_date, $request->end_date)
            ->with(['user', 'plan_category', 'candidate'])->get();

            return response()->json(compact('data'));
        }

        $data = Issue::filterByDate($request->date)->with(['user', 'plan_category', 'candidate'])->get();
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidate_id'     => 'required|exists:candidates,id',
            'plan_category_id' => 'required|exists:plan_categories,id',
            'user_id'          => 'required|exists:users,id',
            'type'             => 'required|string',
            'comments'         => 'required|string',
            'media'            => 'nullable|array',
            'date'             => 'required|date'
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
