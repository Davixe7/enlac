<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicationRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'dose' => 'required|string|max:255',
            'frequency' => 'required|string|max:255',
            'duration' => 'required|string|max:255',
            'candidate_id' => 'required|exists:candidates,id',
            'status' => 'required'
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
