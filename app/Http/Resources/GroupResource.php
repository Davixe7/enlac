<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CandidateResource;

class GroupResource extends JsonResource
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
            'name' => $this->name,
            'program' => new ProgramResource($this->whenLoaded('program')),
            'titular' => optional($this->leader)->full_name,
            'asistente' => optional($this->assistant)->full_name,
            'candidates_count' => $this->candidates_count,
            'candidates' => CandidateResource::collection($this->whenLoaded('candidates')),
            'program_id' => $this->program_id,
            'group_leader_id' => $this->group_leader_id,
            'assistant_id' => $this->assistant_id,
            'plans' => $this->plans()->where('category_id', 5),
            'is_individual' => $this->is_individual,
        ];
    }
}
