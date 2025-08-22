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

        //$ranks = $this->brainFunctionRanks->groupBy('brain_level_id');
        //$ranks = $ranks->map(fn($rank)=>["functions" => $rank->keyBy('brain_function_id')]);

        return array_merge($data, [
            'full_name'            => $this->full_name,
            'admission_status'    => $this->admission_status,
            'picture'              => $this->getFirstMediaUrl('profile_picture'),
            'contacts'             => $this->contacts,
            'contact'              => $this->contacts()->first(),
            'medications'          => $this->medications,
            'evaluation_schedules' => $this->evaluation_schedules,
            'evaluation_schedule'  => $this->evaluation_schedule,
            //'brain_function_ranks' => $ranks,
            'program'              => $this->program,
            'chronological_age'    => number_format( Carbon::parse($this->birth_date)->diffInMonths(), 2 ),
            'interviewee'          => $this->whenLoaded('interviewee')
        ]);
    }
}
