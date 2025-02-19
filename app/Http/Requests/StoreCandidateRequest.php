<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'age' => 'required|integer|min:1',
            'chronological_age' => 'required|integer|min:1',
            'diagnosis' => 'nullable|string',
            'photo' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'El nombre es requerido.',
            'first_name.string' => 'El nombre debe ser una cadena de texto.',
            'first_name.max' => 'El nombre no debe exceder los 255 caracteres.',
            'middle_name.string' => 'El segundo nombre debe ser una cadena de texto.',
            'middle_name.max' => 'El segundo nombre no debe exceder los 255 caracteres.',
            'last_name.required' => 'El apellido es requerido.',
            'last_name.string' => 'El apellido debe ser una cadena de texto.',
            'last_name.max' => 'El apellido no debe exceder los 255 caracteres.',
            'birth_date.required' => 'La fecha de nacimiento es requerida.',
            'birth_date.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'age.required' => 'La edad es requerida.',
            'age.integer' => 'La edad debe ser un número entero.',
            'age.min' => 'La edad no debe ser menor que 0.',
            'chronological_age.required' => 'La edad cronológica es requerida.',
            'chronological_age.integer' => 'La edad cronológica debe ser un número entero.',
            'chronological_age.min' => 'La edad cronológica no debe ser menor que 0.',
            'diagnosis.string' => 'El diagnóstico debe ser una cadena de texto.',
            'photo.string' => 'La foto debe ser una cadena de texto.',
        ];
    }
}
