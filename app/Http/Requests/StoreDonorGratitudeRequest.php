<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDonorGratitudeRequest extends FormRequest
{
    public function rules(): array
{
    // Detectamos si es una petición POST (Crear) o PUT/PATCH (Actualizar)
    $isUpdating = $this->isMethod('PUT') || $this->isMethod('PATCH');

    return [
        'donor_id'         => $isUpdating ? 'sometimes|exists:donors,id' : 'required|exists:donors,id',
        'date'             => 'required',
        'campaign_program' => 'required|string|max:255',
        'type'             => 'required|string|max:255',
        'delivery_method'  => 'required',
        'recipient_name'   => 'nullable|string|max:255',
    ];
}
}
