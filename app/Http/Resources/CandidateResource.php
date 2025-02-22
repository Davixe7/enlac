<?php

namespace App\Http\Resources;

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
        $ranks = $this->brainFunctionRanks->groupBy('brain_level_id');
        $ranks = $ranks->map(fn($rank)=>["functions" => $rank->keyBy('brain_function_id')]);
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'birth_date' => $this->birth_date,
            'age' => $this->age,
            'chronological_age' => $this->chronological_age,
            'diagnosis' => $this->diagnosis,
            'picture' => $this->getFirstMediaUrl('profile_picture'),
            'contact' => $this->contacts->load('addresses')->first(),
            'medications' => $this->medications,
            'evaluation_schedules' => $this->whenLoaded('evaluation_schedules'),
            'evaluation_schedule' => $this->evaluation_schedule,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'brain_function_ranks' => $ranks
        ];
    }
}
