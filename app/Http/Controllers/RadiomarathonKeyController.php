<?php

namespace App\Http\Controllers;

use App\Models\RadiomarathonKey;
use App\Http\Requests\StoreRadiomarathonKeyRequest;
use App\Http\Requests\UpdateRadiomarathonKeyRequest;
use App\Http\Resources\RadiomarathonKeyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RadiomarathonKeyController extends Controller
{
    /**
     * Muestra todas las claves (ordenadas automáticamente por el Scope del Modelo).
     */
    public function index(): AnonymousResourceCollection
    {
        $keys = RadiomarathonKey::all();

        return RadiomarathonKeyResource::collection($keys);
    }

    /**
     * Registra una nueva clave.
     */
    public function store(StoreRadiomarathonKeyRequest $request): RadiomarathonKeyResource
    {
        $key = RadiomarathonKey::create($request->validated());

        return new RadiomarathonKeyResource($key);
    }

    /**
     * Muestra una clave específica.
     */
    public function show(RadiomarathonKey $radiomarathonKey): RadiomarathonKeyResource
    {
        return new RadiomarathonKeyResource($radiomarathonKey);
    }

    /**
     * Actualiza una clave existente.
     */
    public function update(UpdateRadiomarathonKeyRequest $request, $id): RadiomarathonKeyResource
    {
        $key = RadiomarathonKey::findOrFail($id);
        $key->update($request->validated());

        return new RadiomarathonKeyResource($key);
    }

    /**
     * Elimina una clave.
     */
    public function destroy($id)
    {
        $key = RadiomarathonKey::findOrFail($id);
        $key->delete();

        return response()->json(['message' => 'Clave eliminada con éxito']);
    }
}
