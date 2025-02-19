<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrainLevelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'grade' => $this->grade,
            'S' => $this->S,
            'P' => $this->P,
            'L' => $this->L,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
