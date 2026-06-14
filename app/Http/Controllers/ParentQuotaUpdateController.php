<?php

namespace App\Http\Controllers;

use App\Models\ParentQuotaUpdate;
use Illuminate\Http\Request;

class ParentQuotaUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = ParentQuotaUpdate::all();
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount'      => 'required|numeric',
            'valid_since' => 'required|date'
        ]);

        $increment = ParentQuotaUpdate::scheduleOrCreate($validated['amount'], $validated['valid_since']);
        return response()->json(['data'=>$increment], 201);
    }

    public function currentPending()
    {
        $pending = ParentQuotaUpdate::where('applied', false)->first();
        if (!$pending) {
            return response()->json(['message' => 'No hay incrementos pendientes.'], 404);
        }
        return response()->json(['data' => $pending]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ParentQuotaUpdate $parentQuotaUpdate)
    {
        //
    }
}
