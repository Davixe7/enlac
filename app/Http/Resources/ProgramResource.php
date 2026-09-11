<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $pending = $this->pendingPriceUpdate;
        $current = $this->currentPriceRecord;

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'is_active'   => $this->is_active,
            'price'       => $this->current_price,
            'valid_since'     => $current?->valid_since ?? $this->created_at?->format('Y-m-d'),
            'next_price'          => $pending?->price ?? null,
            'next_price_date'     => $pending?->valid_since ?? null,
        ];
    }
}
