<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonorFiscalRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'donor_id' => $this->donor_id,
            'commercial_name' => $this->commercial_name,
            'tax_name' => $this->tax_name,
            'rfc' => $this->rfc,
            'email' => $this->email,
            'tax_regimen' => $this->tax_regimen,
            'cfdi_use' => $this->cfdi_use,
            'company_anniversary' => $this->company_anniversary ? $this->company_anniversary->format('Y-m-d') : null,
            'street' => $this->street,
            'exterior_number' => $this->exterior_number,
            'neighborhood' => $this->neighborhood,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'state' => $this->state,

            // Datos de cobranza
            'billing_contact_name' => $this->billing_contact_name,
            'billing_job_title' => $this->billing_job_title,
            'billing_landline' => $this->billing_landline,
            'billing_cellphone' => $this->billing_cellphone,
            'billing_email' => $this->billing_email,
            'billing_birth_date' => $this->billing_birth_date ? $this->billing_birth_date->format('Y-m-d') : null,
            'home_collection' => (int) $this->home_collection, // Casteado a entero/booleano limpio para Quasar
            'payment_day' => $this->payment_day,

            // Domicilio alternativo de cobranza
            'billing_street' => $this->billing_street,
            'billing_exterior_number' => $this->billing_exterior_number,
            'billing_neighborhood' => $this->billing_neighborhood,
            'billing_postal_code' => $this->billing_postal_code,
            'billing_city' => $this->billing_city,
            'billing_state' => $this->billing_state,

            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
