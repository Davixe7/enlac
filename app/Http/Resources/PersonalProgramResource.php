<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'name'          => $this->name,
            'status'        => intval($this->status),
            'category'      => $this->category,
            'subcategory'   => $this->subcategory,
            'activities'    => $this->whenLoaded('activities', PlanActivitiesResource::collection($this->activities)),
            'created_at'    => $this->created_at?->format('d/m/Y'),
            'date'          => $this->created_at,
            'group'         => $this->group,
            'candidate'     => $this->group ? new BeneficiaryResource($this->group->candidates()->first()) : null,
            'start_date'    => Carbon::parse($this->start_date)->format('d/m/Y'),
            'end_date'      => Carbon::parse($this->end_date)->format('d/m/Y'),
        ]);
    }
}
