<?php

namespace App\Http\Controllers;

use App\Http\Resources\PersonalProgramResource;
use App\Http\Resources\PlanResource;
use App\Models\Group;
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
        ->with(['category', 'planType'])
        ->orderBy('created_at', 'desc')
        ->get();

        $data = $data->map(function($plan){
            $plan->date = $plan->created_at->format('d/m/Y');
            $plan->status = intval($plan->status);
            return $plan->toArray();
        });

        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'candidate_id'     => 'required_without:group_id',
            'category_id'      => 'required|exists:plan_categories,id',
            'plan_type_id'     => 'required|exists:plan_types,id',
            'group_id'         => 'nullable|exists:groups,id',
            'name'             => 'required',
            'activities'       => 'required|array',
            'status'           => 'nullable',
            'start_date'       => 'required',
            'end_date'         => 'required',
        ], [], [
            'category_id'      => 'Plan',
            'plan_type_id'     => 'Tipo de plan',
            'group_id'         => 'ID del grupo',
            'name'             => 'Nombre del plan',
            'activities'       => 'Actividades',
            'status'           => 'Estado',
            'start_date'       => 'Fecha de inicio',
            'end_date'         => 'Fecha de cierre',
        ]);

        if( !$request->group_id ){
            $data['group_id'] = Group::where('is_individual', 1)
            ->whereHas('candidates', fn($q)=>$q->whereId($request->candidate_id))
            ->first()
            ->id;
        }

        unset($data['activities']);
        unset($data['candidate_id']);

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
            'plan_type_id'            => 'required|exists:plan_types,id',
            'group_id'                => 'required|exists:groups,id',
            'name'                    => 'required',
            'activities'              => 'required|array',
            'status'                  => 'nullable',
            'start_date'              => 'required',
            'end_date'                => 'required',
        ], [], [
            'category_id'      => 'Plan',
            'plan_type_id'     => 'Tipo de plan',
            'group_id'         => 'ID del grupo',
            'name'             => 'Nombre del plan',
            'activities'       => 'Actividades',
            'status'           => 'Estado',
            'start_date'       => 'Fecha de inicio',
            'end_date'         => 'Fecha de cierre',
        ]);

        unset($data['activities']);
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
