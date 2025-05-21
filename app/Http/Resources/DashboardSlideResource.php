<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardSlideResource extends JsonResource
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
            'picture' => $this->getFirstMediaUrl('picture'),
            'thumb' => $this->getFirstMediaUrl('picture', 'thumb')
        ]);
    }
}
