<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSponsorRequest extends FormRequest
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
            'name'             => 'required|string|max:191',
            'last_name'        => 'required|string|max:191',
            'second_last_name' => 'required|string|max:191',
            'company_name'     => 'nullable|string|max:255',
            'marital_status'   => 'nullable|string|max:191',
            'gender'           => 'nullable|string|in:male,female,entity',
            'birthdate'        => 'required|date',
            'contact_by'       => 'required',
            'is_anonymous'     => 'required|boolean',
            'type'             => 'required',

            'addresses.*.street'       => 'nullable',
            'addresses.*.inner_number' => 'nullable',
            'addresses.*.outer_number' => 'nullable',
            'addresses.*.neighborhood' => 'nullable',
            'addresses.*.city'         => 'nullable',
            'addresses.*.state'        => 'nullable',
            'addresses.*.country'      => 'nullable',
            'addresses.*.email'        => 'nullable',
            'addresses.*.phone'        => 'nullable',
            'addresses.*.whatsapp'     => 'nullable'
        ];
    }
}
