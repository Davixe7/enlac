<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventsCalendarResources extends JsonResource
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
            'title' =>  $this->candidate->first_name . ' ' . $this->candidate->last_name . ' ' . $this->candidate->middle_name,
            'details' => $this->admission_comment,
            'start' => DATE('Y-m-d H:i:s', strtotime($this->date)),
            'date' => DATE('d/m/Y H:i A', strtotime($this->date)),
            'status' => $this->status,
            'type_id' => $this->type_id,
            'appointment_type' => ($this->medicalRecords && $this->medicalRecords->isNotEmpty()) 
    ? $this->medicalRecords->first()->appointment_type 
    : 2,
        ];
    }
}