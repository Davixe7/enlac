<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
            'admission_comment' => 'required_if:admission_status,false,0'
        ];
    }

    public function messages(): array
    {
        return [
            'candidate_id.required' => 'El candidato es requerido.',
            'candidate_id.exists' => 'El candidato seleccionado no existe.',
        ];
    }
}
