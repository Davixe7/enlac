<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDonorVisitRequest;
use App\Models\Donation;
use App\Models\DonorVisit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonorVisitController extends Controller
{
    // Guardar nueva visita
    public function store(StoreDonorVisitRequest $request): JsonResponse
    {
        $donor_visit = DonorVisit::create($request->validated());

        // Cargamos la relación 'responsible' (que apunta al modelo Contact)
        $donor_visit->load('responsible:id,name');

        return response()->json([
            'message' => 'Visita registrada con éxito',
            'data' => $donor_visit
        ], 201);
    }

    // Actualizar visita existente desde el modal de edición
    public function update(StoreDonorVisitRequest $request, DonorVisit $donor_visit): JsonResponse
    {
        $donor_visit->update($request->validated());
        $donor_visit->unsetRelation('responsible');
        $donor_visit->load('responsible:id,name,last_name,second_last_name');

        return response()->json([
            'message' => 'Visita actualizada con éxito',
            'data' => $donor_visit
        ]);
    }

    // Eliminar registro de la bitácora
    public function destroy(DonorVisit $donor_visit): JsonResponse
    {
        $donor_visit->delete();
        return response()->json(['message' => 'Visita eliminada correctamente']);
    }
}
