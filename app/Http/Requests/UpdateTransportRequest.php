<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransportRequest extends FormRequest
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
        $candidateId = $this->route('candidate');

        return [
            'requires_transport'       => 'required|boolean',
            'transport_address'        => 'required_if:requires_transport,true|nullable|string|max:500',
            'transport_location_link'  => 'required_if:requires_transport,true|nullable|url|max:255',
            'curp'                     => [
                'required_if:requires_transport,true',
                'nullable',
                'string',
                'max:18',
                Rule::unique('candidates', 'curp')->ignore($candidateId),
            ],
        ];
    }
}

