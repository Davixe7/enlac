<?php

namespace App\Services;

use App\Models\Raffle;
use Illuminate\Support\Facades\DB;

class RaffleService
{
    /**
     * Crea o actualiza una rifa e incrementa/genera sus boletos de forma transaccional.
     */
    public function createOrUpdateRaffle(array $data): Raffle
    {
        return DB::transaction(function () use ($data) {
            $ticketsToAdd = (int) ($data['tickets_to_add'] ?? 0);
            unset($data['tickets_to_add']);

            $existingRaffle = Raffle::where('procuration_activity_id', $data['procuration_activity_id'])->first();

            if ($existingRaffle) {
                unset($data['tickets_count']);
                $existingRaffle->update($data);

                if ($ticketsToAdd > 0) {
                    $this->addTickets($existingRaffle, $ticketsToAdd);
                }

                return $existingRaffle->fresh(['tickets.buyer', 'tickets.seller']);
            }

            $raffle = Raffle::create($data);
            $initialCount = $raffle->tickets_count ?? 0;

            if ($initialCount > 0) {
                $this->generateInitialTickets($raffle, $initialCount);
            }

            return $raffle->fresh(['tickets.buyer', 'tickets.seller']);
        });
    }

    private function addTickets(Raffle $raffle, int $quantity): void
    {
        $lastTicketNumber = $raffle->tickets()->max('number') ?? 0;

        $tickets = [];
        for ($i = 1; $i <= $quantity; $i++) {
            $tickets[] = [
                'number' => $lastTicketNumber + $i,
                'status' => 'available'
            ];
        }

        $raffle->tickets()->createMany($tickets);
        $raffle->increment('tickets_count', $quantity);
    }

    private function generateInitialTickets(Raffle $raffle, int $quantity): void
    {
        $tickets = [];
        for ($i = 0; $i < $quantity; $i++) {
            $tickets[] = [
                'number' => $i + 1,
                'status' => 'available'
            ];
        }

        $raffle->tickets()->createMany($tickets);
    }
}
