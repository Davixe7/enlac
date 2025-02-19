<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInterviewRequest extends FormRequest
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
            'parent_name' => 'nullable|string|max:255',
            'apgar_rank' => 'nullable|integer|min:1|max:10',
            'candidate_id' => 'nullable|exists:candidates,id',
            'sphincter' => 'nullable|boolean',
            'signed_at' => 'nullable|date',
            'observation' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'parent_name.string' => 'El nombre del padre/madre debe ser una cadena de texto.',
            'parent_name.max' => 'El nombre del padre/madre no debe exceder los 255 caracteres.',
            'apgar_rank.integer' => 'El rango de Apgar debe ser un número entero.',
            'apgar_rank.min' => 'El rango de Apgar debe ser como mínimo 1.',
            'apgar_rank.max' => 'El rango de Apgar debe ser como máximo 10.',
            'candidate_id.exists' => 'El candidato seleccionado no existe.',
            'sphincter.boolean' => 'El campo esfínter debe ser un valor booleano.',
            'signed_at.date' => 'La fecha de firma debe ser un formato válido.',
            'observation.string' => 'La observación debe ser una cadena de texto.',
        ];
    }
}
