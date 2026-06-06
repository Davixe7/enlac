<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonorFiscalRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Usamos $this->resource para referirnos al modelo instanciado
        return [
            'id'                  => $this->resource->id,
            'donor_id'            => $this->resource->donor_id,
            'commercial_name'     => $this->resource->commercial_name,
            'tax_name'            => $this->resource->tax_name,
            'rfc'                 => $this->resource->rfc,
            'email'               => $this->resource->email,
            'tax_regimen'         => $this->resource->tax_regimen,
            'cfdi_use'            => $this->resource->cfdi_use,
            'company_anniversary' => $this->resource->company_anniversary ? $this->resource->company_anniversary->format('Y-m-d') : null,
            'street'              => $this->resource->street,
            'exterior_number'     => $this->resource->exterior_number,
            'neighborhood'        => $this->resource->neighborhood,
            'postal_code'         => $this->resource->postal_code,
            'city'                => $this->resource->city,
            'state'               => $this->resource->state,

            'billing_contact_name'    => $this->resource->billing_contact_name,
            'billing_job_title'       => $this->resource->billing_job_title,
            'billing_landline'        => $this->resource->billing_landline,
            'billing_cellphone'       => $this->resource->billing_cellphone,
            'billing_email'           => $this->resource->billing_email,
            'billing_birth_date'      => $this->resource->billing_birth_date ? $this->resource->billing_birth_date->format('Y-m-d') : null,
            'home_collection'         => (bool) $this->resource->home_collection,
            'payment_day'             => $this->resource->payment_day,
            'billing_street'          => $this->resource->billing_street,
            'billing_exterior_number' => $this->resource->billing_exterior_number,
            'billing_neighborhood'    => $this->resource->billing_neighborhood,
            'billing_postal_code'     => $this->resource->billing_postal_code,
            'billing_city'            => $this->resource->billing_city,
            'billing_state'           => $this->resource->billing_state,

            'created_at' => $this->resource->created_at?->toIso8601String(),
            'updated_at' => $this->resource->updated_at?->toIso8601String(),
        ];
    }
}
