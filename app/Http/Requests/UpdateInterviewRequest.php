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
            'interview.candidate_id'         => 'nullable|exists:candidates,id',
            'interview.content'              => 'nullable|string',
            'interview.observation'          => 'nullable|string',
            'interview.apgar_rank'           => 'nullable|numeric',
            'interview.sphincters_control'   => 'nullable',
            'interview.signed_at'            => 'nullable|date',
            'interview.answers'              => 'nullable|array',

            'interviewee.name'               => 'nullable',
            'interviewee.candidate_id'       => 'nullable|exists:candidates,id',
            'interviewee.relationship'       => 'nullable',
            'interviewee.legal_relationship' => 'nullable',
        ];
    }
}
