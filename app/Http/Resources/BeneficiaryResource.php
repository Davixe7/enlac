<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BeneficiaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->full_name,
            'sheet'        => $this->sheet,
            'status'       => $this->status,
            'onboard_at'   => $this->onboard_at,
            'program_name' => $this->program->name,
            'program_price' => $this->program->price,
        ];

    }
}
