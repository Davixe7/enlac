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
            'candidate_id'       => 'nullable|exists:candidates,id',
            'content'            => 'nullable|string',
            'observation'        => 'nullable|string',
            'apgar_rank'         => 'nullable|numeric',
            'sphincters_control' => 'nullable',
            'signed_at'          => 'nullable|date',
            'answers'            => 'nullable|array',

            'interviewee.name'               => 'nullable',
            'interviewee.relationship'       => 'nullable',
            'interviewee.legal_relationship' => 'nullable',
        ];
    }

    public function attributes(): array {
        return [
            'interviewee.name'               => 'nombre',
            'interviewee.relationship'       => 'parentesco',
            'interviewee.legal_relationship' => 'es hijo biologico',
        ];
    }
}
