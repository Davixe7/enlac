<?php

namespace App\Services;

use App\Mail\DeductibleReceiptRequested;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DonationService
{
    /**
     * Crea un donativo con su folio correlativo atómico.
     */
    public function createWithFolio(array $data): Donation
    {
        $donation = DB::transaction(function () use ($data) {
            $yearIndicator = '26';
            $prefix = "P-{$yearIndicator}-";

            $lastDonation = Donation::where('folio_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = $lastDonation ? (int) substr($lastDonation->folio_number, -5) + 1 : 1;
            $data['folio_number'] = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            return Donation::create($data);
        });

        // Disparar notificación si requiere recibo deducible
        $this->checkAndSendDeductibleEmail($donation);

        return $donation;
    }

    /**
     * Envía la notificación a Tesorería si la donación requiere recibo deducible.
     */
    public function checkAndSendDeductibleEmail(Donation $donation): void
    {
        if (!empty($donation->has_tax_receipt)) {
            $treasuryUsers = User::role('tesoreria')->whereNotNull('email')->get();

            if ($treasuryUsers->isNotEmpty()) {
                Mail::to($treasuryUsers)->send(new DeductibleReceiptRequested($donation));
            }
        }
    }
}
