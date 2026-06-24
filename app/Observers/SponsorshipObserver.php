<?php

namespace App\Observers;

use App\Models\Sponsorship;

class SponsorshipObserver
{
    /**
     * Handle the Program "created" event.
     */
    public function created(Sponsorship $sponsorship): void
    {
        $sponsorship->paymentConfigs()->create([
            'amount'          => $sponsorship->amount,
            'frequency'       => $sponsorship->frequency,
            'effective_since' => now()->addMonth()->startOfMonth(),
            'effective_until' => null,
            'candidate_id'    => $sponsorship->candidate_id,
            'sponsor_id'      => $sponsorship->sponsor_id ?: null,
        ]);
    }

    /**
     * Handle the Program "updated" event.
     */
    public function updated(Sponsorship $sponsorship): void
    {

        if ($sponsorship->wasChanged(['amount', 'frequency'])) {
            $expirationDate = now()->addMonth()->startOfMonth();
            $sponsorship->paymentConfig()->update(['effective_until' => $expirationDate->copy()->subDay()]);
            $sponsorship->paymentConfigs()->create([
                'amount'          => $sponsorship->amount,
                'frequency'       => $sponsorship->frequency,
                'effective_since' => $expirationDate,
                'effective_until' => null,
                'candidate_id'    => $sponsorship->candidate_id,
                'sponsor_id'      => $sponsorship->sponsor_id ?: null,
            ]);
        }
    }

    /**
     * Handle the Program "deleted" event.
     */
    public function deleted(Sponsorship $sponsorship): void
    {
        //
    }

    /**
     * Handle the Program "restored" event.
     */
    public function restored(Sponsorship $sponsorship): void
    {
        //
    }

    /**
     * Handle the Program "force deleted" event.
     */
    public function forceDeleted(Sponsorship $sponsorship): void
    {
        //
    }
}
