<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SponsorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $defaultAddress = [
            "street" => "",
            "inner_number" => "",
            "outer_number" => "",
            "neighborhood" => "",
            "city" => "",
            "state" => "",
            "country" => "",
            "email" => "",
            "phone" => "",
            "whatsapp" => ""
        ];

        $data = parent::toArray($request);
        $fullName = array_filter([$this->name, $this->last_name, $this->second_last_name]);
        $fullName = join(' ', $fullName);
        $homeAddress = $this->addresses()->whereType('home')->first() ?: array_merge($defaultAddress, ['type' => 'home']);
        $officeAddress = $this->addresses()->whereType('office')->first() ?: array_merge($defaultAddress, ['type' => 'office']);

        return array_merge($data, [
            'addresses'  => [$homeAddress, $officeAddress],
            'full_name'  => $fullName,
            'folio'      => str_pad($this->id, 4, '0', STR_PAD_LEFT),
            'entry_date' => $this->created_at->format('Y-m-d'),
            'candidates_count' => $this->payment_configs()->count(),
            'profile_picture' => $this->getFirstMediaUrl('profile_picture'),
            'gender' => $this->gender,
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y')

        ]);
    }
}
