<?php

namespace App\Services;

use App\Mail\DeductibleReceiptRequested;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\ProcurationActivity;
use App\Enums\ProcurationActivityType;

class DonationService
{
    /**
     * Crea un donativo con su folio correlativo atómico.
     */
    public function createWithFolio(array $data): Donation
    {
        // 1. Limpieza y formateo de los datos recibidos
        $data = $this->prepareDonationData($data);

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
     * Limpia y mapea los datos para garantizar coincidencia
     * exacta con las columnas de la tabla `donations`.
     */
    private function prepareDonationData(array $data): array
    {
        if (isset($data['source']) && $data['source'] === 'others') {
            unset($data['donor_id']);
        }

        $isPadrinosGenerales = isset($data['activity_type'])
        && ($data['activity_type'] === 'Padrinos Generales' || $data['activity_type'] === ProcurationActivityType::PADRINOS_GENERALES->value);

        // Si es Padrinos Generales o el campo viene vacío, asigna la actividad por defecto
        if ($isPadrinosGenerales || empty($data['procuration_activity_id'])) {
            $defaultActivity = ProcurationActivity::firstOrCreate(
                ['name' => 'Padrinos Generales'],
                [
                    'type' => 'Padrinos Generales',
                    'is_active' => true,
                ]
            );

            $data['procuration_activity_id'] = $defaultActivity->id;
        }

        $fullName = $data['full_name'] ?? null;
        $companyName = $data['company_name'] ?? null;

        if ($fullName && $companyName) {
            $data['donor_name'] = "{$fullName} ({$companyName})";
        } elseif ($fullName) {
            $data['donor_name'] = $fullName;
        } elseif ($companyName) {
            $data['donor_name'] = $companyName;
        }

        unset(
            $data['full_name'],
            $data['company_name'],
        );

        return $data;
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
