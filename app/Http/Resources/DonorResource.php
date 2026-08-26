<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $hasPromise = $this->payment_promises->isNotEmpty();
        $promise    = $this->payment_promises->first();

        return array_merge($data, [
            'donations_sum_amount'        => $this->donations_sum_amount,
            'payment_promises_sum_amount' => $this->payment_promises_sum_amount,
            'donations_sum_amount_format' => '$' . number_format($this->donations_sum_amount, 2),
            'full_name'                   => join(' ', array_filter([$this->first_name, $this->last_name, $this->second_lastname])),
            'payment_status'              => $this->donations_sum_amount >= $this->payment_promises_sum_amount ? 'Total' : 'Parcial',
            'published'                   => $hasPromise && $promise->published_at ? 'Publicado' : 'Prospecto',
            'published_at'                => $hasPromise && $promise->published_at ? $promise->published_at : '--'
        ]);
    }
}
