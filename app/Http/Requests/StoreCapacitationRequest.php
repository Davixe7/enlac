<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCapacitationRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true; // Cambiar a true para permitir el acceso (o aplicar tu lógica de roles/Spatie si aplica)
    }

    /**
     * Obtener las reglas de validación que se aplicarán a la solicitud.
     */
    public function rules(): array
    {
        return [
            'name'              => 'required|string|max:255',
            'date'              => 'required|date',
            'start_time'        => 'required|string',
            'end_time'          => 'required|string',
            'location'          => 'required|string|max:255',
            'description'       => 'nullable|string',
            'internal_guests'   => 'nullable|array',
            'internal_guests.*' => 'exists:users,id',
            'external_guests'   => 'nullable|array',
            'external_guests.*' => 'exists:contacts,id',
            'send_emails'       => 'nullable|boolean', // Captura el flag del botón del frontend
        ];
    }
}
