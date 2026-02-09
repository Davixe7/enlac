<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoreReportController extends Controller
{
    /**
    * Display a listing of the resource.
    */
    public function index(Request $request)
    {
        
        
        $candidateId = 55;
        
        $results = DB::table('plans as p')
        // Conectamos el plan con sus actividades a través de la tabla pivote
        ->join('activity_plan as ap', 'p.id', '=', 'ap.plan_id')
        ->join('activities as a', 'ap.activity_id', '=', 'a.id')
        
        // Conectamos con los scores filtrando por el candidato y la fecha
        ->join('activity_daily_scores as s', function($join) use ($candidateId) {
            $join->on('a.id', '=', 's.activity_id')
            ->where('s.candidate_id', '=', $candidateId)
            ->whereDate('s.date', '>=', now()->startOfMonth())
            ->whereDate('s.date', '<=', now()->endOfMonth());
        })
        
        // Opcional: Asegurar que el candidato pertenece al grupo del plan
        ->join('candidate_group as cg', 'p.group_id', '=', 'cg.group_id')
        ->where('cg.candidate_id', '=', $candidateId)
        
        // Selección de campos
        ->select(
            'p.id as plan_id',
            'p.name as plan_name',
            's.candidate_id',
            'a.id as activity_id',
            'a.name as activity_name',
            's.score',
            's.date'
            )
            ->get();
            
            $groupedResults = $results->groupBy('plan_id')->map(function ($items) {
                return [
                    'plan_id'   => $items->first()->plan_id,
                    'plan_name' => $items->first()->plan_name,
                    'scores'    => $items->values() // Reindexa la colección de scores
                ];
            })->values();
            
            return response()->json(['data'=>$groupedResults]);
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
        public function show(string $id)
        {
            //
        }
        
        /**
        * Update the specified resource in storage.
        */
        public function update(Request $request, string $id)
        {
            //
        }
        
        /**
        * Remove the specified resource from storage.
        */
        public function destroy(string $id)
        {
            //
        }
    }
