<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInterviewRequest extends FormRequest
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
            'candidate_id' => 'required|exists:candidates,id',
            'signed_at' => 'nullable|date',
            'observation' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'parent_name.required' => 'El nombre del padre/madre es requerido.',
            'parent_name.string' => 'El nombre del padre/madre debe ser una cadena de texto.',
            'parent_name.max' => 'El nombre del padre/madre no debe exceder los 255 caracteres.',
            'apgar_rank.required' => 'El rango de Apgar es requerido.',
            'apgar_rank.integer' => 'El rango de Apgar debe ser un número entero.',
            'apgar_rank.min' => 'El rango de Apgar debe ser como mínimo 1.',
            'apgar_rank.max' => 'El rango de Apgar debe ser como máximo 10.',
            'candidate_id.required' => 'El campo candidato es requerido.',
            'candidate_id.exists' => 'El candidato seleccionado no existe.',
            'sphincter.required' => 'El campo esfínteres es requerido.',
            'sphincter.boolean' => 'El campo esfínteres debe ser un valor booleano.',
            'signed_at.date' => 'La fecha de firma debe ser un formato válido.',
            'observation.string' => 'La observación debe ser una cadena de texto.',
        ];
    }
}
