<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCandidateRequest extends FormRequest
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
            'candidate.middle_name' => 'nullable|string|max:255',
            'candidate.last_name' => 'required|string|max:255',
            'candidate.birth_date' => 'required|date',
            'candidate.age' => 'required|integer|min:1',
            'candidate.chronological_age' => 'required|integer|min:1',
            'candidate.diagnosis' => 'nullable|string',
            'candidate.photo' => 'nullable|string',

            'contact.first_name' => 'required|string|max:255',
            'contact.middle_name' => 'nullable|string|max:255',
            'contact.last_name' => 'required|string|max:255',
            'contact.relationship' => 'required|string|max:255',
            'contact.enlac_responsible' => 'required|boolean',
            'contact.legal_guardian' => 'required|boolean',
            'contact.email' => 'required|email|max:255',
            'contact.whatsapp' => 'nullable|string|max:255',
            'contact.home_phone' => 'nullable|string|max:255',

            'address.street' => 'required|string|max:255',
            'address.neighborhood' => 'required|string|max:255',
            'address.state' => 'required|string|max:255',
            'address.postal_code' => 'required|string|max:255',
            'address.exterior_number' => 'nullable|string|max:255',
            'address.city' => 'required|string|max:255',
            'address.country' => 'required|string|max:255',

            'medications.*.name' => 'required|string|max:255',
            'medications.*.dose' => 'required|string|max:255',
            'medications.*.frequency' => 'required|string|max:255',
            'medications.*.duration' => 'required|string|max:255',
        ];
    }


}
