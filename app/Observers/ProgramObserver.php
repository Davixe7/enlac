<?php

namespace App\Observers;

use App\Models\Program;

class ProgramObserver
{
    /**
     * Handle the Program "created" event.
     */
    public function created(Program $program): void
    {
        //
    }

    /**
     * Handle the Program "updated" event.
     */
    public function updating(Program $program): void
    {
        if ($program->isDirty(['price', 'is_active'])) {

            $precioAnterior = $program->getOriginal('price');
            $activoAnterior = $program->getOriginal('is_active');
            $fechaUltimaActualizacion = $program->getOriginal('updated_at') ?? $program->created_at;

            // Cerrar el snapshot que estaba vigente hasta este segundo
            $program->snapshots()
                ->whereNull('valid_until')
                ->update(['valid_until' => now()]);

            // Historico
            $program->snapshots()->create([
                'price'       => $precioAnterior,
                'valid_since'  => $fechaUltimaActualizacion,
                'valid_until' => now(),
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
