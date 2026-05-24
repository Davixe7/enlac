<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CapacitationResource extends JsonResource
{
    /**
     * Transformar el recurso en un array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'date'             => $this->date,
            'start_time'       => $this->start_time, // Hora inicial
            'end_time'         => $this->end_time,   // Hora final
            'location'         => $this->location,
            'description'      => $this->description,
            // Contadores separados para la sumatoria de la tabla
            'internal_count'   => $this->internal_guests_count ?? 0,
            'external_count'   => $this->external_guests_count ?? 0,
            'created_at'       => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'       => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
