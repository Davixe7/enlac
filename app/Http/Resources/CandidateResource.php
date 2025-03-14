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
        $data = parent::toArray($request);
        $data['acceptance_status'] = !is_null($this->acceptance_status) ? intval($this->acceptance_status) : null;
        $ranks = $this->brainFunctionRanks->groupBy('brain_level_id');
        $ranks = $ranks->map(fn($rank)=>["functions" => $rank->keyBy('brain_function_id')]);

        return array_merge($data, [
            'full_name' => $this->full_name,
            'picture' => $this->getFirstMediaUrl('profile_picture'),
            'contact' => $this->contacts->load('addresses')->first(),
            'medications' => $this->medications,
            'evaluation_schedules' => $this->whenLoaded('evaluation_schedules'),
            'evaluation_schedule' => $this->evaluation_schedule,
            'brain_function_ranks' => $ranks,
            'program' => $this->whenLoaded('program'),
        ]);
    }
}
