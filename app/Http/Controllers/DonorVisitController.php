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

    /**
     * Reporte avanzado de visitas con filtros y último donativo Radiomaratón
     */
    public function report(Request $request): JsonResponse
    {
        // Iniciamos la consulta cargando la relación del responsable y del donante
        $query = DonorVisit::with([
            'responsible:id,name',
            'donor:id,first_name,last_name,second_last_name,company_name,sector'
        ]);

        // 💡 Subconsulta: Traer el monto del último donativo tipo 'Radiomaratón'
        $query->addSelect(['last_radiomaraton_amount' => Donation::select('amount')
            ->whereColumn('donor_id', 'visits.donor_id')
            ->where('activity_type', 'Radiomaratón')
            ->orderBy('payment_date', 'desc')
            ->limit(1)
        ]);

        // Filtro 1: Fecha Desde (Visita)
        if ($request->filled('date_from')) {
            $query->whereDate('visit_date', '>=', $request->date_from);
        }

        // Filtro 2: Fecha Hasta (Visita)
        if ($request->filled('date_to')) {
            $query->whereDate('visit_date', '<=', $request->date_to);
        }

        // Filtro 3: Tipo de Actividad (Buscando en la columna JSON/Array prospect_for del Donante)
        if ($request->filled('activity_type')) {
            $activity = $request->activity_type;
            $query->whereHas('donor', function ($q) use ($activity) {
                $q->where('prospect_for', 'like', "%{$activity}%");
            });
        }

        // Ordenamos por la fecha de visita más reciente
        $visits = $query->orderBy('visit_date', 'desc')->get();

        return response()->json([
            'data' => $visits
        ], 200);
    }
}
