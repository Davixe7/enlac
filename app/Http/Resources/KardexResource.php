<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KardexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            'template' => $this->getFirstMediaUrl('template'),
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "required" => $this->required,
            "category" => $this->category,
        ];
    }
}
