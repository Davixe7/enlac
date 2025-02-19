<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'candidate_id' => $this->candidate_id,
            'appointment_type' => $this->appointment_type,
            'user_id' => $this->user_id,
            'date' => $this->date,
            'time_slot' => $this->time_slot,
            'observation' => $this->observation,
        ];
    }
}
