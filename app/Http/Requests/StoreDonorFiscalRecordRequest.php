<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonorFiscalRecordRequest extends FormRequest
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
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'donor_id'             => $isUpdate ? 'nullable|exists:donors,id' : 'required|exists:donors,id',
            'commercial_name'      => 'required|string|max:255',
            'tax_name'             => 'required|string|max:255',
            'rfc'                  => 'required|string|min:12|max:13',
            'email'                => 'required|email|max:255',
            'tax_regimen'          => 'required|string|max:255',
            'cfdi_use'             => 'required|string|max:255',
            'company_anniversary'  => 'nullable|date',
            'postal_code'          => 'required|string|max:10',
            'street'               => 'required|string|max:255',
            'exterior_number'      => 'required|string|max:50',
            'neighborhood'         => 'required|string|max:255',
            'city'                 => 'required|string|max:255',
            'state'                => 'required|string|max:255',

            // Cobranza
            'billing_contact_name'    => 'required|string|max:255',
            'billing_job_title'       => 'nullable|string|max:255',
            'billing_landline'        => 'nullable|string|max:50',
            'billing_cellphone'       => 'nullable|string|max:50',
            'billing_email'           => 'nullable|email|max:255',
            'billing_birth_date'      => 'nullable|date',
            'home_collection'         => 'required|boolean',
            'payment_day'             => 'nullable|string|max:255',
            'billing_street'          => 'nullable|string|max:255',
            'billing_exterior_number' => 'nullable|string|max:50',
            'billing_neighborhood'    => 'nullable|string|max:255',
            'billing_postal_code'     => 'nullable|string|max:10',
            'billing_city'            => 'nullable|string|max:255',
            'billing_state'           => 'nullable|string|max:255',
        ];
    }
}
