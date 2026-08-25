<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRaffleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'procuration_activity_id' => 'required|exists:procuration_activities,id',
            'tickets_count'           => 'nullable|integer|min:0',
            'tickets_to_add'          => 'nullable|integer|min:0',
            'ticket_price'            => 'nullable|numeric|min:0|between:0,99999999.99',
            'place'                   => 'nullable|string|max:255',
            'winning_ticket'          => 'nullable|string|max:255',
            'winner_name'             => 'nullable|string|max:255',
            'seller_winner_name'      => 'nullable|string|max:255',
        ];
    }
}
