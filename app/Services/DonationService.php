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

        // Si viene de un boleto de rifa, vinculamos automáticamente la actividad y el donante
        if (!empty($data['raffle_ticket_id'])) {
            $ticket = \App\Models\RaffleTicket::with(['raffle', 'buyer'])->find($data['raffle_ticket_id']);

            if ($ticket) {
                // Asignar actividad de la rifa si no venía definida
                if ($ticket->raffle && $ticket->raffle->procuration_activity_id) {
                    $data['procuration_activity_id'] = $ticket->raffle->procuration_activity_id;
                }

                // Asignar el donante (buyer) del boleto a la donación
                if (empty($data['donor_id']) && $ticket->donor_id) {
                    $data['donor_id'] = $ticket->donor_id;
                }
            }
        }

        $isPadrinosGenerales = isset($data['activity_type'])
        && ($data['activity_type'] === 'Padrinos Generales' || $data['activity_type'] === ProcurationActivityType::PADRINOS_GENERALES->value);

        // Si sigue sin procuration_activity_id, se le asigna la actividad por defecto
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
