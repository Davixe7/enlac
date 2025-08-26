<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicationRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'         => 'string|max:255',
            'dose'         => 'string|max:255',
            'frequency'    => 'string|max:255',
            'duration'     => 'string|max:255',
            'observations' => 'nullable|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'         => 'nombre',
            'dose'         => 'dosis',
            'frequency'    => 'frecuencia',
            'duration'     => 'duracion',
            'observations' => 'observaciones',
        ];
    }
}
