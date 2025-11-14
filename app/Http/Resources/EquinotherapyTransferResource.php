<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquinotherapyTransferResource extends JsonResource
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
            'ida' => $this->ida,
            'regreso' => $this->regreso,
            'candidate' => new CandidateResource($this->whenLoaded('candidate'))
        ];
    }
}
