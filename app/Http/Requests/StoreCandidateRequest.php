<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends FormRequest
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
            'candidate.first_name' => 'required|string|max:255',
            'candidate.middle_name' => 'required|string|max:255',
            'candidate.last_name' => 'required|string|max:255',
            'candidate.birth_date' => 'required|date',
            'candidate.diagnosis' => 'required|string',
            'candidate.photo' => 'nullable|string',
            'candidate.info_channel' => 'required',
            'candidate.sheet' => 'nullable',

            'contacts' => 'required|array',
            'contacts.*.first_name' => 'required|string|max:255',
            'contacts.*.middle_name' => 'nullable|string|max:255',
            'contacts.*.last_name' => 'required|string|max:255',
        ];
    }
}
