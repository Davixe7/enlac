<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentConfigRequest extends FormRequest
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
            'candidate_id'             => 'required|exists:candidates,id',
            'sponsor_id'               => 'sometimes|exists:sponsors,id',
            'amount'                   => 'required|numeric|min:0',
            'frequency'                => 'required|integer|min:1|max:255',
            'month_payday'             => 'required|integer|min:1|max:31',
            'address_type'             => 'required|in:home,office',
            'wants_pickup'             => 'nullable|boolean',
            'wants_reminder'           => 'nullable|boolean',
            'wants_deductible_receipt' => 'nullable|boolean',
            'type'                     => 'nullable',

            'receipt.rfc'                      => 'required_if_accepted:wants_deductible_receipt',
            'receipt.company_name'             => 'required_if_accepted:wants_deductible_receipt',
            'receipt.fiscalRegime'             => 'required_if_accepted:wants_deductible_receipt',
            'receipt.cfdi'                     => 'required_if_accepted:wants_deductible_receipt',
            'receipt.email'                    => 'required_if_accepted:wants_deductible_receipt',
            'receipt.observations'             => 'required_if_accepted:wants_deductible_receipt',
            'receipt.fiscalStatus'             => 'required_if_accepted:wants_deductible_receipt',
            'receipt.street'                   => 'required_if_accepted:wants_deductible_receipt',
            'receipt.external_number'          => 'required_if_accepted:wants_deductible_receipt',
            'receipt.neighborhood'             => 'required_if_accepted:wants_deductible_receipt',
            'receipt.city'                     => 'required_if_accepted:wants_deductible_receipt',
            'receipt.zip_code'                 => 'required_if_accepted:wants_deductible_receipt',
            'receipt.state'                    => 'required_if_accepted:wants_deductible_receipt',
            'receipt.country'                  => 'required_if_accepted:wants_deductible_receipt',
        ];
    }

    public function attributes()
    {
        return  [
            'receipt.rfc'             => 'RFC',
            'receipt.company_name'    => 'razón social',
            'receipt.fiscalRegime'    => 'régimen fiscal',
            'receipt.cfdi'            => 'uso de CFDI',
            'receipt.email'           => 'correo electrónico',
            'receipt.observations'    => 'observaciones',
            'receipt.fiscalStatus'    => 'estatus fiscal',
            'receipt.street'          => 'calle',
            'receipt.external_number' => 'número exterior',
            'receipt.neighborhood'    => 'colonia',
            'receipt.city'            => 'ciudad',
            'receipt.zip_code'        => 'código postal',
            'receipt.state'           => 'estado',
            'receipt.country'         => 'país',
        ];
    }
}
