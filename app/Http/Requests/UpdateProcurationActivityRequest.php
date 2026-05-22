<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProcurationActivityRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la petición.
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string',
            'is_active' => 'sometimes|required|boolean',
            'created_date' => 'nullable|date_format:Y-m-d',
            'event_date' => 'nullable|date_format:Y-m-d',
            'place' => 'nullable|string|max:255',
            'goal_amount' => 'nullable|numeric',
            'tickets_count' => 'nullable|integer',
            'ticket_price' => 'nullable|numeric',
            'winning_ticket' => 'nullable|string|max:255',
            'winner_name' => 'nullable|string|max:255',
            'seller_winner_name' => 'nullable|string|max:255',
        ];
    }
}
