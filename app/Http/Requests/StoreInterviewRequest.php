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
            'interview.candidate_id'       => 'required|exists:candidates,id',
            'interview.content'            => 'required|string',
            'interview.observation'        => 'nullable|string',
            'interview.apgar_rank'         => 'required|numeric',
            'interview.sphincters_control' => 'required',
            'interview.signed_at'          => 'nullable|date',
            'interview.answers'            => 'nullable|array',

            'interviewee.name'               => 'required',
            'interviewee.candidate_id'       => 'required|exists:candidates,id',
            'interviewee.relationship'       => 'required',
            'interviewee.legal_relationship' => 'required',
        ];
    }
}
