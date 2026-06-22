<?php

namespace App\Observers;

use App\Models\Program;
use App\Models\ProgramSnapshot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProgramObserver
{
    /**
     * Handle the Program "created" event.
     */
    public function created(Program $program): void
    {
        $program->programStatusLogs()->create([
            'is_active' => !is_null($program->is_active) ? $program->is_active : 1,
            'user_id'   => Auth::check() ? auth()->id() : 1
        ]);

        $startDate = request()->input('valid_since', now()->format('Y-m-d'));
        $program->prices()->create([
            'price'       => $program->price,
            'valid_since' => $startDate,
            'valid_until' => null
        ]);
    }

    /**
     * Handle the Program "updated" event.
     */
    public function updated(Program $program): void
    {

        if ($program->isDirty(['is_active'])) {
            $program->programStatusLogs()->create(['is_active' => $program->is_active, 'user_id'=>auth()->id()]);
        }

        if ($program->isDirty(['price'])) {
            $validSince = request()->input('valid_since', now()->format('Y-m-d'));
            $carbonValidSince = Carbon::parse($validSince);
            $program->prices()->current()
            ->update([
                'valid_until' => $carbonValidSince->copy()->subDay()->format('Y-m-d')
            ]);

            $program->prices()->create([
                'price'       => $program->price,
                'valid_since' => $validSince,
                'valid_until' => null,
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
