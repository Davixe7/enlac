<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RideResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        return array_merge($data, [
            'schedule'  => join(" ", array_filter([$this->start_time, $this->end_time])),
            'candidate' => new BasicCandidateResource($this->candidate)
        ]);
    }
}
