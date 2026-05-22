<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Http\Requests\StoreDonorRequest;
use Illuminate\Http\JsonResponse;

class DonorController extends Controller
{
    public function index(): JsonResponse
    {
        // Cargamos la relación 'fiscalRecords' para que viaje en el JSON al frontend
        $donors = Donor::with('fiscalRecords')
            ->orderBy('first_name')
            ->get()
            ->append('full_name');

        return response()->json($donors);
    }

    public function store(StoreDonorRequest $request)
    {
        $validatedData = $request->validated();
        $fiscalRecords = $validatedData['fiscal_records'] ?? [];
        unset($validatedData['fiscal_records']); // Evita el error de columna inexistente en 'donors'

        // Guardar Donante Principal
        $donor = Donor::create($validatedData);

        // Guardar de golpe los registros fiscales aprovechando que las llaves hacen match perfecto
        if (count($fiscalRecords) > 0) {
            $donor->fiscalRecords()->createMany($fiscalRecords);
        }

        return response()->json($donor->load('fiscalRecords'), 201);
    }

    public function update(StoreDonorRequest $request, Donor $donor)
    {
        $validatedData = $request->validated();
        $fiscalRecords = collect($validatedData['fiscal_records'] ?? []);
        unset($validatedData['fiscal_records']);

        // Actualizar Donante Principal
        $donor->update($validatedData);

        // 1. Eliminación quirúrgica de los registros borrados en el front
        $donor->fiscalRecords()->whereNotIn('id', $fiscalRecords->pluck('id')->filter())->delete();

        // 2. Sincronización limpia directa (¡Sin mapeos manuales repetitivos!)
        $fiscalRecords->each(function ($record) use ($donor) {
            $donor->fiscalRecords()->updateOrCreate(
                ['id' => $record['id'] ?? null],
                $record
            );
        });

        return response()->json($donor->load('fiscalRecords'), 200);
    }

}
