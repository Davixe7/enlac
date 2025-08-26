<?php

namespace App\Http\Controllers;

use App\Models\PlanType;
use Illuminate\Http\Request;

class PlanTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = PlanType::all();
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
    public function show(PlanType $planType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PlanType $planType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlanType $planType)
    {
        //
    }
}
