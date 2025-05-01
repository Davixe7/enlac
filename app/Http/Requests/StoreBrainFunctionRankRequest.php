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
            'comments'          => '', /* required_if:caracteristic,0,F|string|max:500 */
            'laterality_impact' => 'required_if:caracteristic,0,F|in:l,r,b',
            'brain_level_id'    => 'required|exists:brain_levels,id',
            'brain_function_id' => 'required|exists:brain_functions,id',
            'candidate_id'      => 'required|exists:candidates,id',
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
