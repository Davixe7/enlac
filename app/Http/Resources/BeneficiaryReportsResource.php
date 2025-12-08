<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BeneficiaryReportsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request); 

        return [
            'beneficiaries' => BeneficiaryResource::collection($this->resource['beneficiaries']),
            'counts'        => $this->resource['counts']
        ];
    }
}
