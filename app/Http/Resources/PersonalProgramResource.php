<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonalProgramResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'plan_category'   => $this->plan_category,
            'activities' => $this->whenLoaded('activities', PlanActivitiesResource::collection($this->activities))
        ]);
    }
}
