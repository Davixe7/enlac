<?php

namespace App\Http\Controllers;

use App\Models\WorkArea;
use Illuminate\Http\Request;

class WorkAreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = WorkArea::orderBy('name')->get();
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate(['name'=>'required']);
        $workArea = WorkArea::create( $data );
        return response()->json(['data' => $workArea], 201 );
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkArea $workArea)
    {
        response()->json(['data' => $workArea]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkArea $workArea)
    {
        $data = $request->validate(['name'=>'required']);
        $workArea->update( $data );
        return response()->json(['data' => $workArea]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkArea $workArea)
    {
        $workArea->delete();
        response()->json([], 200);
    }
}
