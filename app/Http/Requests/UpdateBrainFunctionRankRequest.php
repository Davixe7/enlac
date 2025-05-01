<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrainFunctionRankRequest extends FormRequest
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
            'caracteristic' => 'in:0,F,P',
            'comments' => 'nullable|string|required_if:caracteristic,0,F|max:500',
            'laterality_impact' => 'in:l,r,b|required_if:caracteristic,0,F',
            'brain_level_id' => 'exists:brain_levels,id',
            'brain_function_id' => 'exists:brain_functions,id',
            'candidate_id' => 'exists:candidates,id',
        ];
    }

    public function attributes() {
        return [
            'caracteristic'     => 'nivel de impacto',
            'comments'          => 'comentario',
            'laterality_impact' => 'lateralidad',
        ];
    }
}
