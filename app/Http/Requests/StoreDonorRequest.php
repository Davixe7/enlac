<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonorRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'second_last_name' => 'nullable|string|max:255',
            'cellphone' => 'required|string|size:10',
            'sector' => 'required|string',
            'contact_restrictions' => 'required|string',
            'is_active' => 'boolean',

            // Validaciones para los datos fiscales que vienen del modal hijo (DonorFiscalRecordModal.vue)
            'fiscal_records'                             => 'nullable|array',
            'fiscal_records.*.id'                        => 'nullable',
            'fiscal_records.*.commercial_name'           => 'required|string',
            'fiscal_records.*.tax_name'                  => 'required|string',
            'fiscal_records.*.rfc'                       => 'required|string',
            'fiscal_records.*.tax_regimen'               => 'required|string',
            'fiscal_records.*.cfdi_use'                  => 'required|string',
            'fiscal_records.*.email'                     => 'required|email',
            'fiscal_records.*.company_anniversary'       => 'nullable|date_format:Y-m-d',
            'fiscal_records.*.street'                    => 'nullable|string',
            'fiscal_records.*.exterior_number'           => 'nullable|string',
            'fiscal_records.*.neighborhood'              => 'nullable|string',
            'fiscal_records.*.postal_code'               => 'required|string',
            'fiscal_records.*.city'                      => 'nullable|string',
            'fiscal_records.*.state'                     => 'nullable|string',

            // Campos de cobranza
            'fiscal_records.*.billing_contact_name'      => 'required|string',
            'fiscal_records.*.billing_job_title'         => 'nullable|string',
            'fiscal_records.*.billing_landline'          => 'nullable|string',
            'fiscal_records.*.billing_cellphone'         => 'nullable|string',
            'fiscal_records.*.billing_email'             => 'nullable|email',
            'fiscal_records.*.billing_birth_date'        => 'nullable|date_format:Y-m-d',
            'fiscal_records.*.home_collection'           => 'nullable|boolean',
            'fiscal_records.*.payment_day'               => 'nullable|string',
            'fiscal_records.*.billing_street'            => 'nullable|string',
            'fiscal_records.*.billing_exterior_number'   => 'nullable|string',
            'fiscal_records.*.billing_neighborhood'      => 'nullable|string',
            'fiscal_records.*.billing_postal_code'       => 'nullable|string',
            ];
    }
}
