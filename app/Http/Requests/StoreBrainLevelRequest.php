<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrainLevelRequest extends FormRequest
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
            'grade' => 'required|string|max:255',
            'S' => 'required|integer|min:1',
            'P' => 'required|integer|min:1',
            'L' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es requerido.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no debe exceder los 255 caracteres.',
            'grade.required' => 'El grado es requerido.',
            'grade.string' => 'El grado debe ser una cadena de texto.',
            'grade.max' => 'El grado no debe exceder los 255 caracteres.',
            'S.required' => 'El valor de S es requerido.',
            'S.integer' => 'El valor de S debe ser un número entero.',
            'S.min' => 'El valor de S no debe ser menor que 0.',
            'P.required' => 'El valor de P es requerido.',
            'P.integer' => 'El valor de P debe ser un número entero.',
            'P.min' => 'El valor de P no debe ser menor que 0.',
            'L.required' => 'El valor de L es requerido.',
            'L.integer' => 'El valor de L debe ser un número entero.',
            'L.min' => 'El valor de L no debe ser menor que 0.',
        ];
    }
}
