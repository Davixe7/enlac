<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrainLevelRequest extends FormRequest
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
            'grade' => 'string|max:255',
            'S' => 'integer|min:1',
            'P' => 'integer|min:1',
            'L' => 'integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no debe exceder los 255 caracteres.',
            'grade.string' => 'El grado debe ser una cadena de texto.',
            'grade.max' => 'El grado no debe exceder los 255 caracteres.',
            'S.integer' => 'El valor de S debe ser un número entero.',
            'S.min' => 'El valor de S no debe ser menor que 0.',
            'P.integer' => 'El valor de P debe ser un número entero.',
            'P.min' => 'El valor de P no debe ser menor que 0.',
            'L.integer' => 'El valor de L debe ser un número entero.',
            'L.min' => 'El valor de L no debe ser menor que 0.',
        ];
    }
}
