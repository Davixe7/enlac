<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrainFunctionRankResource extends JsonResource
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
            'caracteristic' => $this->caracteristic,
            'comments' => $this->comments,
            'laterality_impact' => $this->laterality_impact,
            'brain_level' => new BrainLevelResource($this->brainLevel),
            'brain_function' => new BrainFunctionResource($this->brainFunction),
            'evaluation_id'  => $this->evaluation_id,
            'brain_level_id' => $this->brain_level_id,
            'brain_function_id' => $this->brain_function_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
