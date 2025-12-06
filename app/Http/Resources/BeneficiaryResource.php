<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BeneficiaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                                      => $this->id,
            'name'                                    => $this->full_name,
            'sheet'                                   => $this->sheet,
            'candidate_status_id'                     => $this->candidate_status_id,
            'status'                                  => $this->whenLoaded('candidateStatus'),
            'entry_date'                              => $this->entry_date ? Carbon::parse($this->entry_date)->format('d/m/Y') : null,
            'requires_transport'                      => $this->requires_transport,
            'program'                                 => $this->whenLoaded('program'),
            'program_name'                            => $this->whenLoaded('program', $this->program->name),
            'program_price'                           => $this->whenLoaded('program', $this->program->price),
            'group_id'                                => $this->whenLoaded('personal_groups', fn() => $this->personal_groups->first()?->id, null),
            'equinetherapy_permission_medical'        => $this->equinetherapy_permission_medical,
            'equinetherapy_permission_legal_guardian' => $this->equinetherapy_permission_legal_guardian,
        ];
    }
}
