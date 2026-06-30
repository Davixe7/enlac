<?php

namespace App\Observers;

use App\Models\ProcurationActivity;

class ProcurationActivityObserver
{
    /**
     * Handle the ProcurationActivity "created" event.
     */
    public function created(ProcurationActivity $procurationActivity): void
    {
        //
    }

    /**
     * Handle the ProcurationActivity "updated" event.
     */
    public function updated(ProcurationActivity $procurationActivity): void
    {
        if( $procurationActivity->type == 'Obsequio entre Amigos' ){
            for ($i=0; $i < 200; $i++) {
                $tickets[] = [

                ];
            }
            $procurationActivity->raffleTickets()->createMany();
        }
    }

    /**
     * Handle the ProcurationActivity "deleted" event.
     */
    public function deleted(ProcurationActivity $procurationActivity): void
    {
        //
    }

    /**
     * Handle the ProcurationActivity "restored" event.
     */
    public function restored(ProcurationActivity $procurationActivity): void
    {
        //
    }

    /**
     * Handle the ProcurationActivity "force deleted" event.
     */
    public function forceDeleted(ProcurationActivity $procurationActivity): void
    {
        //
    }
}
