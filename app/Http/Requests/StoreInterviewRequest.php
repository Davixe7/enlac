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
            'candidate_id'       => 'required|exists:candidates,id',
            'content'            => 'required|string',
            'observation'        => 'nullable|string',
            'apgar_rank'         => 'required|numeric',
            'sphincters_control' => 'required',
            'signed_at'          => 'nullable|date',
            'answers'            => 'nullable|array',

            'interviewee.name'               => 'required',
            'interviewee.relationship'       => 'required',
            'interviewee.legal_relationship' => 'required',
        ];
    }
}
