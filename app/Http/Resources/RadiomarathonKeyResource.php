<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RadiomarathonKeyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'code'           => $this->code,
            'classification' => $this->classification,
            'concept'        => $this->concept,
            'is_active'      => (bool) $this->is_active,
            'created_at'     => $this->created_at?->toIso8601String(),
        ];
    }
}
