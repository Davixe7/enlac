<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\PlanCategory;
use Illuminate\Http\Request;

class PlanCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = PlanCategory::baseOnly($request->base_only)->get();
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'plan_type_id' => 'required|exists:plan_types',
            'name'         => 'required'
        ]);
        $plan_category = PlanCategory::create($data);
        return response()->json(compact('data'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PlanCategory $plan_category)
    {
        return response()->json($plan_category, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PlanCategory $plan_category)
    {
        $data = $request->validate(['name' => 'required']);
        $plan_category->update($data);
        return response()->json(compact('data'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlanCategory $plan_category)
    {
        $plan_category->delete();
        return response()->json([], 204);
    }
}
