<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use App\Http\Resources\MedicationResource;
use App\Http\Requests\StoreMedicationRequest;
use App\Http\Requests\UpdateMedicationRequest;

class MedicationController extends Controller
{
    public function index()
    {
        return MedicationResource::collection(Medication::all());
    }

    public function store(StoreMedicationRequest $request)
    {
        $medication = Medication::create($request->validated());
        return new MedicationResource($medication);
    }

    public function show(Medication $medication)
    {
        return new MedicationResource($medication);
    }

    public function update(UpdateMedicationRequest $request, Medication $medication)
    {
        $medication = $medication->update($request->validated());
        return new MedicationResource($medication);
    }

    public function destroy(Medication $medication)
    {
        $medication->delete();
        return response()->json(['message' => 'Medication deleted successfully']);
    }
}
