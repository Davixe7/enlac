<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCapacitationRequest;
use App\Http\Requests\UpdateCapacitationRequest;
use App\Http\Resources\CapacitationResource;
use App\Models\Capacitation;
use Illuminate\Support\Facades\DB;

class CapacitationController extends Controller
{
    /**
     * Mostrar el listado de capacitaciones (para la tabla principal).
     */
    public function index()
    {
        // Consultamos las capacitaciones incluyendo los contadores y las relaciones completas
        $capacitations = Capacitation::withCount([
            'internalGuests as internal_count',
            'externalGuests as external_count'
        ])
        ->with(['internalGuests', 'externalGuests'])
        ->orderBy('date', 'desc')
        ->get();

        // Mapeamos la colección para normalizar los nombres de las llaves que espera Vue
        $formattedCapacitations = $capacitations->map(function ($capacitation) {
            $array = $capacitation->toArray();
            $array['internal_guests'] = $array['internal_guests'] ?? $array['internal_fields'] ?? [];
            $array['external_guests'] = $array['external_guests'] ?? $array['external_fields'] ?? [];
            return $array;
        });

        return response()->json([
            'data' => $formattedCapacitations
        ], 200);
    }

    /**
     * Mostrar una capacitación específica.
     */
    public function show($id)
    {
        // Buscamos el registro cargando sus contadores y datos relacionales de inmediato
        $capacitation = Capacitation::withCount([
            'internalGuests as internal_count',
            'externalGuests as external_count'
        ])
        ->with(['internalGuests', 'externalGuests'])
        ->findOrFail($id);

        // Normalizamos las llaves de la respuesta para mantener la consistencia con el front
        $responseData = $capacitation->toArray();
        $responseData['internal_guests'] = $responseData['internal_guests'] ?? $responseData['internal_fields'] ?? [];
        $responseData['external_guests'] = $responseData['external_guests'] ?? $responseData['external_fields'] ?? [];

        return response()->json([
            'data' => $responseData
        ], 200);
    }

    /**
     * Almacenar una nueva capacitación utilizando Form Request.
     */
    public function store(StoreCapacitationRequest $request)
    {
        $validated = $request->validated();

        $capacitation = DB::transaction(function () use ($validated) {

            // 1. Crear el registro base de la capacitación
            $capacitation = Capacitation::create([
                'name'        => $validated['name'],
                'date'        => $validated['date'],
                'start_time'  => $validated['start_time'],
                'end_time'    => $validated['end_time'],
                'location'    => $validated['location'],
                'description' => $validated['description'] ?? null,
            ]);

            // 2. Sincronizar tablas pivote si contienen elementos seleccionados
            if (!empty($validated['internal_guests'])) {
                $capacitation->internalGuests()->sync($validated['internal_guests']);
            }

            if (!empty($validated['external_guests'])) {
                $capacitation->externalGuests()->sync($validated['external_guests']);
            }

            return $capacitation;
        });

        // Cargamos los contadores para que el recurso devuelva el total_guests inicial correcto en la respuesta
        $capacitation->loadCount(['internalGuests', 'externalGuests']);

        return response()->json([
            'message' => 'Capacitación registrada con éxito',
            'data'    => new CapacitationResource($capacitation)
        ], 201);
    }

    /**
     * Actualizar una capacitación existente.
     */
    public function update(UpdateCapacitationRequest $request, $id)
    {
        $capacitation = Capacitation::findOrFail($id);

        $validated = $request->validated();

        $capacitation->update([
            'name'        => $validated['name'],
            'date'        => $validated['date'],
            'start_time'  => $validated['start_time'],
            'end_time'    => $validated['end_time'],
            'location'    => $validated['location'],
            'description' => $validated['description'] ?? null,
        ]);

        // 4. Sincronizar las tablas pivote usando los métodos correctos de tu modelo
        $capacitation->internalGuests()->sync($validated['internal_guests'] ?? []);
        $capacitation->externalGuests()->sync($validated['external_guests'] ?? []);

        // 5. Lógica de notificaciones
        if ($request->boolean('send_emails')) {

        }

        // 6. Cargar los contadores y las colecciones de objetos con sus IDs
        $capacitation->loadCount([
            'internalGuests as internal_count',
            'externalGuests as external_count'
        ]);

        // Cargamos las relaciones completas para retornar los objetos al cliente
        $capacitation->load(['internalGuests', 'externalGuests']);

        // Renombramos las llaves en la respuesta sobre la marcha para comodidad en Vue
        $responseData = $capacitation->toArray();

        $responseData['internal_guests'] = $responseData['internal_guests'] ?? $responseData['internal_fields'] ?? [];
        $responseData['external_guests'] = $responseData['external_guests'] ?? $responseData['external_fields'] ?? [];

        return response()->json([
            'message' => 'Capacitación actualizada con éxito',
            'data'    => $responseData
        ], 200);
    }
}
