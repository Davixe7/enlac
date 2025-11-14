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
            'id'            => $this->id,
            'name'          => $this->full_name,
            'sheet'         => $this->sheet,
            'entry_status'  => $this->entry_status,
            'entry_date'    => $this->entry_date,
            // --- CAMPOS DE TRANSPORTE GENERAL ---
            'requires_transport'      => $this->requires_transport,
            'transport_address'       => $this->transport_address,
            'transport_location_link' => $this->transport_location_link,
            'curp'                    => $this->curp,

            'program_name'  => $this->program ? $this->program->name : 'Sin asignar',
            'program_price' => $this->program ? $this->program->price : 'Sin asignar',
            'group_id'      => $this->whenLoaded('personal_groups', fn() => $this->personal_groups->first()?->id, null),

            'equinetherapy_permission_medical' => $this->equinetherapy_permission_medical,
            'equinetherapy_permission_legal_guardian' => $this->equinetherapy_permission_legal_guardian,

            'status' => $this->status,
            'status_history' => $this->whenLoaded('statusHistory', function () {
                return $this->statusHistory->map(fn($h) => [
                    'status'       => $h->status,
                    'comment'      => $h->comment,
                    'document_url' => $h->document_path ? asset('storage/'.$h->document_path) : null,
                    'changed_at'   => \Carbon\Carbon::parse($h->changed_at)->format('Y-m-d H:i:s'),
                ]);
            }),
            'can_reingresar' => ($this->status) === 'inactivo',
            'scheduled_entry_date' => $this->scheduled_entry_date ? Carbon::parse($this->scheduled_entry_date)->format('d/m/Y') : null
        ];
    }
}
