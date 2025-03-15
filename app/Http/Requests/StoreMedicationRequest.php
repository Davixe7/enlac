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
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del medicamento es requerido.',
            'name.string' => 'El nombre del medicamento debe ser una cadena de texto.',
            'name.max' => 'El nombre del medicamento no debe exceder los 255 caracteres.',
            'dose.required' => 'La dosis es requerida.',
            'dose.string' => 'La dosis debe ser una cadena de texto.',
            'dose.max' => 'La dosis no debe exceder los 255 caracteres.',
            'frequency.required' => 'La frecuencia es requerida.',
            'frequency.string' => 'La frecuencia debe ser una cadena de texto.',
            'frequency.max' => 'La frecuencia no debe exceder los 255 caracteres.',
            'duration.required' => 'La duración es requerida.',
            'duration.string' => 'La duración debe ser una cadena de texto.',
            'duration.max' => 'La duración no debe exceder los 255 caracteres.',
        ];
    }
}
