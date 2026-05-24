<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Http\Requests\StoreDonorRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $donors = Donor::with('fiscalRecords')
            // Filtro por Nombre / Razón Social / Empresa
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('second_last_name', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
                });
            })
            // Filtro por Tipo de Actividad (Prospecto para)
            ->when($request->filled('activity_type'), function ($query) use ($request) {
                $activity = $request->input('activity_type');
                // Asumiendo que guardas 'prospect_for' como JSON cast en el modelo
                $query->whereJsonContains('prospect_for', $activity);
            })
            // Filtro por Mes de Cumpleaños (birth_date formato Y-m-d)
            ->when($request->filled('birth_month'), function ($query) use ($request) {
                $month = $request->input('birth_month');
                $query->whereMonth('birth_date', $month);
            })
            ->orderBy('first_name')
            ->get()
            ->append('full_name');

        return response()->json($donors);
    }

    public function store(StoreDonorRequest $request)
    {
        $validatedData = $request->validated();
        $fiscalRecords = $validatedData['fiscal_records'] ?? [];

        unset($validatedData['fiscal_records']);
        unset($validatedData['donor_type']);

        $donor = Donor::create($validatedData);

        if (count($fiscalRecords) > 0) {
            $donor->fiscalRecords()->createMany($fiscalRecords);
        }

        return response()->json($donor->load('fiscalRecords')->append('full_name'), 201);
    }

    public function update(StoreDonorRequest $request, Donor $donor)
    {
        $validatedData = $request->validated();
        $fiscalRecords = collect($validatedData['fiscal_records'] ?? []);

        unset($validatedData['fiscal_records']);
        unset($validatedData['donor_type']);

        $donor->update($validatedData);

        // Sincronización quirúrgica limpia
        $donor->fiscalRecords()->whereNotIn('id', $fiscalRecords->pluck('id')->filter())->delete();

        $fiscalRecords->each(function ($record) use ($donor) {
            $donor->fiscalRecords()->updateOrCreate(
                ['id' => $record['id'] ?? null],
                $record
            );
        });

        return response()->json($donor->load('fiscalRecords')->append('full_name'), 200);
    }
}
