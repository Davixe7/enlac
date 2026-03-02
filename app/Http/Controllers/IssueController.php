<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use App\Exports\IssuesExport;
use Maatwebsite\Excel\Facades\Excel;

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

    public function export(Request $request)
    {
        $query = Issue::query();

        if ($request->candidate_id) {
            $query->filterByCandidate($request->candidate_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->filterByDates($request->start_date, $request->end_date);
        }

        if ($request->date && !$request->start_date) {
            $query->filterByDate($request->date);
        }

        $fileName = 'reporte-incidencias-' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new IssuesExport($query), $fileName);
    }
}


