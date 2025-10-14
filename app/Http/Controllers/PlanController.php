<?php

namespace App\Http\Controllers;

use App\Http\Resources\PersonalProgramResource;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = Plan::whereGroupId($request->group_id)
        ->with(['category', 'subcategory'])
        ->orderBy('created_at', 'desc')
        ->get();
        
        return PersonalProgramResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'      => 'required|exists:plan_categories,id',
            'subcategory_id'   => 'required|exists:plan_categories,id',
            'group_id'         => 'required|exists:groups,id',
            'name'             => 'required',
            'activities'       => 'required|array',
            'status'           => 'nullable',
            'start_date'       => 'required|date_format:d/m/Y',
            'end_date'         => 'required|date_format:d/m/Y',
        ], [], [
            'category_id'      => 'Plan',
            'subcategory_id'   => 'Tipo de plan',
            'group_id'         => 'ID del grupo',
            'name'             => 'Nombre del plan',
            'activities'       => 'Actividades',
            'status'           => 'Estado',
            'start_date'       => 'Fecha de inicio',
            'end_date'         => 'Fecha de cierre',
        ]);
        
        unset($data['activities']);
        $data['start_date'] = Carbon::createFromFormat('d/m/Y', $data['start_date']);
        $data['end_date'] = Carbon::createFromFormat('d/m/Y', $data['end_date']);
        $data = Plan::create($data);

        $data->activities()->attach($request->activities);
        $data->load('activities');
        return response()->json(compact('data'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Plan $plan)
    {
        $data = $plan->load(['activities', 'category']);
        return new PersonalProgramResource($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'category_id'             => 'required|exists:plan_categories,id',
            'subcategory_id'          => 'required|exists:plan_categories,id',
            'group_id'                => 'required|exists:groups,id',
            'name'                    => 'required',
            'activities'              => 'required|array',
            'status'                  => 'nullable',
            'start_date'              => 'required|date_format:d/m/Y',
            'end_date'                => 'required|date_format:d/m/Y',
        ], [], [
            'category_id'      => 'Plan',
            'subcategory_id'   => 'Tipo de plan',
            'group_id'         => 'ID del grupo',
            'name'             => 'Nombre del plan',
            'activities'       => 'Actividades',
            'status'           => 'Estado',
            'start_date'       => 'Fecha de inicio',
            'end_date'         => 'Fecha de cierre',
        ]);
        
        unset($data['activities']);
        $data['start_date'] = Carbon::createFromFormat('d/m/Y', $data['start_date']);
        $data['end_date'] = Carbon::createFromFormat('d/m/Y', $data['end_date']);
        $plan->update($data);

        $plan->activities()->sync($request->activities);
        $plan->load('activities');
        return response()->json(['data'=>$plan], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $personalProgram)
    {
        //
    }
}
