<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'evaluator' => new UserResource($this->evaluator),
            'date' => Carbon::parse($this->date)->format('Y-m-d H:i:s'),
            'frontendDate' => Carbon::parse($this->date)->format('d/m/Y'),
            'frontendTime' => Carbon::parse($this->date)->format('H:i'),
        ]);
    }
}
