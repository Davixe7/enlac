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
            'entry_status' => $this->entry_status,
            'entry_date'   => $this->entry_date,
            'program_name' => $this->program ? $this->program : $this->program->name,
            'program_price' => $this->program->price,
            'group_id'      => $this->group ? $this->group->id : null
        ];

    }
}
