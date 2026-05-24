<?php

namespace App\Http\Controllers;

use App\Models\DonorShipment;
use App\Http\Requests\StoreDonorShipmentRequest;
use Illuminate\Http\JsonResponse;

class DonorShipmentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDonorShipmentRequest $request): JsonResponse
    {
        // Usamos el método validated() del FormRequest para mayor seguridad
        $shipment = DonorShipment::create($request->validated());

        return response()->json([
            'message' => 'Envío registrado correctamente',
            'data' => $shipment
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreDonorShipmentRequest $request, DonorShipment $donorShipment): JsonResponse
    {
        $donorShipment->update($request->validated());

        return response()->json([
            'message' => 'Envío actualizado correctamente',
            'data' => $donorShipment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DonorShipment $donorShipment): JsonResponse
    {
        $donorShipment->delete();

        return response()->json([
            'message' => 'Envío eliminado correctamente'
        ]);
    }
}
