<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Http\Requests\StoreDonationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    /**
     * Registrar un nuevo donativo aplicando lógica de folios atómicos
     */
    public function store(StoreDonationRequest $request): JsonResponse
    {
        // Usamos una transacción para garantizar que el folio no se duplique si entran dos peticiones en simultáneo
        $donation = DB::transaction(function () use ($request) {

            // 1. Lógica de Generación de Folio Automático (P-26-00001)
            $yearIndicator = '26'; // Año en curso 2026 fijo por requerimiento
            $prefix = "P-{$yearIndicator}-";

            // Buscamos el último folio generado para este año con bloqueo de escritura
            $lastDonation = Donation::where('folio_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            if ($lastDonation) {
                // Extraemos el número correlativo final (ej: de P-26-00005 toma 5)
                $lastNumber = (int) substr($lastDonation->folio_number, -5);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            // Rellenamos con ceros a la izquierda hasta completar los 5 dígitos
            $generatedFolio = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // 2. Unimos el folio generado a los datos limpios validados
            $data = $request->validated();
            $data['folio_number'] = $generatedFolio;

            // 3. Crear el registro en la base de datos
            return Donation::create($data);
        });

        return response()->json([
            'message' => 'Donativo aplicado con éxito',
            'data' => $donation
        ], 201);
    }
}
