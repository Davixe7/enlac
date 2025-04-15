<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SponsorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $fullName = array_filter([$this->name, $this->last_name, $this->second_last_name]);
        $fullName = join(' ', $fullName);

        return array_merge($data, [
            'full_name'  => $fullName,
            'folio'      => str_pad($this->id, 4, '0', STR_PAD_LEFT),
            'entry_date' => $this->created_at->format('Y-m-d'),
            'candidates_count' => $this->payment_configs()->count()
        ]);
    }
}
