<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCandidateRequest extends FormRequest
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
            'candidate.first_name' => 'nullable|string|max:255',
            'candidate.middle_name' => 'nullable|string|max:255',
            'candidate.last_name' => 'nullable|string|max:255',
            'candidate.birth_date' => 'nullable|date',
            'candidate.diagnosis' => 'nullable|string',
            'candidate.photo' => 'nullable|string',
            'candidate.sheet' => 'nullable',
            'candidate.info_channel' => 'nullable',

            'contacts' => 'nullable|array',
            'contacts.*.first_name' => 'nullable|string|max:255',
            'contacts.*.middle_name' => 'nullable|string|max:255',
            'contacts.*.last_name' => 'nullable|string|max:255',
        ];
    }
}
