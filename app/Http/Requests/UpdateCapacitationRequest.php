<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCapacitationRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtén las reglas de validación que se aplicarán a la solicitud.
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
            'internal_guests.*' => 'integer|exists:users,id', // Valida que cada ID exista en la tabla users
            'external_guests'   => 'nullable|array',
            'external_guests.*' => 'integer|exists:contacts,id', // Valida que cada ID exista en la tabla contacts
            'send_emails'       => 'boolean',
        ];
    }

    /**
     * Obtén los mensajes de error personalizados para las reglas de validación.
     */
    public function messages(): array
    {
        return [
            'name.required'        => 'El nombre de la capacitación es obligatorio.',
            'date.required'        => 'La fecha de ejecución es obligatoria.',
            'date.date'            => 'La fecha ingresada no tiene un formato válido.',
            'start_time.required'  => 'La hora de inicio es obligatoria.',
            'end_time.required'    => 'La hora de finalización es obligatoria.',
            'location.required'    => 'El lugar o ubicación es obligatorio.',
            'internal_guests.array'=> 'Los invitados internos deben estructurarse como un listado.',
            'internal_guests.*.exists' => 'Uno o más usuarios internos seleccionados no son válidos.',
            'external_guests.array'=> 'Los invitados externos deben estructurarse como un listado.',
            'external_guests.*.exists' => 'Uno o más contactos externos seleccionados no son válidos.',
        ];
    }
}
