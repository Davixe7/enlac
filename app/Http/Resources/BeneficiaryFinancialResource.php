<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BeneficiaryFinancialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $month = $request->month ?: now()->month;
        $year = now()->month > 7 ? now()->year : now()->subYear()->year;
        $date = Carbon::create( $year, $month );

        return array_merge($data, [
            'full_name'     => $this->full_name,
            'parent_paid'   => '$ ' . number_format($this->parent_paid, 2, ',', '.'),
            'sponsr_paid'   => '$ ' . number_format($this->sponsr_paid, 2, ',', '.'),
            'parent_amount' => '$ ' . number_format($this->parent_amount, 2, ',', '.'),
            'sponsr_amount' => '$ ' . number_format($this->sponsr_amount, 2, ',', '.'),
            'enlacs_amount' => '$ ' . number_format($this->enlacs_amount, 2, ',', '.'),
            'parent_status' => ($this->parent_paid == $this->parent_amount) ? 'green-2' : ((now() < $date->addDays(10)) ? 'yellow-2' : 'red-2'),
            'sponsr_status' => ($this->sponsr_paid == $this->sponsr_amount) ? 'green-2' : ((now() < $date->addDays(10)) ? 'yellow-2' : 'red-2')
        ]);
    }
}
