<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Interceptamos el string del Front antes de validar y lo normalizamos
        $this->merge([
            'knows_facilities' => $this->knows_facilities === 'SÍ',
        ]);
    }

    public function rules(): array
    {
        return [
            'donor_type'           => 'required|string',
            'first_name'           => 'required|string|max:255',
            'last_name'            => 'required|string|max:255',
            'second_last_name'     => 'nullable|string|max:255',
            'preferred_name'       => 'nullable|string|max:255',
            'marital_status'       => 'nullable|string',
            'gender'               => 'nullable|string',
            'cellphone'            => 'required|string|max:20',
            'landline'             => 'nullable|string|max:20',
            'personal_email'       => 'nullable|email|max:255',

            // 👈 Cambiado a boolean para hacer match perfecto con lo que inyectó prepareForValidation
            'knows_facilities'     => 'nullable|boolean',

            'sector'               => 'required|string',
            'street'               => 'nullable|string',
            'exterior_number'      => 'nullable|string',
            'neighborhood'         => 'nullable|string',
            'postal_code'          => 'nullable|string',
            'city'                 => 'nullable|string',
            'state'                => 'nullable|string',
            'country'              => 'nullable|string',
            'is_private_contact'   => 'nullable|integer',
            'notes'                => 'nullable|string',
            'contact_restrictions' => 'nullable|string',
            'prospect_for'         => 'nullable|array',

            // Los campos que ya corregimos
            'company_name'         => 'nullable|string|max:255',
            'job_title'            => 'nullable|string|max:255',
            'birth_date'           => 'nullable|date_format:Y-m-d',

            // Datos del Cónyuge
            'spouse_first_name'       => 'nullable|string|max:255',
            'spouse_last_name'        => 'nullable|string|max:255',
            'spouse_second_last_name' => 'nullable|string|max:255',
            'spouse_birth_date'       => 'nullable|date_format:Y-m-d',
            'wedding_anniversary'     => 'nullable|string',

            // Registros fiscales anidados
            'fiscal_records'          => 'nullable|array',
        ];
    }
}
