<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $paymentTypes = ['parent' => 'Cuota de Padres', 'sponsor' => 'Cuota de Padrinos'];

        $data = parent::toArray($request);
        return array_merge($data, [
            'scope' => ['Total', 'Parcial'][$this->is_partial],
            'payment_type' => $paymentTypes[$this->payment_type],
            'date' => Carbon::parse($this->date)->format('d/m/Y'),
            'amount' => '$ '  . number_format($this->amount, 2)
        ]);
    }
}
