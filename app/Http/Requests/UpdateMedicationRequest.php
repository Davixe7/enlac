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
            'name' => 'string|max:255',
            'dose' => 'string|max:255',
            'frequency' => 'string|max:255',
            'duration' => 'string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'El nombre del medicamento debe ser una cadena de texto.',
            'name.max' => 'El nombre del medicamento no debe exceder los 255 caracteres.',
            'dose.string' => 'La dosis debe ser una cadena de texto.',
            'dose.max' => 'La dosis no debe exceder los 255 caracteres.',
            'frequency.string' => 'La frecuencia debe ser una cadena de texto.',
            'frequency.max' => 'La frecuencia no debe exceder los 255 caracteres.',
            'duration.string' => 'La duración debe ser una cadena de texto.',
            'duration.max' => 'La duración no debe exceder los 255 caracteres.',
        ];
    }
}
