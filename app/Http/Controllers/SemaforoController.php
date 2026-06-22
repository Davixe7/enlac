<?php

namespace App\Http\Controllers;

use App\Services\SemaforoService;
use Illuminate\Http\Request;

class SemaforoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $schoolYear = now()->month >= 8 ? now()->year : now()->year - 1;
        $semaforo = new SemaforoService();
        $data = $semaforo->generateMatrix($request->candidate_id, $schoolYear);
        return response()->json(['data'=>$data]);
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
