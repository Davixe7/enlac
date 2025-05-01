<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentConfigResource extends JsonResource
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
            'type'      => $this->sponsor_id ? 'Cuota de Padrinos' : 'Cuota de Padres',
            'candidate' => new CandidateResource($this->whenLoaded('candidate')),
            'sponsor'   => new SponsorResource($this->whenLoaded('sponsor')),
        ]);
    }
}
