<?php

namespace App\Http\Controllers;

use App\Models\ActivityCategory;
use Illuminate\Http\Request;

class ActivityCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if( $request->plan_category_id ){
            $data = ActivityCategory::whereParentId($request->plan_category_id)->get();
            return response()->json(compact('data'));
        }

        $data = ActivityCategory::all();
        return response()->json(compact('data'));
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
    public function show(ActivityCategory $activityCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ActivityCategory $activityCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActivityCategory $activityCategory)
    {
        //
    }
}
