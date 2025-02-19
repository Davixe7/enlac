<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InterviewResource extends JsonResource
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
            'parent_name' => $this->parent_name,
            'apgar_rank' => $this->apgar_rank,
            'candidate_id' => $this->candidate_id,
            'sphincter' => $this->sphincter,
            'signed_at' => $this->signed_at,
            'observation' => $this->observation,];
    }
}
