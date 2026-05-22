<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Datos del Donante fijos
            'donor_id' => 'required|exists:donors,id',
            'procuration_activity_id' => 'required|exists:procuration_activities,id',
            'activity_type' => 'required|string',

            // Info Financiera fija
            'concept' => 'nullable|string',
            'payment_date' => 'required|date_format:Y-m-d',
            'payment_method' => 'required|in:Efectivo,Transferencia,Depósito,Cheque,Tarjeta Débito,Tarjeta Crédito,Oxxo',
            'reference' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:MXN,DLLS',
            'exchange_rate' => 'required_if:currency,DLLS|nullable|numeric|min:0.0001',
            'equivalent_amount_mxn' => 'required_if:currency,DLLS|nullable|numeric',

            // Recibo Deducible
            'has_tax_receipt' => 'required|boolean',
            'tax_receipt_number' => 'required_if:has_tax_receipt,true|nullable|string|max:100',

            // VALIDACIONES CONDICIONALES DINÁMICAS (Según el Tipo de Actividad)
            // Alcancías
            'piggy_bank_location' => 'required_if:activity_type,Alcancía|nullable|string|max:255',

            // Alianza o Fundación
            'project_name' => 'required_if:activity_type,Alianza,activity_type,Fundaciones|nullable|string|max:255',

            // Boteo
            'boteo_area' => 'required_if:activity_type,Boteo|nullable|string|max:255',
            'boteo_can_number' => 'required_if:activity_type,Boteo|nullable|string|max:100',
            'boteo_ten_percent' => 'required_if:activity_type,Boteo|nullable|numeric',

            // Programa de Verano o Natación
            'beneficiary_id' => 'required_if:activity_type,Programa de Verano,activity_type,Natación|nullable|exists:beneficiaries,id',
            'external_name' => 'nullable|string|max:255',
            'group_name' => 'required_if:activity_type,Programa de Verano,activity_type,Natación|nullable|string|max:100',

            // Organismo de Gobierno
            'government_institution_name' => 'required_if:activity_type,Organismos de Gobierno|nullable|string|max:255',
        ];
    }
}
