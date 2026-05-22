<?php

namespace App\Http\Controllers;

use App\Models\ProcurationActivity;
use App\Http\Requests\StoreProcurationActivityRequest;
use App\Http\Requests\UpdateProcurationActivityRequest;
use Illuminate\Http\JsonResponse;

class ProcurationActivityController extends Controller
{
    /**
     * Listar las actividades ordenadas alfabéticamente
     */
    public function index(): JsonResponse
    {
        $activities = ProcurationActivity::orderBy('name')->get();
        return response()->json($activities);
    }

    /**
     * Crear una nueva actividad usando Form Request
     */
    public function store(StoreProcurationActivityRequest $request): JsonResponse
    {
        $activity = ProcurationActivity::create($request->validated());

        return response()->json($activity, 201);
    }

    /**
     * Actualizar una actividad (Básico y Avanzado) usando Form Request
     */
    public function update(UpdateProcurationActivityRequest $request, $id): JsonResponse
    {
        $activity = ProcurationActivity::findOrFail($id);

        $activity->update($request->validated());

        return response()->json($activity, 200);
    }
}
