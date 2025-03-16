<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrainFunctionRankRequest extends FormRequest
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
            'caracteristic'     => 'required|in:0,F,P',
            'comments'          => 'nullable|string|required_if:caracteristic,0,F|max:500',
            'laterality_impact' => 'nullable|in:l,r|required_if:caracteristic,0,F',
            'brain_level_id'    => 'required|exists:brain_levels,id',
            'brain_function_id' => 'required|exists:brain_functions,id',
            'candidate_id'      => 'required|exists:candidates,id',
        ];
    }

    public function messages(): array
    {
        return [
            'caracteristic.required' => 'Este campo es requerido.',
            'caracteristic.in' => 'Debes seleccionar 0, F o P.',
            'comments.string' => 'El comentario debe ser una cadena de texto.',
            'comments.required_if' => 'El comentario es obligatorio.',
            'laterality_impact.required_if' => 'Este campo es requerido.',
            'laterality_impact.in' => 'Debe seleccionar el Hemisferio del impacto.',
            'brain_level_id.required' => 'El nivel cerebral es requerido.',
            'brain_level_id.exists' => 'El nivel cerebral seleccionado no existe.',
            'brain_function_id.required' => 'La función cerebral es requerida.',
            'brain_function_id.exists' => 'La función cerebral seleccionada no existe.',
            'candidate_id.required' => 'El candidato es requerido.',
            'candidate_id.exists' => 'El candidato seleccionado no existe.',
        ];
    }
}
