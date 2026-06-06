<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Http\Requests\StoreDonorRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Sponsor;
use Illuminate\Support\Facades\DB;

class DonorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Este método solo buscará en la tabla DONORS
        $donors = Donor::with('fiscalRecords')
            // Filtro por término de búsqueda (Nombre, apellido, empresa)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
                });
            })
            // SOLUCIÓN: Filtro por Tipo de Actividad (Buscando dentro del array JSON)
            ->when($request->filled('activity_type'), function ($query) use ($request) {
                $activityType = $request->input('activity_type');
                // Usamos whereJsonContains porque 'prospect_for' se comporta como un array en el JSON
                $query->whereJsonContains('prospect_for', $activityType);
            })
            // EXTRA: Filtro por Mes de Cumpleaños
            ->when($request->filled('birth_month'), function ($query) use ($request) {
                $month = $request->input('birth_month'); // Ej: "01", "02"
                $query->whereMonth('birth_date', $month);
            })
            ->orderBy('first_name')
            ->get()
            ->append('full_name');

        return response()->json($donors);
    }

    public function searchDonorsAndSponsors(Request $request): JsonResponse
    {
        $search = $request->query('search', '');

        // Buscar Donantes
        $donors = Donor::where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%");
            })
            ->with('fiscalRecords')
            ->get()
            ->map(function ($donor) {
                return [
                    'id' => $donor->id,
                    'full_name' => trim($donor->first_name . ' ' . $donor->last_name . ' ' . ($donor->company_name ?? '')),
                    'origin' => 'donante',
                    'fiscal_records' => $donor->fiscalRecords
                ];
            });

        // Buscar Sponsors
        $sponsors = Sponsor::where('name', 'like', "%{$search}%")
            ->where('type', 'general')
            ->get()
            ->map(function ($sponsor) {
                return [
                    'id' => $sponsor->id,
                    'full_name' => trim($sponsor->name . ' ' . $sponsor->last_name),
                    'origin' => 'sponsor',
                    'company_name' => $sponsor->company_name,
                    'fiscal_records' => []
                ];
            });

        $results = $donors->concat($sponsors)->sortBy('full_name')->values();

        return response()->json($results);
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

    public function show(Donor $donor): JsonResponse
    {
        $donor->load([
            'fiscalRecords',
            'visits.responsible:id,name,last_name,second_last_name',
            'gratitudes',
            'shipments',
            'donations',
            'statusLogs'
        ]);

        // Aseguramos que el atributo full_name se calcule y se envíe
        $donor->append('full_name');

        return response()->json($donor);
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

    public function toggleStatus(Request $request, $id): JsonResponse
    {
        $donor = Donor::find($id);

        if (!$donor) {
            return response()->json(['message' => 'Donante no encontrado'], 404);
        }

        $request->validate(['is_active' => 'required|boolean']);

        $donor->is_active = $request->is_active;
        $donor->save();

        return response()->json(['message' => 'Estatus actualizado']);
    }
}
