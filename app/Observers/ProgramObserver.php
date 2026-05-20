<?php

namespace App\Observers;

use App\Models\Program;
use App\Models\ProgramSnapshot;
use Carbon\Carbon;

class ProgramObserver
{
    /**
     * Handle the Program "created" event.
     */
    public function created(Program $program): void
    {
        $startDate = request()->input('effective_date', now()->format('Y-m-d'));

        $program->snapshots()->create([
            'price' => $program->price,
            'valid_since' => $startDate,
            'valid_until' => null, // Vigente indefinidamente hasta que cambie
        ]);
    }

    /**
     * Handle the Program "updated" event.
     */
    public function updated(Program $program): void
    {

        if ($program->isDirty(['price', 'is_active'])) {

            // 1. Capturamos la fecha en la que el usuario quiere que el NUEVO precio sea vigente
            // Si no viene en el request, asumimos que es inmediatamente (hoy)
            $fechaVigenciaNueva = request()->input('valid_since', now()->format('Y-m-d'));

            // Convertimos a Carbon para restar 1 día de forma segura si es necesario
            $carbonVigenciaNueva = Carbon::parse($fechaVigenciaNueva);

            // 2. Cerramos el snapshot actual (el que tiene valid_until en NULL)
            // Su vigencia termina justo un día antes de que empiece el nuevo precio
            $program->snapshots()
                ->whereNull('valid_until')
                ->update([
                    'valid_until' => $carbonVigenciaNueva->copy()->subDay()->format('Y-m-d')
                ]);

            // 3. Creamos el NUEVO snapshot con el NUEVO precio que se mantendrá vigente de forma indefinida
            $program->snapshots()->create([
                'price'       => $program->price, // El nuevo precio modificado
                'valid_since' => $fechaVigenciaNueva, // Inicia en la fecha que le indicamos
                'valid_until' => null, // Vigente indefinidamente hacia el futuro
            ]);
        }
    }

    /**
     * Handle the Program "deleted" event.
     */
    public function deleted(Program $program): void
    {
        //
    }

    /**
     * Handle the Program "restored" event.
     */
    public function restored(Program $program): void
    {
        //
    }

    /**
     * Handle the Program "force deleted" event.
     */
    public function forceDeleted(Program $program): void
    {
        //
    }
}
