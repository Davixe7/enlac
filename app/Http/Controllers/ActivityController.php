<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = Activity::with(['plan_category']);
        if( $request->plan_category_id ){
            $data = $data->wherePlanCategoryId($request->plan_category_id);
        }
        $data = $data->get();
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required',
            'plan_category_id' => 'required|exists:plan_categories,id',
            'measurement_unit' => 'required',
            'goal_type'        => 'required'
        ]);

        $data = Activity::create($data);
        $data->load('plan_category');
        return response()->json(compact('data'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        return response()->json($activity, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Activity $activity)
    {
        $data = $request->validate([
            'name'             => 'required',
            'plan_category_id' => 'required|exists:plan_categories,id',
            'measurement_unit' => 'required',
            'goal_type'        => 'required'
        ]);

        $activity->update($data);
        $data = $activity->load('plan_category');
        return response()->json(compact('data'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();
        return response()->json([], 204);
    }
}
