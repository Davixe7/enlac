<?php

namespace App\Http\Controllers;

use App\Models\FamilyMember;
use Illuminate\Http\Request;

class FamilyMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->candidate_id) {
            $data = FamilyMember::whereCandidateId($request->candidate_id)->get();
            return response()->json(compact('data'));
        }

        $data = FamilyMember::all();
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'candidate_id'         => 'required|exists:candidates,id',
            'age'                  => 'nullable|integer|min:0|max:120',
            'relationship'         => 'required|string|max:100',
            'marital_status'       => 'nullable|string|max:50',
            'scolarship'           => 'nullable|string|max:100',
            'ocupation'            => 'nullable|string|max:150',
            'monthly_income'       => 'required|numeric|min:0',
            'monthly_contribution' => 'required|numeric|min:0|max:' . $request->monthly_income,
        ], [
            'name.required' => 'El nombre completo es obligatorio.',
            'monthly_contribution.max' => 'La aportación no puede ser mayor al ingreso mensual.',
        ]);

        // Si la validación pasa, creas el registro
        $data = FamilyMember::create($validated);

        return response()->json(compact('data'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(FamilyMember $familyMember)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FamilyMember $familyMember)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FamilyMember $familyMember)
    {
        //
    }
}
