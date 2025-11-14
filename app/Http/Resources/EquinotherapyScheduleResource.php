<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquinotherapyScheduleResource extends JsonResource
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
            'id' => $this->id,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_comodin' => $this->is_comodin,
            'candidate' => new CandidateResource($this->whenLoaded('candidate'))
        ];
    }
}
