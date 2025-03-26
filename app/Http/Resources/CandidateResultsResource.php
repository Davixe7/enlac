<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateResultsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'full_name' => $this->full_name,
            'full_name' => $this->full_name,
            'sheet' => $this->sheet,
            'acceptance_status' => !is_null($this->acceptance_status) ? intval($this->acceptance_status) : null,
            'evaluator' => $this->evaluator,
            'evaluation_schedule' => $this->evaluation_schedule,
        ];
    }
}
