<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateResource extends JsonResource
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
            'program'              => $this->whenLoaded('program'),
            'status'               => $this->status->label(),
            'full_name'            => $this->full_name,
            'picture'              => $this->getFirstMediaUrl('profile_picture'),
            'contacts'             => $this->contacts,
            'contact'              => $this->contacts()->first(),
            'medications'          => $this->medications,
            'evaluation_schedules' => $this->evaluationSchedules,
            'evaluation_schedule'  => $this->evaluationSchedule,
            'chronological_age'    => number_format( Carbon::parse($this->birth_date)->diffInMonths(), 2 ),
            'chronological_age2'   => number_format( Carbon::parse($this->birth_date)->diffInYears(), 2 ),
            'interviewee'          => $this->whenLoaded('interviewee'),
            'location_detail'      => $this->whenLoaded('locationDetail', fn()=>$this->locationDetail),
        ]);
    }
}
